<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\KycVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DigioKycController extends Controller
{
    /**
     * Show KYC page
     */
    public function index()
    {
        return view('UserDashboard.settings.kyc_upgrade');
    }
   


        public function testDirectRedirect(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'note' => 'UNAUTHENTICATED'
            ], 401);
        }

        $mobile = $request->phone ?? $user->phone;
        $name   = $request->name ?? $user->name;

        /**
         * 1️⃣ Get latest KYC of user
         */
        $lastKyc = KycVerification::where('user_id', $user->id)
            ->latest()
            ->first();

        /**
         * 🚫 BLOCK if KYC still ACTIVE
         */
        if ($lastKyc && in_array($lastKyc->status, [
            
            'pending',
            'approval_pending',
            'approved'
        ])) {
            return response()->json([
                'success' => false,
                'note' => 'KYC already in progress or completed'
            ]);
        }

        /**
         * 🧹 DELETE OLD FAILED / EXPIRED KYC
         */
        if ($lastKyc && in_array($lastKyc->status, [
            'rejected',
            'failed',
            'expired'
        ])) {
            KycVerification::where('user_id', $user->id)
                ->whereIn('status', ['rejected', 'failed', 'expired'])
                ->delete();
        }

        try {
            /**
             * 2️⃣ LOAD DIGIO CONFIG (SAFE WAY)
             */
            $clientId     = config('services.digio.client_id');
            $clientSecret = config('services.digio.client_secret');
            $baseUrl      = rtrim(config('services.digio.base_url'), '/');
            $workflow     = config('services.digio.workflow');

            if (!$clientId || !$clientSecret || !$baseUrl || !$workflow) {
                Log::error('DIGIO_CONFIG_MISSING', [
                    'client_id' => $clientId,
                    'base_url'  => $baseUrl,
                    'workflow'  => $workflow
                ]);

                return response()->json([
                    'success' => false,
                    'note' => 'Digio configuration missing'
                ], 500);
            }

            /**
             * 3️⃣ CREATE NEW DIGIO KYC
             */
            $referenceId   = 'KYC_' . time();
            $transactionId = $referenceId;

            $payload = [
                'template_name'       => $workflow,
                'customer_identifier' => $mobile,
                'customer_name'       => $name,
                'reference_id'        => $referenceId,
                'transaction_id'      => $transactionId,
                'notify_customer'     => false,
                'expire_in_days'      => 1,
                'message'             => 'KYC Verification'
            ];

            $apiUrl = $baseUrl . '/client/kyc/v2/request/with_template';

            Log::info('DIGIO_KYC_REQUEST', [
                'user_id' => $user->id,
                'url'     => $apiUrl,
                'payload' => $payload
            ]);

            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->acceptJson()
                ->timeout(30)
                ->post($apiUrl, $payload);

            /**
             * 🔴 DIGIO API ERROR
             */
            if (!$response->successful()) {
                Log::error('DIGIO_KYC_API_ERROR', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);

                return response()->json([
                    'success'      => false,
                    'note'         => 'Digio API error',
                    'http_status'  => $response->status(),
                    'digio_error'  => $response->json()
                ]);
            }

            $data = $response->json();

            if (empty($data['id'])) {
                return response()->json([
                    'success' => false,
                    'note' => 'Digio response missing document id',
                    'digio_error' => $data
                ]);
            }

            $documentId = $data['id'];

            /**
             * ❌ BLOCK DGO FLOW
             */
            if (str_starts_with($documentId, 'DGO')) {
                return response()->json([
                    'success' => false,
                    'note' => 'DGO flow not supported'
                ]);
            }

            /**
             * 4️⃣ SAVE KYC ENTRY
             */
            KycVerification::create([
                'user_id'           => $user->id,
                'digio_document_id' => $documentId,
                'customer_name'     => $name,
                'customer_mobile'   => $mobile,
                'customer_email'    => $user->email,
                'reference_id'      => $referenceId,
                'transaction_id'    => $transactionId,
                'status'            => 'initiated',
                'kyc_details'       => json_encode(['type' => 're-kyc']),
                'raw_response'      => json_encode($data)
            ]);

            /**
             * 5️⃣ BUILD REDIRECT URL
             */
            $redirectBase = str_contains($baseUrl, 'ext.digio')
                ? 'https://ext.digio.in/#/gateway/login/'
                : 'https://app.digio.in/#/gateway/login/';

            $redirectUrl = $redirectBase .
                $documentId . '/' .
                time() . '/' .
                $mobile .
                '?redirect_url=' . urlencode(route('digio.callback'));

            return response()->json([
                'success'      => true,
                'document_id'  => $documentId,
                'redirect_url' => $redirectUrl
            ]);

        } catch (\Throwable $e) {
            Log::error('DIGIO_KYC_EXCEPTION', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'note' => 'Server exception',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function callback(Request $request)
    {
        Log::info('================ DIGIO CALLBACK START ================');
        Log::info('Callback Payload', $request->all());

        $digioId = $request->input('digio_doc_id'); // KID...
        $callbackStatus = $request->input('status');

        Log::info('Parsed Callback Data', [
            'digio_id'         => $digioId,
            'callback_status' => $callbackStatus,
        ]);

        if (!$digioId) {
            Log::error('Callback FAILED: digio_doc_id missing');
            return response()->json(['error' => 'Invalid callback'], 400);
        }

        // 1️⃣ DB record check
        $kyc = DB::table('kyc_verifications')
            ->where('digio_document_id', $digioId)
            ->first();

        if (!$kyc) {
            Log::error('KYC NOT FOUND in DB', [
                'digio_document_id' => $digioId
            ]);
            return response()->json(['error' => 'KYC not found'], 404);
        }

        Log::info('KYC Record Found', [
            'db_status' => $kyc->status,
            'user_id'   => $kyc->user_id,
        ]);

        // 2️⃣ Manual approval (sirf approval_pending pe)
       if (in_array($kyc->status, ['initiated', 'approval_pending', 'requested'])) {

            Log::info('Status eligible for manual approval', [
                'status' => $kyc->status,
                'digio_id' => $digioId
            ]);

            $approvalResponse = $this->approveKycManually($digioId);

            Log::info('Manual Approval Finished', [
                'response' => $approvalResponse
            ]);
        } else {
                    Log::info('Manual approval skipped (status != approval_pending)');
                }

        // 3️⃣ FINAL STATUS + DETAILS FETCH
        Log::info('Calling Digio RESPONSE API');
        $this->fetchAndUpdateKycStatus($digioId);

        Log::info('================ DIGIO CALLBACK END =================');

        // ⚠️ browser testing only
        return redirect()->route('kyc.index');
    }


    private function approveKycManually($id)
{
    $clientId     = config('services.digio.client_id');
    $clientSecret = config('services.digio.client_secret');
    $baseUrl      = rtrim(config('services.digio.base_url'), '/');

    if (!$clientId || !$clientSecret || !$baseUrl) {
        Log::error('DIGIO_CONFIG_MISSING_IN_CALLBACK');
        return null;
    }

    $url = $baseUrl . "/client/kyc/v2/request/{$id}/manage_approval";

    return Http::withBasicAuth($clientId, $clientSecret)
        ->post($url, [
            'status' => 'approved'
        ])
        ->json();
}

  
    private function fetchAndUpdateKycStatus($id)
    {
        Log::info('---- STATUS RESPONSE API START ----', ['id' => $id]);

        if (!$id) {
            Log::error('STATUS RESPONSE ABORTED: id is NULL');
            return;
        }

        $url = env('DIGIO_API_BASE_URL') . "/client/kyc/v2/{$id}/response";

        $clientId     = config('services.digio.client_id');
        $clientSecret = config('services.digio.client_secret');
        $baseUrl      = rtrim(config('services.digio.base_url'), '/');

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->timeout(30)
            ->post($baseUrl . "/client/kyc/v2/{$id}/response");


        

        if (!$response->successful()) {
            Log::error('Status Response FAILED', [
                'id' => $id,
                'response' => $response->body()
            ]);
            return;
        }

        $data = $response->json();

        Log::info('Parsed Status Response', $data);

        /**
         * ======================================
         * BUILD FULL KYC DETAILS JSON (ONE COLUMN)
         * ======================================
         */
        $kycDetails = [
            'aadhaar' => null,
            'signature_file' => null,
            'selfie_file' => null,
            'face_match' => null,
        ];

        foreach ($data['actions'] ?? [] as $action) {

            // 📄 Aadhaar + Face match
            if (
                ($action['type'] ?? '') === 'digilocker' &&
                isset($action['details']['aadhaar'])
            ) {
                $kycDetails['aadhaar'] = $action['details']['aadhaar'];
                $kycDetails['face_match'] = $action['face_match_result'] ?? null;
            }

            // ✍️ Signature
            if (
                ($action['type'] ?? '') === 'image' &&
                in_array('signature', $action['rules_data']['strict_validation_types'] ?? [])
            ) {
                $kycDetails['signature_file'] = $action['file_id'] ?? null;
            }

            // 🤳 Selfie
            if (($action['type'] ?? '') === 'selfie') {
                $kycDetails['selfie_file'] = $action['file_id'] ?? null;
            }
        }

        /**
         * ======================================
         * UPDATE ONLY EXISTING DB COLUMNS
         * ======================================
         */
        $kycModel = KycVerification::where('digio_document_id', $id)->first();
        if ($kycModel) {
            $kycModel->update([
                'status'       => $data['status'] ?? null,
                'kyc_details'  => $kycDetails,
                'raw_response' => $data,
                'updated_at'   => now()
            ]);
        }

        Log::info('KYC JSON STORED SUCCESSFULLY', [
            'aadhaar'   => !empty($kycDetails['aadhaar']),
            'signature' => !empty($kycDetails['signature_file']),
            'selfie'    => !empty($kycDetails['selfie_file']),
            'face_match'=> !empty($kycDetails['face_match'])
        ]);

        Log::info('---- STATUS RESPONSE API END ----');
    }

    public function digioCallback(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        /**
         * 1️⃣ FIND USER'S ACTIVE KYC
         */
        $kyc = KycVerification::where('user_id', $user->id)
            ->whereIn('status', ['initiated', 'approval_pending', 'pending', 'processing'])
            ->latest()
            ->first();

        if (!$kyc) {
            return redirect()
                ->route('user.dashboard')
                ->with('error', 'No active KYC request found.');
        }

        $documentId = $kyc->digio_document_id;

        try {

            /**
             * 2️⃣ CALL DIGIO RESPONSE API
             */
            $apiUrl = env('DIGIO_API_BASE_URL') . "/client/kyc/v2/{$documentId}/response";

            $response = Http::withBasicAuth(
                env('DIGIO_CLIENT_ID'),
                env('DIGIO_CLIENT_SECRET')
            )
            ->timeout(30)
            ->post($apiUrl);

            if (!$response->successful()) {

                Log::error('Digio verification failed', [
                    'document_id' => $documentId,
                    'response' => $response->json()
                ]);

                return redirect()
                    ->route('user.dashboard')
                    ->with('error', 'Unable to verify KYC right now.');
            }

            $digioData = $response->json();
            $status = strtolower($digioData['status'] ?? 'pending');

            /**
             * 3️⃣ BUILD SINGLE KYC JSON OBJECT
             */
            $kycDetails = [
                'aadhaar' => null,
                'signature_file' => null,
                'selfie_file' => null,
                'face_match' => null,
            ];

            foreach ($digioData['actions'] ?? [] as $action) {

                // 📄 Aadhaar + face match
                if (
                    ($action['type'] ?? '') === 'digilocker' &&
                    isset($action['details']['aadhaar'])
                ) {
                    $kycDetails['aadhaar'] = $action['details']['aadhaar'];
                    $kycDetails['face_match'] = $action['face_match_result'] ?? null;
                }

                // ✍️ Signature
                if (
                    ($action['type'] ?? '') === 'image' &&
                    in_array('signature', $action['rules_data']['strict_validation_types'] ?? [])
                ) {
                    $kycDetails['signature_file'] = $action['file_id'] ?? null;
                }

                // 🤳 Selfie
                if (($action['type'] ?? '') === 'selfie') {
                    $kycDetails['selfie_file'] = $action['file_id'] ?? null;
                }
            }

            /**
             * 4️⃣ UPDATE ONLY REAL COLUMNS
             */
            $kyc->update([
                'status'       => $status,
                'kyc_details'  => json_encode($kycDetails),
                'raw_response' => json_encode($digioData),
                'kyc_completed_at' =>
                    in_array($status, ['approved', 'completed', 'success']) ? now() : null,
                'kyc_expires_at' =>
                    isset($digioData['expire_in_days'])
                        ? now()->addDays($digioData['expire_in_days'])
                        : $kyc->kyc_expires_at,
            ]);

            /**
             * 5️⃣ USER MESSAGE
             */
            if (in_array($status, ['approved', 'completed', 'success'])) {
                return redirect()
                    ->route('user.dashboard')
                    ->with('success', '✅ KYC verified successfully');
            }

            if (in_array($status, ['approval_pending', 'pending', 'processing'])) {
                return redirect()
                    ->route('user.dashboard')
                    ->with('info', '⏳ KYC submitted and under review');
            }

            return redirect()
                ->route('user.dashboard')
                ->with('error', '❌ KYC verification failed');

        } catch (\Exception $e) {

            Log::error('Digio callback exception', [
                'document_id' => $documentId,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('user.dashboard')
                ->with('error', 'KYC processing error. Please retry.');
        }
    }
 
   

    public function checkKycStatusDirect(Request $request)
    {
        $user = Auth::user();
        Log::info('KYC Status Check Started', ['user_id' => $user->id ?? 'Guest']);

        if (!$user) {
            return response()->json(['success' => false, 'error' => 'UNAUTHENTICATED'], 401);
        }

        $kyc = KycVerification::where('user_id', $user->id)->latest()->first();

        if (!$kyc) {
            Log::warning('KYC Status Check: No record found', ['user_id' => $user->id]);
            return response()->json([
                'success' => true,
                'kyc_status' => 'none',
                'message' => 'No KYC found'
            ]);
        }

        /** ✅ Load Digio config safely */
        $clientId     = config('services.digio.client_id');
        $clientSecret = config('services.digio.client_secret');
        $baseUrl      = rtrim(config('services.digio.base_url'), '/');

        if (!$clientId || !$clientSecret || !$baseUrl) {
            Log::error('DIGIO_CONFIG_MISSING_IN_STATUS_CHECK');
            return response()->json(['success' => false, 'error' => 'DIGIO_CONFIG_MISSING'], 500);
        }

        $documentId = $kyc->digio_document_id;
        $apiUrl = "{$baseUrl}/client/kyc/v2/{$documentId}/response";

        try {
            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->timeout(30)
                ->post($apiUrl);

            if (!$response->successful()) {
                Log::error('Digio API Response Error', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);

                return response()->json([
                    'success' => false,
                    'error'   => 'DIGIO_API_ERROR'
                ], $response->status());
            }

            $digioData = $response->json();
            $status = strtolower($digioData['status'] ?? 'pending');

            Log::info('Digio Status Received', [
                'status' => $status,
                'doc_id' => $documentId
            ]);

            /** Build unified KYC JSON */
            $kycDetails = [
                'aadhaar'               => null,
                'signature_file'        => null,
                'signature_local_path'  => null,
                'selfie_file'           => null,
                'selfie_local_path'     => null,
                'face_match'            => null,
            ];

            foreach ($digioData['actions'] ?? [] as $action) {

                // Aadhaar + Face match
                if (
                    ($action['type'] ?? '') === 'digilocker' &&
                    isset($action['details']['aadhaar'])
                ) {
                    $kycDetails['aadhaar'] = $action['details']['aadhaar'];
                    $kycDetails['face_match'] = $action['face_match_result'] ?? null;
                }

                // Signature
                if (
                    ($action['type'] ?? '') === 'image' &&
                    in_array('signature', $action['rules_data']['strict_validation_types'] ?? [])
                ) {
                    $fileId = $action['file_id'] ?? null;
                    $kycDetails['signature_file'] = $fileId;

                    if ($fileId) {
                        $kycDetails['signature_local_path'] =
                            $this->storeMediaLocally($fileId, 'signature', $user->id, $kyc);
                    }
                }

                // Selfie
                if (($action['type'] ?? '') === 'selfie') {
                    $fileId = $action['file_id'] ?? null;
                    $kycDetails['selfie_file'] = $fileId;

                    if ($fileId) {
                        $kycDetails['selfie_local_path'] =
                            $this->storeMediaLocally($fileId, 'selfie', $user->id, $kyc);
                    }
                }
            }

            $kyc->update([
                'status'           => $status,
                'kyc_details'      => $kycDetails,
                'raw_response'     => $digioData,
                'kyc_completed_at' => in_array($status, ['approved', 'completed', 'success'])
                    ? now()
                    : null,
            ]);

            Log::info('KYC Record Updated Successfully', ['user_id' => $user->id]);

            return response()->json([
                'success'       => true,
                'kyc_status'    => $status,
                'has_signature' => !empty($kycDetails['signature_local_path']),
                'has_selfie'    => !empty($kycDetails['selfie_local_path']),
            ]);

        } catch (\Throwable $e) {
            Log::error('KYC Status Exception', ['msg' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'INTERNAL_ERROR'], 500);
        }
    }


    private function storeMediaLocally($requestId, $type, $userId, $kycModel)
    {
        Log::info('Requesting Media from Digio', [
            'type' => $type,
            'request_id' => $requestId
        ]);

        /** ✅ Load config (NOT env) */
        $clientId     = config('services.digio.client_id');
        $clientSecret = config('services.digio.client_secret');
        $baseUrl      = rtrim(config('services.digio.base_url'), '/');

        if (!$clientId || !$clientSecret || !$baseUrl) {
            Log::error('DIGIO_CONFIG_MISSING_IN_MEDIA_FETCH');
            return null;
        }

        try {
            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->get("{$baseUrl}/client/kyc/v2/media/{$requestId}", [
                    'doc_type' => $type,
                    'base64'   => 'true',
                ]);

            if (!$response->successful()) {
                Log::error('Failed to fetch media from Digio', [
                    'type' => $type,
                    'response' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            if (empty($data['file_in_base64'])) {
                Log::error('Digio response missing file_in_base64', ['type' => $type]);
                return null;
            }

            /** Decode image */
            $imageBinary = base64_decode($data['file_in_base64']);

            $tempFile = tempnam(sys_get_temp_dir(), 'kyc_media_');
            file_put_contents($tempFile, $imageBinary);

            /** Spatie Media Library */
            $collection = "kyc_{$type}";

            $kycModel->clearMediaCollection($collection);

            $media = $kycModel->addMedia($tempFile)
                ->usingFileName("{$type}_{$requestId}.jpg")
                ->usingName('KYC ' . ucfirst($type))
                ->toMediaCollection($collection);

            if ($media) {
                Log::info('Media stored successfully', ['path' => $media->getPath()]);
                return $media->getPath();
            }

        } catch (\Throwable $e) {
            Log::error('Exception while storing Digio media', [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }


    public function viewDigioFile(string $requestId, string $type)
    {
        if (!in_array($type, ['signature', 'selfie'])) {
            abort(400, 'Invalid media type');
        }

        // Use the specific KID you mentioned if it's not already in your .env
        $clientId = env('DIGIO_CLIENT_ID', 'KID2602061826425736H6K1KODPNGTG1');
        $clientSecret = env('DIGIO_CLIENT_SECRET');
        $baseUrl = rtrim(env('DIGIO_API_BASE_URL'), '/');

        $fullUrl = "{$baseUrl}/client/kyc/v2/media/{$requestId}";
        
        $queryParams = [
            'doc_type' => $type,
            'xml'      => 'true',
            'base64'   => 'true',
        ];

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->get($fullUrl, $queryParams);

        if (!$response->successful()) {
            \Log::error('DIGIO API FAILURE', ['body' => $response->body()]);
            abort(404);
        }

        $data = $response->json();

        // FIX: Digio uses 'file_in_base64' (as seen in your log), NOT 'file_base64'
        if (empty($data['file_in_base64'])) {
            \Log::error("Key 'file_in_base64' missing in response", ['data' => $data]);
            abort(404, 'No image data');
        }

        // Decode the base64 string
        $imageBinary = base64_decode($data['file_in_base64']);

        return response($imageBinary, 200, [
            'Content-Type'        => 'image/jpeg',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
            'Content-Disposition' => 'inline',
        ]);
    }


}
