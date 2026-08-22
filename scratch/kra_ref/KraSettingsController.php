<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KraSetting;
use App\Services\NdmlKraService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class KraSettingsController extends Controller
{
    /**
     * Display the KRA Settings page.
     */
    public function index()
    {
        $settings = KraSetting::getSettings();
        return view('admin.api.kra-settings', compact('settings'));
    }

    /**
     * Update the KRA settings in the database.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'ndml_user_id' => 'nullable|string|max:100',
            'ndml_password' => 'nullable|string|max:255',
            'ndml_bp_id' => 'nullable|string|max:100',
            'ndml_passkey' => 'nullable|string|max:100',
            'ndml_encryption_key' => 'nullable|string|max:100',
            'ndml_uat_mode' => 'required|boolean',
            'sftp_host' => 'nullable|string|max:255',
            'sftp_port' => 'required|integer|min:1|max:65535',
            'sftp_username' => 'nullable|string|max:100',
            'sftp_password' => 'nullable|string|max:255',
            'auto_upload_on_approval' => 'required|boolean',
        ]);

        try {
            $settings = KraSetting::first();
            if ($settings) {
                $settings->update($validated);
            } else {
                KraSetting::create($validated);
            }

            return redirect()->back()->with('success', 'KRA configuration updated successfully.');
        } catch (Exception $e) {
            Log::error('KRA settings update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update KRA configuration: ' . $e->getMessage());
        }
    }

    /**
     * Run a live authentication test with current credentials.
     */
    public function testSoap(Request $request): JsonResponse
    {
        try {
            // Instantiate service (it will dynamically pull either newly saved DB credentials or falls back)
            $service = new NdmlKraService();
            
            // Run registration password hashing test
            $regEncrypted = $service->getRegistrationEncryptedPassword();
            
            // Run inquiry passcode hashing test
            $inqEncrypted = $service->getInquiryEncryptedPassword();

            return response()->json([
                'status' => 'success',
                'message' => 'Credentials verified successfully! SOAP cURL connection established, and passwords were encrypted without error.',
                'data' => [
                    'registration_hash' => substr($regEncrypted, 0, 15) . '...',
                    'inquiry_hash' => substr($inqEncrypted, 0, 15) . '...'
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('KRA SOAP connection test failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'SOAP connection test failed. Please verify credentials or ensure your server IP is whitelisted by NDML. Details: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Manually trigger re-upload/sync for a user to KRA.
     */
    public function reupload(string $userId)
    {
        try {
            \App\Jobs\UploadKraDocumentsToSftp::dispatchSync($userId);
            return redirect()->back()->with('success', 'KRA documents generated and uploaded successfully.');
        } catch (\Throwable $e) {
            Log::error('KRA Manual Reupload failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'KRA Reupload failed: ' . $e->getMessage());
        }
    }

    /**
     * Create a dummy user and trigger a test KRA upload (demographics + SFTP PDF).
     */
    public function testUpload(Request $request)
    {
        try {
            // Generate a random test PAN to avoid database unique constraints
            $testPan = 'KRATS' . rand(1000, 9999) . 'T';

            // 1. Create or retrieve test user
            $testUser = \App\Models\User::updateOrCreate(
                ['email' => 'kra.test.customer@metawish.com'],
                [
                    'name' => 'KRA TEST CUSTOMER',
                    'pan_card' => $testPan,
                    'pan_card_name' => 'KRA TEST CUSTOMER',
                    'father_name' => 'FATHER TEST NAME',
                    'phone' => '9999999999',
                    'gender' => 'male',
                    'marital_status' => 'single',
                    'address' => '123 Test Street, Compliance Sector, Mumbai, Maharashtra',
                    'city' => 'Mumbai',
                    'state' => 'Maharashtra',
                    'pincode' => '400001',
                    'status' => 'active',
                    'dob' => \Carbon\Carbon::parse('1990-01-01'),
                    'password' => bcrypt('password123')
                ]
            );

            // 2. Create or retrieve approved KYC record
            $kyc = \App\Models\KycVerification::updateOrCreate(
                ['user_id' => $testUser->id],
                [
                    'digio_document_id' => 'KID' . time() . 'TEST',
                    'customer_name' => 'KRA TEST CUSTOMER',
                    'customer_mobile' => '9999999999',
                    'customer_email' => 'kra.test.customer@metawish.com',
                    'reference_id' => 'REF' . time(),
                    'transaction_id' => 'TXN' . time(),
                    'status' => 'approved',
                    'kyc_completed_at' => now(),
                    'kyc_details' => [
                        'type' => 'test-kyc',
                        'signature_local_path' => null,
                        'selfie_local_path' => null
                    ],
                    'aadhaar_details' => [
                        'name' => 'KRA TEST CUSTOMER',
                        'gender' => 'male',
                        'address' => '123 Test Street, Mumbai'
                    ]
                ]
            );

            // 3. Attach dummy signature and selfie images if not already present
            if (!$kyc->hasMedia('kyc_signature')) {
                // Generate a tiny white square image as dummy signature
                $dummyImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
                $tempSig = tempnam(sys_get_temp_dir(), 'sig_');
                file_put_contents($tempSig, $dummyImage);
                $kyc->addMedia($tempSig)
                    ->usingFileName('signature_test.jpg')
                    ->usingName('KYC Signature')
                    ->toMediaCollection('kyc_signature');
            }

            if (!$kyc->hasMedia('kyc_selfie')) {
                $dummyImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
                $tempSelfie = tempnam(sys_get_temp_dir(), 'selfie_');
                file_put_contents($tempSelfie, $dummyImage);
                $kyc->addMedia($tempSelfie)
                    ->usingFileName('selfie_test.jpg')
                    ->usingName('KYC Selfie')
                    ->toMediaCollection('kyc_selfie');
            }

            // 4. Force execute the sync job synchronously
            \App\Jobs\UploadKraDocumentsToSftp::dispatchSync($testUser->id);

            // Fetch the updated KYC record to see the callback_message
            $kyc->refresh();

            if ($kyc->callback_status === 'synced_to_kra') {
                return redirect()->back()->with('success', 'UAT Test Upload Successful! SOAP call succeeded and PDF uploaded to SFTP. Details: ' . $kyc->callback_message);
            } else {
                return redirect()->back()->with('error', 'UAT Test Upload Failed: ' . $kyc->callback_message);
            }
        } catch (\Throwable $e) {
            Log::error('KRA Test Upload Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'KRA Test Upload Exception: ' . $e->getMessage());
        }
    }
}
