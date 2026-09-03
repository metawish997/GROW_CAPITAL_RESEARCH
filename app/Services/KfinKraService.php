<?php

namespace App\Services;

use App\Models\KraSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Cache;

class KfinKraService
{
    protected $userId;
    protected $password;
    protected $posCode;
    protected $uatMode;
    protected $baseUrl;

    // SFTP Credentials (if needed as fallback)
    protected $sftpHost;
    protected $sftpPort;
    protected $sftpUsername;
    protected $sftpPassword;

    public function __construct()
    {
        $this->resolveCredentials();
        $this->initializeEndpoints();
    }

    protected function resolveCredentials()
    {
        $settings = null;
        try {
            $settings = KraSetting::first();
        } catch (Exception $e) {
            // Silently fallback to env if DB fails
        }

        if ($settings) {
            $this->userId = $settings->kfin_user_id ?? env('KFIN_USER_ID');
            $this->password = $settings->kfin_password ?? env('KFIN_PASSWORD');
            $this->posCode = $settings->kfin_pos_code ?? env('KFIN_POS_CODE');
            $this->uatMode = $settings->kfin_uat_mode ?? env('KFIN_UAT_MODE', true);

            $this->sftpHost = $settings->sftp_host ?? env('KFIN_SFTP_HOST');
            $this->sftpPort = $settings->sftp_port ?? env('KFIN_SFTP_PORT', 22);
            $this->sftpUsername = $settings->sftp_username ?? env('KFIN_SFTP_USERNAME');
            $this->sftpPassword = $settings->sftp_password ?? env('KFIN_SFTP_PASSWORD');
        } else {
            $this->userId = env('KFIN_USER_ID');
            $this->password = env('KFIN_PASSWORD');
            $this->posCode = env('KFIN_POS_CODE');
            $this->uatMode = env('KFIN_UAT_MODE', true);
            $this->sftpHost = env('KFIN_SFTP_HOST');
            $this->sftpPort = env('KFIN_SFTP_PORT', 22);
            $this->sftpUsername = env('KFIN_SFTP_USERNAME');
            $this->sftpPassword = env('KFIN_SFTP_PASSWORD');
        }
    }

    protected function initializeEndpoints()
    {
        if ($this->uatMode) {
            $this->baseUrl = 'https://uat.kfinkra.in';
        } else {
            $this->baseUrl = 'https://services.kfinkra.in';
        }
    }

    /**
     * Get Client Token (Cached for validity period)
     */
    public function getClientToken()
    {
        if (empty($this->userId) || empty($this->password) || empty($this->posCode)) {
            throw new Exception("KFin credentials are not fully configured.");
        }

        $cacheKey = 'kfin_api_token_' . $this->posCode;
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $url = $this->baseUrl . '/api/inter/getClientToken';
        $payload = [
            "UserId" => $this->userId,
            "Password" => $this->password,
            "Poscode" => $this->posCode,
            "tokenvalidity" => now()->addDays(1)->format('d/m/Y'),
        ];

        Log::info("KFin KRA - Requesting Client Token", ["UserId" => $this->userId, "Poscode" => $this->posCode]);

        $response = Http::timeout(30)->post($url, $payload);

        if ($response->successful() && $response->json('success') === true) {
            $token = $response->json('token');
            // Cache token for 23 hours (less than requested validity to be safe)
            Cache::put($cacheKey, $token, now()->addHours(23));
            return $token;
        }

        Log::error("KFin KRA - getClientToken Failed", [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body()
        ]);
        
        throw new Exception("Failed to retrieve KFin API Token: " . ($response->json('errMessage') ?? 'Unknown error'));
    }

