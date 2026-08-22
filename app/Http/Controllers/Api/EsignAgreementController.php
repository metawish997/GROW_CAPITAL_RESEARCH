<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EsignAgreement;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Models\AppSetting;

class EsignAgreementController extends Controller
{
    /**
     * Check if the authenticated user has signed the agreement.
     */
    public function status(Request $request)
    {
        $user = $request->user();
        Log::info('[E-SIGN] Checking status for user', ['user_id' => $user->id]);

        $agreement = $user->esignAgreement;

        if (!$agreement) {
            Log::info('[E-SIGN] Agreement not found', ['user_id' => $user->id]);
            return response()->json([
                'status' => 'success',
                'is_signed' => false,
            ]);
        }

        // If it's already completely signed
        if ($agreement->is_signed || $agreement->status === 'signed') {
            Log::info('[E-SIGN] Agreement already signed', ['user_id' => $user->id]);
            $kyc = \App\Models\KycVerification::where('user_id', $user->id)->where('status', 'approved')->latest()->first();
            return response()->json([
                'status' => 'success',
                'is_signed' => true,
                'signed_at' => $agreement->signed_at,
                'document_url' => url('/api/user/esign/download'),
                'kyc' => $kyc ? [
                    'customer_name' => $kyc->customer_name,
                    'customer_mobile' => $kyc->customer_mobile,
                    'kyc_completed_at' => $kyc->kyc_completed_at ? $kyc->kyc_completed_at->format('Y-m-d H:i:s') : ($kyc->updated_at ? $kyc->updated_at->format('Y-m-d H:i:s') : null)
                ] : null
            ]);
        }

        // If pending, check Digio status
        if ($agreement->status === 'pending' && $agreement->digio_document_id) {
            $digio = AppSetting::getGroup('digio');
            $clientId = $digio['client_id'] ?? null;
            $clientSecret = $digio['client_secret'] ?? null;
            $baseUrl = rtrim($digio['base_url'] ?? '', '/');

            if ($clientId && $clientSecret && $baseUrl) {
                try {
                    Log::info('[E-SIGN] Polling Digio for status', ['doc_id' => $agreement->digio_document_id]);
                    $response = Http::withBasicAuth($clientId, $clientSecret)
                        ->get("{$baseUrl}/v2/client/document/{$agreement->digio_document_id}");

                    if ($response->successful()) {
                        $digioData = $response->json();
                        
                        // If signed, download the signed PDF
                        if (isset($digioData['agreement_status']) && $digioData['agreement_status'] === 'completed') {
                            Log::info('[E-SIGN] Digio completed. Downloading signed PDF...');
                            $downloadUrl = "{$baseUrl}/v2/client/document/download?document_id={$agreement->digio_document_id}";
                            
                            $pdfRes = Http::withBasicAuth($clientId, $clientSecret)->get($downloadUrl);
                            
                            if ($pdfRes->successful()) {
                                $fileName = 'agreements/signed_' . $user->id . '_' . time() . '.pdf';
                                Storage::disk('local')->put($fileName, $pdfRes->body());
                                
                                $agreement->update([
                                    'document_path' => $fileName,
                                    'status' => 'signed',
                                    'is_signed' => true,
                                    'signed_at' => now(),
                                ]);

                                Log::info('[E-SIGN] Final signed PDF saved', ['path' => $fileName]);

                                // Send agreement emails to customer and admin
                                self::sendSignedAgreementEmails($user, $agreement);

                                $kyc = \App\Models\KycVerification::where('user_id', $user->id)->where('status', 'approved')->latest()->first();
                                return response()->json([
                                    'status' => 'success',
                                    'is_signed' => true,
                                    'signed_at' => $agreement->signed_at,
                                    'document_url' => url('/api/user/esign/download'),
                                    'kyc' => $kyc ? [
                                        'customer_name' => $kyc->customer_name,
                                        'customer_mobile' => $kyc->customer_mobile,
                                        'kyc_completed_at' => $kyc->kyc_completed_at ? $kyc->kyc_completed_at->format('Y-m-d H:i:s') : ($kyc->updated_at ? $kyc->updated_at->format('Y-m-d H:i:s') : null)
                                    ] : null
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('[E-SIGN] Polling Digio failed: ' . $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'is_signed' => false,
                'esign_url' => $agreement->esign_url,
                'message' => 'E-sign pending. Please complete the signature.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'is_signed' => false,
        ]);
    }

    /**
     * Preview the dummy PDF before signing.
     */
    public function preview(Request $request)
    {
        $user = $request->user();

        $data = [
            'user_name' => $user->name ?? 'User',
            'user_mobile' => $user->mobile ?? '',
            'ip_address' => $request->ip(),
            'date' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = Pdf::loadView('pdf.agreement', $data);
        return response()->json([
            'status' => 'success',
            'pdf_base64' => base64_encode($pdf->output())
        ]);
    }

    /**
     * Generate the dummy PDF and upload to Digio.
     */
    public function sign(Request $request)
    {
        $user = $request->user();
        Log::info('[E-SIGN] Initiating sign process', ['user_id' => $user->id]);

        $agreement = $user->esignAgreement;

        // Check if already signed
        if ($agreement && ($agreement->is_signed || $agreement->status === 'signed')) {
            Log::warning('[E-SIGN] Sign attempt failed: already signed', ['user_id' => $user->id]);
            return response()->json([
                'status' => 'error',
                'message' => 'Agreement has already been signed.',
            ], 400);
        }

        // If pending, return the existing URL
        if ($agreement && $agreement->status === 'pending' && $agreement->esign_url) {
            return response()->json([
                'status' => 'success',
                'message' => 'Agreement signature is pending.',
                'esign_url' => $agreement->esign_url,
            ]);
        }

        // Fetch Digio Credentials
        $digio = AppSetting::getGroup('digio');
        $clientId = $digio['client_id'] ?? null;
        $clientSecret = $digio['client_secret'] ?? null;
        $baseUrl = rtrim($digio['base_url'] ?? '', '/');

        // Generate PDF
        $data = [
            'user_name' => $user->name ?? 'User',
            'user_mobile' => $user->mobile ?? '',
            'ip_address' => $request->ip(),
            'date' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = Pdf::loadView('pdf.agreement', $data);
        $fileName = 'agreements/draft_' . $user->id . '_' . time() . '.pdf';
        $pdfContent = $pdf->output();

        // Upload to Digio if credentials exist
        $digioDocId = null;
        $esignUrl = null;
        
        if ($clientId && $clientSecret && $baseUrl) {
            $signerPhone = $user->mobile ?? '';

            // Extract KYC details for signature verification
            $kyc = \App\Models\KycVerification::where('user_id', $user->id)->where('status', 'approved')->latest()->first();
            $verificationRules = [];
            
            if ($kyc && $kyc->raw_response) {
                $rawResponse = is_string($kyc->raw_response) ? json_decode($kyc->raw_response, true) : $kyc->raw_response;
                $aadhaarDetails = $rawResponse['actions'][0]['details']['aadhaar'] ?? [];
                
                $rawAadhaar = $aadhaarDetails['id_number'] ?? null;
                $last4Aadhaar = $rawAadhaar ? substr($rawAadhaar, -4) : null;
                $kycName = $aadhaarDetails['name'] ?? null;
                $kycGender = $aadhaarDetails['gender'] ?? null;
                $kycDob = $aadhaarDetails['dob'] ?? null;
                $kycYob = $kycDob ? substr($kycDob, -4) : null;
                
                $andConditions = [];
                if ($kycGender) $andConditions[] = ['field' => 'gender', 'match_type' => 'exact', 'value' => $kycGender];
                if ($last4Aadhaar) $andConditions[] = ['field' => 'aadhaar', 'match_type' => 'exact', 'value' => $last4Aadhaar];
                if (!empty($andConditions)) $verificationRules[] = ['operation' => 'AND', 'conditions' => $andConditions];

                $orConditions = [];
                if ($kycYob) $orConditions[] = ['field' => 'yob', 'match_type' => 'exact', 'value' => $kycYob];
                if ($kycName) $orConditions[] = ['field' => 'name', 'match_type' => 'fuzzy', 'value' => $kycName, 'threshold' => '80'];
                if (!empty($orConditions)) $verificationRules[] = ['operation' => 'OR', 'conditions' => $orConditions];
            }

            $payload = [
                'signers' => [[
                    'identifier' => $signerPhone,
                    'name' => $data['user_name'],
                    'sign_type' => 'aadhaar',
                    'reason' => 'Service Agreement'
                ]],
                'expire_in_days' => 1,
                'display_on_page' => 'all',
                'generate_access_token' => true,
                'notify_signers' => true,
                'file_name' => "Agreement_{$user->id}.pdf",
                'file_data' => base64_encode($pdfContent)
            ];

            if (!empty($verificationRules) && $signerPhone) {
                $payload['signature_verification'] = [
                    $signerPhone => [
                        'abort_on_fail' => true,
                        'max_attempt' => 3,
                        'rules' => $verificationRules
                    ]
                ];
            }

            Log::info('[E-SIGN] Uploading PDF to Digio', ['url' => "{$baseUrl}/v2/client/document/uploadpdf"]);

            try {
                $response = Http::withBasicAuth($clientId, $clientSecret)
                    ->timeout(30)
                    ->post("{$baseUrl}/v2/client/document/uploadpdf", $payload);

                if ($response->successful()) {
                    $digioData = $response->json();
                    $digioDocId = $digioData['id'] ?? null;
                    $esignUrl = $digioData['signing_parties'][0]['sign_url'] ?? null;

                    if (!$esignUrl && isset($digioData['access_token']['id'])) {
                        // Fallback gateway URL
                        $gatewayBase = str_contains($baseUrl, 'ext.digio') ? 'https://ext.digio.in' : 'https://app.digio.in';
                        $esignUrl = "{$gatewayBase}/#/gateway/login/{$digioDocId}/{$digioData['access_token']['id']}/{$signerPhone}?redirect_url=" . urlencode(url('/dashboard'));
                    }
                    Log::info('[E-SIGN] Digio Upload Success', ['doc_id' => $digioDocId]);
                } else {
                    Log::error('[E-SIGN] Digio Upload Failed', ['status' => $response->status(), 'body' => $response->body()]);
                }
            } catch (\Exception $e) {
                Log::error('[E-SIGN] Digio Upload Exception: ' . $e->getMessage());
            }
        }

        // Save Draft PDF to storage
        Storage::disk('local')->put($fileName, $pdfContent);

        // Save to DB
        if ($agreement) {
            $agreement->update([
                'document_path' => $fileName,
                'digio_document_id' => $digioDocId,
                'esign_url' => $esignUrl,
                'status' => $esignUrl ? 'pending' : 'signed', // Fallback to signed if digio fails/not config
                'is_signed' => $esignUrl ? false : true,
                'signed_at' => $esignUrl ? null : now(),
            ]);
        } else {
            $agreement = EsignAgreement::create([
                'user_id' => $user->id,
                'document_path' => $fileName,
                'digio_document_id' => $digioDocId,
                'esign_url' => $esignUrl,
                'status' => $esignUrl ? 'pending' : 'signed',
                'is_signed' => $esignUrl ? false : true,
                'signed_at' => $esignUrl ? null : now(),
                'ip_address' => $request->ip(),
            ]);
        }

        if (!$esignUrl) {
            // Trigger emails for fallback/direct signed completion
            self::sendSignedAgreementEmails($user, $agreement);
        }

        if ($esignUrl) {
            return response()->json([
                'status' => 'success',
                'message' => 'Redirecting to Digio for E-Sign.',
                'esign_url' => $esignUrl,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Agreement signed successfully (Fallback Dummy).',
            'is_signed' => true,
            'document_url' => url('/api/user/esign/download'),
        ]);
    }

    /**
     * Download the signed agreement.
     */
    public function download(Request $request)
    {
        $user = $request->user();
        $agreement = $user->esignAgreement;

        Log::info('[E-SIGN] Download/Preview requested', ['user_id' => $user->id]);

        $disposition = $request->query('download') ? 'attachment' : 'inline';
        $filename = 'GROW_CAPITAL_RESEARCH_Agreement.pdf';

        if ($agreement && $agreement->digio_document_id) {
            $digio = \App\Models\AppSetting::getGroup('digio');
            $clientId = $digio['client_id'] ?? null;
            $clientSecret = $digio['client_secret'] ?? null;
            $baseUrl = rtrim($digio['base_url'] ?? '', '/');

            if ($clientId && $clientSecret && $baseUrl) {
                $downloadUrl = "{$baseUrl}/v2/client/document/download?document_id={$agreement->digio_document_id}";
                try {
                    $pdfRes = Http::withBasicAuth($clientId, $clientSecret)->get($downloadUrl);
                    if ($pdfRes->successful()) {
                        Log::info('[E-SIGN] Fetched signed PDF via Digio document_id');
                        return response($pdfRes->body(), 200, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => "{$disposition}; filename=\"{$filename}\""
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('[E-SIGN] Digio API download failed: ' . $e->getMessage());
                }
            }
        }

        if (!$agreement || !Storage::disk('local')->exists($agreement->document_path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Agreement not found.',
            ], 404);
        }

        Log::info('[E-SIGN] Serving local PDF', ['document_path' => $agreement->document_path]);

        return Storage::disk('local')->response($agreement->document_path, $filename, [
            'Content-Disposition' => "{$disposition}; filename=\"{$filename}\""
        ]);
    }

    /**
     * Send signed agreement PDF to customer and admin.
     */
    public static function sendSignedAgreementEmails(\App\Models\User $user, \App\Models\EsignAgreement $agreement, string $type = 'automatic')
    {
        Log::info('[EMAIL] Initiating signed agreement emails', ['user_id' => $user->id, 'type' => $type]);

        $smtpSettings = AppSetting::getGroup('smtp');
        $fromAddress = 'noreply@growcapitals.com';
        $fromName = 'Grow Capital Research';

        if (!empty($smtpSettings) && !empty($smtpSettings['host'])) {
            config([
                'mail.default'                 => 'smtp',
                'mail.mailers.smtp.host'       => $smtpSettings['host'],
                'mail.mailers.smtp.port'       => $smtpSettings['port'] ?? 587,
                'mail.mailers.smtp.username'   => $smtpSettings['username'] ?? '',
                'mail.mailers.smtp.password'   => $smtpSettings['password'] ?? '',
                'mail.mailers.smtp.encryption' => $smtpSettings['encryption'] ?? 'tls',
                'mail.from.address'            => $smtpSettings['from_address'] ?? 'noreply@growcapitals.com',
                'mail.from.name'               => $smtpSettings['from_name'] ?? 'Grow Capital Research',
            ]);
            $fromAddress = $smtpSettings['from_address'] ?? 'noreply@growcapitals.com';
            $fromName = $smtpSettings['from_name'] ?? 'Grow Capital Research';
            Log::info('[EMAIL] Dynamic SMTP configuration applied');
        }

        // Get the signed PDF content
        $pdfContent = null;
        $fileName = "grow_capital_agreement_{$user->id}.pdf";

        if ($agreement->digio_document_id) {
            $digio = AppSetting::getGroup('digio');
            $clientId = $digio['client_id'] ?? null;
            $clientSecret = $digio['client_secret'] ?? null;
            $baseUrl = rtrim($digio['base_url'] ?? '', '/');

            if ($clientId && $clientSecret && $baseUrl) {
                $downloadUrl = "{$baseUrl}/v2/client/document/download?document_id={$agreement->digio_document_id}";
                try {
                    $pdfRes = Http::withBasicAuth($clientId, $clientSecret)->get($downloadUrl);
                    if ($pdfRes->successful()) {
                        $pdfContent = $pdfRes->body();
                    }
                } catch (\Exception $e) {
                    Log::error('[EMAIL] Failed to fetch signed PDF from Digio: ' . $e->getMessage());
                }
            }
        }

        if (!$pdfContent && $agreement->document_path && Storage::disk('local')->exists($agreement->document_path)) {
            $pdfContent = Storage::disk('local')->get($agreement->document_path);
        }

        if (!$pdfContent) {
            Log::error('[EMAIL] Signed PDF content not found. Email send aborted.');
            return;
        }

        // Clean customer name for greeting
        $customerName = ($user->kyc && $user->kyc->customer_name) ? $user->kyc->customer_name : $user->name;
        $executionDate = $agreement->signed_at ? $agreement->signed_at->format('d-m-Y H:i:s') : now()->format('d-m-Y H:i:s');

        $customerSent = false;
        $customerError = null;

        // 1. Send copy to customer
        try {
            $customerMailData = [
                'customer_name' => $customerName,
                'email'         => $user->email,
                'date'          => $executionDate,
            ];

            // A. Send agreement PDF email
            Mail::send('emails.agreement_customer', $customerMailData, function ($message) use ($user, $pdfContent, $fileName, $fromAddress, $fromName) {
                $message->to($user->email)
                    ->from($fromAddress, $fromName)
                    ->subject('Your Executed Service Agreement - Grow Capital Research')
                    ->attachData($pdfContent, $fileName, [
                        'mime' => 'application/pdf',
                    ]);
            });
            Log::info('[EMAIL] Agreement email sent to customer: ' . $user->email);

            // B. Send welcome onboarding email (no terms PDF needed — already in signed agreement)
            Mail::send('emails.welcome', ['customer_name' => $customerName], function ($message) use ($user, $fromAddress, $fromName) {
                $message->to($user->email)
                    ->from($fromAddress, $fromName)
                    ->subject('Welcome to Grow Capital Research - Subscription Activated');
            });
            Log::info('[EMAIL] Welcome onboarding email with terms PDF sent to customer: ' . $user->email);

            $customerSent = true;
        } catch (\Exception $e) {
            $customerError = $e->getMessage();
            Log::error('[EMAIL] Failed to send customer emails: ' . $customerError);
        }

        // 2. Send copy to admin configured email address
        $adminEmail = $fromAddress; // Fallback to SMTP from address
        // Check if there is an admin user we can copy
        $adminUser = \App\Models\User::where('role', 'admin')->first();
        if ($adminUser && !empty($adminUser->email)) {
            $adminEmail = $adminUser->email;
        }

        $adminSent = false;
        $adminError = null;

        try {
            $adminMailData = [
                'user_id'       => $user->id,
                'customer_name' => $customerName,
                'email'         => $user->email,
                'mobile'        => $user->phone ?? $user->mobile,
                'date'          => $executionDate,
            ];

            Mail::send('emails.agreement_admin', $adminMailData, function ($message) use ($adminEmail, $pdfContent, $fileName, $customerName, $user, $fromAddress, $fromName) {
                $message->to($adminEmail)
                    ->from($fromAddress, $fromName)
                    ->subject("New Executed Agreement: {$customerName} (#{$user->id})")
                    ->attachData($pdfContent, $fileName, [
                        'mime' => 'application/pdf',
                    ]);
            });
            $adminSent = true;
            Log::info('[EMAIL] Agreement email sent to admin: ' . $adminEmail);
        } catch (\Exception $e) {
            $adminError = $e->getMessage();
            Log::error('[EMAIL] Failed to send email to admin: ' . $adminError);
        }

        // Record log to DB
        $logs = $agreement->email_logs ?? [];
        $logs[] = [
            'type'            => $type,
            'sent_at'         => now()->toIso8601String(),
            'customer_email'  => $user->email,
            'customer_status' => $customerSent ? 'success' : 'failed',
            'customer_error'  => $customerError,
            'admin_email'     => $adminEmail,
            'admin_status'    => $adminSent ? 'success' : 'failed',
            'admin_error'     => $adminError,
        ];
        $agreement->update(['email_logs' => $logs]);
    }
}
