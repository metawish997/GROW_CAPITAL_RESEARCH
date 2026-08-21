<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\KycVerification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KycService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $baseUrl;
    protected string $workflow;

    public function __construct()
    {
        // Load Digio credentials from the DB (app_settings table) — not from .env
        $digio = AppSetting::getGroup('digio');

        $this->clientId     = trim($digio['client_id'] ?? '');
        $this->clientSecret = trim($digio['client_secret'] ?? '');
        $this->baseUrl      = rtrim(trim($digio['base_url'] ?? ''), '/');
        $this->workflow     = trim($digio['workflow'] ?? 'kyc_with_aadhaar');

        // Auto-fix sandbox API URL which requires port 444
        if (str_starts_with($this->baseUrl, 'https://ext.digio.in') && !str_ends_with($this->baseUrl, ':444')) {
            $this->baseUrl = 'https://ext.digio.in:444';
        }

        Log::info('[KYC] KycService initialized', [
            'base_url'    => $this->baseUrl,
            'client_id'   => $this->clientId ? substr($this->clientId, 0, 10) . '...' : 'MISSING',
            'workflow'    => $this->workflow,
            'env'         => $digio['environment'] ?? 'unknown',
        ]);
    }

    /**
     * Check if Digio is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret) && !empty($this->baseUrl);
    }

    /**
     * Initiate a new KYC request for a user.
     * Returns ['success' => bool, 'document_id' => string, 'redirect_url' => string, 'error' => string]
     */
    public function initiateKyc(array $params): array
    {
        if (!$this->isConfigured()) {
            Log::error('[KYC] Digio not configured — credentials missing in DB');
            return ['success' => false, 'error' => 'Digio API not configured. Please set credentials in Admin → API Settings.'];
        }

        $referenceId = 'GC_KYC_' . time() . '_' . $params['user_id'];
        $payload = [
            'template_name'       => $this->workflow,
            'customer_identifier' => $params['mobile'],
            'customer_name'       => $params['name'],
            'reference_id'        => $referenceId,
            'transaction_id'      => $referenceId,
            'notify_customer'     => false,
            'expire_in_days'      => 1,
            'message'             => 'KYC Verification - Grow Capitals Research',
        ];

        $apiUrl = $this->baseUrl . '/client/kyc/v2/request/with_template';

        Log::info('[KYC] Initiating Digio KYC', [
            'user_id' => $params['user_id'],
            'url'     => $apiUrl,
            'payload' => array_merge($payload, ['customer_identifier' => '***']),
        ]);

        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->acceptJson()
                ->timeout(30)
                ->post($apiUrl, $payload);

            if (!$response->successful()) {
                Log::error('[KYC] Digio API Error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return [
                    'success'      => false,
                    'error'        => 'Digio API error: ' . ($response->json('message') ?? $response->body()),
                    'http_status'  => $response->status(),
                ];
            }

            $data       = $response->json();
            $documentId = $data['id'] ?? null;

            if (!$documentId) {
                Log::error('[KYC] Digio returned no document ID', [
                    'raw_response' => $data, 
                    'raw_body'     => $response->body(),
                    'payload'      => $payload
                ]);
                return ['success' => false, 'error' => 'Digio returned no document ID. Check logs.', 'raw' => $data];
            }

            // Build the Digio redirect URL based on environment
            $isSandbox   = str_contains($this->baseUrl, 'ext.digio');
            $redirectBase = $isSandbox
                ? 'https://ext.digio.in/#/gateway/login/'
                : 'https://app.digio.in/#/gateway/login/';

            $redirectUrl = $redirectBase
                . $documentId . '/'
                . time() . '/'
                . $params['mobile']
                . '?redirect_url=' . urlencode(route('kyc.callback'));

            // Save KYC record
            KycVerification::create([
                'user_id'           => $params['user_id'],
                'digio_document_id' => $documentId,
                'customer_name'     => $params['name'],
                'customer_mobile'   => $params['mobile'],
                'customer_email'    => $params['email'] ?? null,
                'reference_id'      => $referenceId,
                'transaction_id'    => $referenceId,
                'workflow'          => $this->workflow,
                'status'            => 'initiated',
                'raw_response'      => $data,
            ]);

            Log::info('[KYC] KYC initiated successfully', ['document_id' => $documentId]);

            return [
                'success'      => true,
                'document_id'  => $documentId,
                'redirect_url' => $redirectUrl,
            ];

        } catch (\Throwable $e) {
            Log::error('[KYC] Exception during initiation: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Fetch latest status from Digio and update DB record.
     */
    public function fetchAndUpdateKycStatus(string $documentId): ?KycVerification
    {
        Log::info('[KYC] Fetching status for: ' . $documentId);

        $url = "{$this->baseUrl}/client/kyc/v2/{$documentId}/response";

        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->timeout(30)
                ->post($url);

            if (!$response->successful()) {
                Log::error('[KYC] Status fetch failed', ['id' => $documentId, 'error' => $response->body()]);
                return null;
            }

            $data          = $response->json();
            $currentStatus = strtolower($data['status'] ?? 'pending');

            // Auto-approve if approval_pending
            if (in_array($currentStatus, ['approval_pending', 'completed'])) {
                $this->approveKycManually($documentId);
                $currentStatus = 'approved';
            }

            // Parse KYC details from action array
            $kycDetails = $this->parseKycDetails($data['actions'] ?? []);

            $kyc = KycVerification::findByDocumentId($documentId);
            if ($kyc) {
                $kyc->update([
                    'status'           => $currentStatus,
                    'kyc_details'      => $kycDetails,
                    'raw_response'     => $data,
                    'kyc_completed_at' => ($currentStatus === 'approved') ? ($kyc->kyc_completed_at ?? now()) : null,
                ]);

                // Sync mobile number to user if approved
                if ($currentStatus === 'approved' && !empty($kyc->customer_mobile)) {
                    $user = $kyc->user;
                    if ($user && empty($user->mobile)) {
                        $user->update(['mobile' => $kyc->customer_mobile]);
                    } elseif ($user && $user->mobile !== $kyc->customer_mobile) {
                        $user->update(['mobile' => $kyc->customer_mobile]);
                    }
                }
            }

            Log::info('[KYC] Status updated', ['document_id' => $documentId, 'status' => $currentStatus]);
            return $kyc;

        } catch (\Exception $e) {
            Log::error('[KYC] fetchAndUpdateKycStatus exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sync user KYC status — skips if already approved or no record.
     */
    public function syncUserKyc($user): ?KycVerification
    {
        $kyc = KycVerification::where('user_id', $user->id)->latest()->first();

        if (!$kyc || !$kyc->digio_document_id) {
            return $kyc;
        }

        if ($kyc->isApproved()) {
            if (!empty($kyc->customer_mobile) && $user->mobile !== $kyc->customer_mobile) {
                $user->update(['mobile' => $kyc->customer_mobile]);
            }
            Log::info('[KYC] Sync skipped - Already approved', ['user_id' => $user->id]);
            return $kyc;
        }

        return $this->fetchAndUpdateKycStatus($kyc->digio_document_id);
    }

    /**
     * Manually approve a KYC document in Digio.
     */
    public function approveKycManually(string $documentId): array
    {
        $url = "{$this->baseUrl}/client/kyc/v2/request/{$documentId}/manage_approval";
        Log::info('[KYC] Manually approving: ' . $documentId);

        return Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->post($url, ['status' => 'approved'])
            ->json();
    }

    /**
     * Parse actions array from Digio response into a clean KYC details array.
     */
    public function parseKycDetails(array $actions): array
    {
        $kycDetails = [
            'aadhaar'        => null,
            'signature_file' => null,
            'selfie_file'    => null,
            'face_match'     => null,
        ];

        foreach ($actions as $action) {
            $type = $action['type'] ?? '';

            // Aadhaar + Face Match
            if ($type === 'digilocker' && isset($action['details']['aadhaar'])) {
                $kycDetails['aadhaar']    = $action['details']['aadhaar'];
                $kycDetails['face_match'] = $action['face_match_result'] ?? null;
            }

            // Signature
            if ($type === 'image' && in_array('signature', $action['rules_data']['strict_validation_types'] ?? [])) {
                $kycDetails['signature_file'] = $action['file_id'] ?? null;
            }

            // Selfie
            if ($type === 'selfie') {
                $kycDetails['selfie_file'] = $action['file_id'] ?? null;
            }
        }

        return $kycDetails;
    }
}