    /**
     * Get KYC Status for a given PAN
     */
    public function getKycStatus(string $pan)
    {
        $token = $this->getClientToken();
        $url = $this->baseUrl . '/api/inter/getKycStatus';
        
        $payload = [
            "pan" => strtoupper($pan),
            "posCode" => $this->posCode
        ];

        $response = Http::withToken($token)
            ->timeout(30)
            ->post($url, $payload);

        if ($response->successful() && $response->json('message') === 'success') {
            return $response->json('data');
        }

        Log::error("KFin KRA - getKycStatus Failed for PAN: $pan", [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body()
        ]);

        return null;
    }

    /**
     * Get KYC Details for a given PAN
     */
    public function getKycDetails(string $pan, string $dob)
    {
        $token = $this->getClientToken();
        $url = $this->baseUrl . '/api/inter/getKycDetails';
        
        // DOB must be DD/MM/YYYY
        $formattedDob = \Carbon\Carbon::parse($dob)->format('d/m/Y');

        $payload = [
            "APP_REQ_ROOT" => [
                "APP_PAN_INQ" => [
                    "APP_PAN_NO" => strtoupper($pan),
                    "APP_DOB_INCORP" => $formattedDob,
                    "APP_IOP_FLAG" => "IE"
                ]
            ]
        ];

        $response = Http::withToken($token)
            ->timeout(30)
            ->post($url, $payload);

        if ($response->successful() && $response->json('message') === 'success') {
            return $response->json('data.resdtls.ROOT.KYC_DATA');
        }

        Log::error("KFin KRA - getKycDetails Failed for PAN: $pan", [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body()
        ]);

        return null;
    }

    /**
     * Check if a PAN has valid KYC.
     * Returns true if status is 07 (Validated).
     */
    public function isKycValid(string $pan): bool
    {
        $statusData = $this->getKycStatus($pan);
        if ($statusData && isset($statusData['resdtls']['APP_PAN_INQ']['APP_STATUS'])) {
            return $statusData['resdtls']['APP_PAN_INQ']['APP_STATUS'] === '07';
        }
        return false;
    }

    /**
     * Upload New KYC Data via API
     */
    public function newKycUpload(array $kycData)
    {
        $token = $this->getClientToken();
        $url = $this->baseUrl . '/api/inter/newKycUpload';

        $payload = [
            "ROOT" => [
                "HEADER" => [
                    "BATCH_NO" => "B" . time(),
                    "BATCH_DATE" => now()->format('d/m/Y')
                ],
                "KYCDATA" => array_merge([
                    "APP_INT_CODE" => "KFIN",
                    "APP_POS_CODE" => $this->posCode,
                    "APP_DATE" => now()->format('d/m/Y'),
                ], $kycData)
            ]
        ];

        $response = Http::withToken($token)
            ->timeout(60)
            ->post($url, $payload);

        if ($response->successful() && $response->json('message') === 'success') {
            return $response->json();
        }

        Log::error("KFin KRA - newKycUpload Failed", [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body()
        ]);

        throw new Exception("KFin KRA Upload Failed: " . ($response->json('errMessage') ?? 'Unknown error'));
    }

    /**
     * Upload KYC PDF/XML Files
     */
    public function fileUpload(string $pan, array $filePaths)
    {
        $token = $this->getClientToken();
        $url = $this->baseUrl . '/api/inter/upload';

        $request = Http::withToken($token)->timeout(120);

        $request->attach('Pan', $pan);
        $request->attach('poscode', $this->posCode);

        foreach ($filePaths as $key => $path) {
            if (file_exists($path)) {
                $filename = basename($path);
                // Attach as an array of files under 'file' parameter (or dynamically if expected differently)
                $request->attach('file', file_get_contents($path), $filename);
            }
        }

        $response = $request->post($url);

        if ($response->successful() && $response->json('message') === 'success') {
            return $response->json();
        }

        Log::error("KFin KRA - fileUpload Failed for PAN: $pan", [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body()
        ]);

        throw new Exception("KFin KRA File Upload Failed: " . ($response->json('errMessage') ?? 'Unknown error'));
    }
}
