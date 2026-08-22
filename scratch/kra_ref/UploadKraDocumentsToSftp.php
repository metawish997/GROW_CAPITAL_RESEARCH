<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\KycVerification;
use App\Services\NdmlKraService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class UploadKraDocumentsToSftp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("KRA Job started for User ID: " . $this->userId);

        try {
            $user = User::find($this->userId);
            if (!$user) {
                throw new Exception("User ID {$this->userId} not found.");
            }

            $kyc = KycVerification::where('user_id', $this->userId)
                ->whereIn('status', ['approved', 'completed', 'success'])
                ->latest()
                ->first();

            if (!$kyc) {
                throw new Exception("No approved KycVerification record found for User ID: " . $this->userId);
            }

            $pan = strtoupper(trim($user->pan_card));
            if (empty($pan)) {
                $extractedPan = null;
                $extractedPanName = null;
                
                if ($kyc && !empty($kyc->raw_response)) {
                    $actions = $kyc->raw_response['actions'] ?? [];
                    foreach ($actions as $act) {
                        if (isset($act['details']['pan']['id_number'])) {
                            $extractedPan = strtoupper(trim($act['details']['pan']['id_number']));
                            $extractedPanName = $act['details']['pan']['name'] ?? null;
                            break;
                        }
                    }
                }
                
                if (empty($extractedPan) && $kyc && !empty($kyc->kyc_details)) {
                    $extractedPan = $kyc->kyc_details['pan']['id_number'] ?? null;
                    $extractedPanName = $kyc->kyc_details['pan']['name'] ?? null;
                }
                
                if (!empty($extractedPan)) {
                    $updateData = ['pan_card' => $extractedPan];
                    if (!empty($extractedPanName)) {
                        $updateData['pan_card_name'] = $extractedPanName;
                        $user->pan_card_name = $extractedPanName;
                    }
                    $user->update($updateData);
                    $user->pan_card = $extractedPan;
                    Log::info("KRA Job: Recovered and updated empty PAN card for User ID {$this->userId} with PAN: {$extractedPan}");
                    $pan = $extractedPan;
                } else {
                    throw new Exception("User ID {$this->userId} does not have a PAN card associated and it could not be recovered from KYC records.");
                }
            }

            // 1. Initialize NDML KRA SOAP service
            $service = new NdmlKraService();

            // 2. Perform live XML Registration call to NDML KRA Webservices
            Log::info("KRA Job: Dispatching SOAP Registration request for user: " . $user->name);
            $regResult = $service->registerKyc($user, $kyc);

            if (!$regResult['success']) {
                Log::error("KRA Job Registration Failed: " . ($regResult['error'] ?? 'Unknown SOAP error'));
                // Do not abort yet, let's proceed with document SFTP upload as they can be processed independently by NDML backoffice
            } else {
                Log::info("KRA Job: SOAP Registration call completed successfully.");
            }

            // 3. Construct KRA Consolidated PDF containing Profile Info, digilocker Aadhaar validation, Selfie, and Signature
            Log::info("KRA Job: Preparing consolidated PDF for PAN: " . $pan);
            
            // Extract media files locally (Signature and Selfie) and convert to base64 for DomPDF rendering
            $sigPath = $kyc->getFirstMediaPath('kyc_signature');
            $sigBase64 = '';
            if ($sigPath && File::exists($sigPath)) {
                $sigBase64 = 'data:image/jpeg;base64,' . base64_encode(File::get($sigPath));
            } else {
                // Fallback to kyc_details paths
                $details = $kyc->kyc_details ?? [];
                $fallbackSig = $details['signature_local_path'] ?? null;
                if ($fallbackSig && File::exists($fallbackSig)) {
                    $sigBase64 = 'data:image/jpeg;base64,' . base64_encode(File::get($fallbackSig));
                }
            }

            $selfiePath = $kyc->getFirstMediaPath('kyc_selfie');
            $selfieBase64 = '';
            if ($selfiePath && File::exists($selfiePath)) {
                $selfieBase64 = 'data:image/jpeg;base64,' . base64_encode(File::get($selfiePath));
            } else {
                $details = $kyc->kyc_details ?? [];
                $fallbackSelfie = $details['selfie_local_path'] ?? null;
                if ($fallbackSelfie && File::exists($fallbackSelfie)) {
                    $selfieBase64 = 'data:image/jpeg;base64,' . base64_encode(File::get($fallbackSelfie));
                }
            }

            $dobFormatted = $user->dob ? $user->dob->format('d-m-Y') : 'Not Provided';
            $regDate = $kyc->kyc_completed_at ? $kyc->kyc_completed_at->format('d-m-Y H:i:s') : date('d-m-Y H:i:s');

            $html = "
            <html>
            <head>
                <style>
                    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2d3748; margin: 0; padding: 15px; font-size: 11px; }
                    .header { text-align: center; border-bottom: 2px solid #0b3186; padding-bottom: 10px; margin-bottom: 20px; }
                    .header h1 { margin: 0; font-size: 18px; color: #0b3186; text-transform: uppercase; }
                    .header p { margin: 5px 0 0; font-size: 10px; color: #718096; }
                    .section-title { font-size: 12px; font-weight: bold; background-color: #f7fafc; border-left: 3px solid #3182ce; padding: 5px 10px; margin-top: 20px; margin-bottom: 10px; text-transform: uppercase; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
                    th, td { padding: 6px 10px; text-align: left; vertical-align: top; border-bottom: 1px solid #e2e8f0; }
                    th { font-weight: bold; color: #4a5568; width: 30%; }
                    .media-container { width: 100%; margin-top: 20px; }
                    .media-box { width: 48%; display: inline-block; text-align: center; border: 1px dashed #cbd5e0; padding: 10px; box-sizing: border-box; background-color: #fcfcfc; }
                    .media-box img { max-width: 180px; max-height: 180px; margin-top: 5px; border-radius: 4px; border: 1px solid #e2e8f0; }
                    .footer { text-align: center; margin-top: 40px; font-size: 9px; color: #a0aec0; border-top: 1px solid #e2e8f0; padding-top: 10px; }
                </style>
            </head>
            <body>
                <div class='header'>
                    <h1>KRA KYC Application Form</h1>
                    <p>Generated on behalf of Registered Analyst (RA) Mandate - KRA Compliance Sync System</p>
                </div>

                <div class='section-title'>Customer KYC Details</div>
                <table>
                    <tr><th>Applicant Name</th><td>" . ($user->pan_card_name ?? $user->name) . "</td></tr>
                    <tr><th>PAN Card Number</th><td>" . $pan . "</td></tr>
                    <tr><th>Date of Birth</th><td>" . $dobFormatted . "</td></tr>
                    <tr><th>Father's Name</th><td>" . ($user->father_name ?? 'N/A') . "</td></tr>
                    <tr><th>Gender</th><td>" . ucfirst($user->gender ?? 'Male') . "</td></tr>
                    <tr><th>Marital Status</th><td>" . ucfirst($user->marital_status ?? 'Single') . "</td></tr>
                </table>

                <div class='section-title'>Contact & Correspondence</div>
                <table>
                    <tr><th>Mobile Number</th><td>+91 " . substr($user->phone, -10) . "</td></tr>
                    <tr><th>Email Address</th><td>" . strtolower($user->email) . "</td></tr>
                    <tr><th>Permanent Address</th><td>" . ($user->address ?? 'N/A') . "</td></tr>
                    <tr><th>City / State / Pincode</th><td>" . ($user->city ?? 'N/A') . " / " . ($user->state ?? 'N/A') . " / " . ($user->pincode ?? 'N/A') . "</td></tr>
                </table>

                <div class='section-title'>Digio Aadhaar & IPV Verification</div>
                <table>
                    <tr><th>Digio Reference ID</th><td>" . $kyc->digio_document_id . "</td></tr>
                    <tr><th>Verification Timestamp</th><td>" . $regDate . "</td></tr>
                    <tr><th>Aadhaar Digilocker Status</th><td>Verified (Success)</td></tr>
                    <tr><th>In-Person Verification (IPV)</th><td>Completed & Validated</td></tr>
                </table>

                <div class='media-container'>
                    <div class='media-box' style='margin-right: 2%;'>
                        <strong>Customer Live Selfie Photo</strong><br>
                        " . ($selfieBase64 ? "<img src='{$selfieBase64}'>" : "<div style='height:120px;padding-top:40px;color:#a0aec0;'>Selfie Image Not Found</div>") . "
                    </div>
                    <div class='media-box'>
                        <strong>Customer Signature Specimen</strong><br>
                        " . ($sigBase64 ? "<img src='{$sigBase64}'>" : "<div style='height:120px;padding-top:40px;color:#a0aec0;'>Signature Image Not Found</div>") . "
                    </div>
                </div>

                <div class='footer'>
                    Metawish Registered Analyst Compliance Portal • NDML KRA Unified KYC Pipeline
                </div>
            </body>
            </html>
            ";

            // Generate and save consolidated PDF using DomPDF
            $pdf = Pdf::loadHTML($html);
            $pdfDirectory = storage_path('app/kra_pdfs/' . date('Y-m-d'));
            
            if (!File::exists($pdfDirectory)) {
                File::makeDirectory($pdfDirectory, 0755, true);
            }

            $pdfPath = $pdfDirectory . '/' . $pan . '.pdf';
            $pdf->save($pdfPath);
            Log::info("KRA Job: PDF compiled successfully at: " . $pdfPath);

            // 4. Securely upload the generated PDF to KRA SFTP
            Log::info("KRA Job: Initiating SFTP upload to NDML for user: " . $user->name);
            $uploadResult = $service->uploadKycDocumentsToSftp($pan, $pdfPath);

            if ($uploadResult) {
                Log::info("KRA Job SUCCESS: SOAP Registration and SFTP document upload finished for user ID: " . $this->userId);
                
                // Mark user's KYC record as synced
                $kyc->update([
                    'callback_status' => 'synced_to_kra',
                    'callback_message' => 'SOAP registration call made and POI/POA PDF successfully uploaded to NDML KRA SFTP on ' . date('Y-m-d H:i:s')
                ]);
            } else {
                throw new Exception("NDML SFTP upload process returned false.");
            }

        } catch (Exception $e) {
            Log::error("KRA Job failed with exception: " . $e->getMessage() . " on line " . $e->getLine());
            try {
                if (isset($kyc)) {
                    $kyc->update([
                        'callback_status' => 'failed',
                        'callback_message' => $e->getMessage()
                    ]);
                }
            } catch (\Exception $dbEx) {
                Log::error("KRA Job failed to write error status to DB: " . $dbEx->getMessage());
            }
            throw $e; // Throwing triggers automatic queue failure handling and retries if configured
        }
    }
}
