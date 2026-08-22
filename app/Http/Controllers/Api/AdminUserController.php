<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\KycVerification;

class AdminUserController extends Controller
{
    /**
     * Get a paginated list of all users.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        // Optional search by name, email, or mobile
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Exclude admins (assuming admins are handled differently or identified, but here we'll just list regular users)
        // If there's an 'is_admin' flag we could check it. For now, list all.
        $users = $query->with(['kyc', 'esignAgreement'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'users'   => $users
        ]);
    }

    /**
     * Create a new customer profile and initiate their Digio KYC.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'mobile'   => 'required|string|min:10|max:15|unique:users,mobile',
            'pan_card' => 'nullable|string|max:15',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'mobile'   => $request->mobile,
            'pan_card' => $request->pan_card ? strtoupper(trim($request->pan_card)) : null,
            'password' => bcrypt(\Illuminate\Support\Str::random(16)),
            'role'     => 'user',
        ]);

        // Automatically initiate Digio KYC for this new customer
        $digioUrl = null;
        try {
            $kycService = new \App\Services\KycService();
            $result = $kycService->initiateKyc([
                'user_id' => $user->id,
                'name'    => $user->name,
                'mobile'  => $user->mobile,
                'email'   => $user->email,
            ]);

            if ($result['success']) {
                $digioUrl = $result['redirect_url'];
                // Auto accept declaration for admin-created users
                $declarationText = \App\Models\AppSetting::getValue('kyc', 'declaration', "I hereby authorize Grow Capital Research to retrieve my profile and verify my identity details via Digio secure KYC gateway.");
                \App\Models\KycVerification::where('digio_document_id', $result['document_id'])->update([
                    'declaration_accepted' => true,
                    'declaration_accepted_at' => now(),
                    'declaration_text' => $declarationText,
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[ADMIN] Failed to auto-initiate Digio KYC: ' . $e->getMessage());
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Customer profile created successfully.',
            'user'         => $user,
            'digio_kyc_url' => $digioUrl
        ]);
    }

    /**
     * Get details of a single user including their KYC.
     */
    public function show($id)
    {
        \Illuminate\Support\Facades\Log::info('[ADMIN] Fetching user details', ['id' => $id]);
        try {
            $user = User::with(['kyc', 'esignAgreement'])->find($id);
            \Illuminate\Support\Facades\Log::info('[ADMIN] User query successful', ['found' => (bool)$user]);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $token = sha1($user->id . 'grow-capital-media');
            $user->media_upload_url = url("/kyc/upload-media/{$user->id}/{$token}");

            $digioKycUrl = null;
            if ($user->kyc && $user->kyc->digio_document_id && !str_starts_with($user->kyc->digio_document_id, 'MANUAL_') && !in_array($user->kyc->status, ['approved', 'completed'])) {
                $digioSettings = AppSetting::getGroup('digio');
                $baseUrl = $digioSettings['base_url'] ?? '';
                $isSandbox = str_contains($baseUrl, 'ext.digio');
                $redirectBase = $isSandbox
                    ? 'https://ext.digio.in/#/gateway/login/'
                    : 'https://app.digio.in/#/gateway/login/';
                
                $digioKycUrl = $redirectBase
                    . $user->kyc->digio_document_id . '/'
                    . time() . '/'
                    . ($user->kyc->customer_mobile ?? $user->mobile ?? '')
                    . '?redirect_url=' . urlencode(route('kyc.callback'));
            }
            $user->digio_kyc_url = $digioKycUrl;

            return response()->json([
                'success' => true,
                'user'    => $user
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[ADMIN] Error fetching user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download or view the signed agreement for a user as admin.
     */
    public function downloadEsign($id, Request $request)
    {
        $user = User::findOrFail($id);
        $agreement = $user->esignAgreement;

        if (!$agreement) {
            return response()->json(['status' => 'error', 'message' => 'Agreement not found.'], 404);
        }

        $disposition = $request->query('download') ? 'attachment' : 'inline';
        $filename = "GROW_CAPITAL_RESEARCH_Agreement_{$user->id}.pdf";

        if ($agreement->digio_document_id) {
            $digio = \App\Models\AppSetting::getGroup('digio');
            $clientId = $digio['client_id'] ?? null;
            $clientSecret = $digio['client_secret'] ?? null;
            $baseUrl = rtrim($digio['base_url'] ?? '', '/');

            if ($clientId && $clientSecret && $baseUrl) {
                $downloadUrl = "{$baseUrl}/v2/client/document/download?document_id={$agreement->digio_document_id}";
                try {
                    $pdfRes = \Illuminate\Support\Facades\Http::withBasicAuth($clientId, $clientSecret)->get($downloadUrl);
                    if ($pdfRes->successful()) {
                        return response($pdfRes->body(), 200, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => "{$disposition}; filename=\"{$filename}\""
                        ]);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('[ADMIN E-SIGN] Digio API download failed: ' . $e->getMessage());
                }
            }
        }

        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($agreement->document_path)) {
            return response()->json(['status' => 'error', 'message' => 'Local file not found.'], 404);
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->response($agreement->document_path, $filename, [
            'Content-Disposition' => "{$disposition}; filename=\"{$filename}\""
        ]);
    }

    /**
     * Serve selfie or signature media files.
     */
    public function getKycMedia($id, $type)
    {
        $user = User::findOrFail($id);
        $kyc = $user->kyc;
        if (!$kyc || !$kyc->kyc_details) {
            abort(404, 'No KYC details available.');
        }

        $details = $kyc->kyc_details;
        $pathKey = $type . '_local_path';
        $localPath = $details[$pathKey] ?? null;

        if (!$localPath || !file_exists($localPath)) {
            // Attempt to re-download if file_id exists
            $fileIdKey = $type . '_file';
            $fileId = $details[$fileIdKey] ?? null;
            if ($fileId) {
                $service = new \App\Services\KycService();
                $localPath = $service->downloadAndStoreMedia($fileId, $type, $user->id);
            }
        }

        if (!$localPath || !file_exists($localPath)) {
            abort(404, 'Media file not found on disk.');
        }

        return response()->file($localPath, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'no-cache, private'
        ]);
    }

    /**
     * Upload or replace selfie/signature media files.
     */
    public function uploadKycMedia(Request $request, $id, $type)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = User::findOrFail($id);
        $kyc = $user->kyc;
        if (!$kyc) {
            $kyc = \App\Models\KycVerification::create([
                'user_id' => $user->id,
                'digio_document_id' => 'MANUAL_' . time(),
                'customer_name' => $user->name ?? 'Manual Upload',
                'customer_mobile' => $user->mobile ?? '—',
                'reference_id' => 'MANUAL_' . time(),
                'status' => 'approved',
                'kyc_details' => [],
            ]);
        }

        $file = $request->file('file');
        
        // Define local path and store file
        $fileName = "kyc_media/{$user->id}_{$type}_manual_" . time() . ".jpg";
        \Illuminate\Support\Facades\Storage::disk('local')->put($fileName, file_get_contents($file));
        $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($fileName);

        // Update details array in JSON column
        $details = $kyc->kyc_details ?? [];
        $details[$type . '_local_path'] = $fullPath;
        $details[$type . '_file'] = 'manual_' . time();
        
        $kyc->update([
            'kyc_details' => $details
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' uploaded successfully.',
            'path' => $fullPath
        ]);
    }

    /**
     * Get dashboard summary statistics.
     */
    public function dashboardStats(Request $request)
    {
        $totalUsers = User::where('role', 'user')->count();
        
        $kycApproved = \App\Models\KycVerification::where('status', 'approved')->count();
        $kycPending  = \App\Models\KycVerification::whereIn('status', ['pending', 'initiated', 'approval_pending'])->count();
        $kycRejected = \App\Models\KycVerification::whereIn('status', ['failed', 'rejected', 'expired'])->count();
        
        $kraSynced   = \App\Models\KycVerification::where('callback_status', 'synced_to_kra')->count();
        $kraPending  = \App\Models\KycVerification::where('status', 'approved')
            ->where(function($q) {
                $q->whereNull('callback_status')
                  ->orWhere('callback_status', '!=', 'synced_to_kra');
            })->count();

        // Calculate KRA Sync percentage
        $totalApproved = $kycApproved;
        $syncPercentage = $totalApproved > 0 ? round(($kraSynced / $totalApproved) * 100) : 0;

        // Fetch recent customers
        $recentCustomers = User::where('role', 'user')
            ->with(['kyc'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => ($user->kyc && $user->kyc->customer_name) ? $user->kyc->customer_name : $user->name,
                    'email' => $user->email,
                    'kyc_status' => $user->kyc ? $user->kyc->status : 'not_started',
                    'kra_status' => $user->kyc ? $user->kyc->callback_status : null,
                    'created_at' => $user->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'stats' => [
                'total_users'     => $totalUsers,
                'kyc_approved'    => $kycApproved,
                'kyc_pending'     => $kycPending,
                'kyc_rejected'    => $kycRejected,
                'kra_synced'      => $kraSynced,
                'kra_pending'     => $kraPending,
                'sync_percentage' => $syncPercentage,
                'recent_users'    => $recentCustomers,
            ]
        ]);
    }

    /**
     * Update customer profile details.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'father_name'    => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:50',
            'dob'            => 'nullable|string|max:50',
            'address'        => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'pincode'        => 'nullable|string|max:20',
            'pan_card'       => 'nullable|string|max:20',
            'pan_card_name'  => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer profile updated successfully.',
            'user'    => $user
        ]);
    }

    /**
     * Download a single compiled PDF of KRA KYC details + E-Sign Agreement.
     */
    public function downloadConsolidated($id, Request $request)
    {
        $user = User::findOrFail($id);
        $kyc = KycVerification::where('user_id', $id)
            ->whereIn('status', ['approved', 'completed', 'success'])
            ->latest()
            ->first();

        if (!$kyc) {
            return response()->json([
                'success' => false,
                'message' => 'Consolidated download is only available for approved KYC records.'
            ], 400);
        }

        // Render KRA HTML block
        $pan = strtoupper(trim($user->pan_card));
        $details = $kyc->kyc_details ?? [];
        
        $sigPath = $details['signature_local_path'] ?? null;
        $sigBase64 = '';
        if ($sigPath && File::exists($sigPath)) {
            $sigBase64 = 'data:image/jpeg;base64,' . base64_encode(File::get($sigPath));
        }

        $selfiePath = $details['selfie_local_path'] ?? null;
        $selfieBase64 = '';
        if ($selfiePath && File::exists($selfiePath)) {
            $selfieBase64 = 'data:image/jpeg;base64,' . base64_encode(File::get($selfiePath));
        }

        $dobFormatted = $user->dob ? ($user->dob instanceof \Carbon\Carbon ? $user->dob->format('d-m-Y') : date('d-m-Y', strtotime($user->dob))) : 'Not Provided';
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
                
                /* Agreement Styles */
                .text-center { text-align: center; }
                .font-bold { font-weight: bold; }
                .mb-1 { margin-bottom: 5px; }
                .mb-2 { margin-bottom: 10px; }
                .mb-4 { margin-bottom: 15px; }
                .mb-6 { margin-bottom: 25px; }
                .mt-4 { margin-top: 15px; }
                .mt-8 { margin-top: 30px; }
                .uppercase { text-transform: uppercase; }
                .bg-cyan { background-color: #00ffff; padding: 3px; }
                .bg-yellow { background-color: #ffff00; padding: 2px; }
                .text-red { color: #c00000; }
                .text-blue { color: #0000ff; text-decoration: underline; }
                .border-b { border-bottom: 1px solid #000; padding-bottom: 5px; }
                .pl-4 { padding-left: 20px; }
                .box { border: 1px solid #777; padding: 20px; }
                .footer-table { width: 100%; font-size: 13px; }
                .footer-table td { padding: 5px 0; vertical-align: bottom; }
                .line { border-bottom: 1px solid #000; display: inline-block; min-width: 150px; text-align: center; }
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
                <tr><th>Mobile Number</th><td>+91 " . substr($user->phone ?? $user->mobile, -10) . "</td></tr>
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
                Grow Capital Research Compliance Portal • NDML KRA Unified KYC Pipeline
            </div>

            <!-- PAGE BREAK -->
            <div style='page-break-before: always;'></div>
        ";

        // Build data array for agreement template
        $esign = $user->esignAgreement;
        $agreementDate = $esign && $esign->updated_at ? $esign->updated_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s');
        $agreementIp = $esign->ip_address ?? $request->ip();

        $agreementData = [
            'user_name' => $user->pan_card_name ?? $user->name,
            'user_mobile' => $user->phone ?? $user->mobile,
            'ip_address' => $agreementIp,
            'date' => $agreementDate,
        ];

        // Fetch agreement HTML body
        $agreementHtml = view('pdf.agreement', $agreementData)->render();

        // Extract everything inside body tags
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $agreementHtml, $matches)) {
            $html .= $matches[1];
        } else {
            $html .= $agreementHtml;
        }

        $html .= "</body></html>";

        // Generate and stream consolidated PDF
        $pdf = Pdf::loadHTML($html);
        $aadhaarName = ($user->kyc && $user->kyc->customer_name) ? $user->kyc->customer_name : $user->name;
        $cleanedName = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($aadhaarName)));
        
        $signDate = ($esign && $esign->signed_at) ? \Carbon\Carbon::parse($esign->signed_at) : now();
        $dateStr = $signDate->format('d-m-Y');
        $filename = "{$cleanedName}_kyc_agreement_{$dateStr}.pdf";

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Resend signed agreement emails to customer and admin.
     */
    public function resendAgreementEmail($id)
    {
        $user = User::findOrFail($id);
        $agreement = $user->esignAgreement;

        if (!$agreement || !$agreement->is_signed) {
            return response()->json([
                'success' => false,
                'message' => 'Service agreement has not been signed or completed yet.'
            ], 400);
        }

        try {
            \App\Http\Controllers\Api\EsignAgreementController::sendSignedAgreementEmails($user, $agreement, 'manual_resend');

            return response()->json([
                'success' => true,
                'message' => 'Signed agreement emails resent successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send emails: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a new Digio KYC link for the customer.
     */
    public function generateDigioKycLink($id)
    {
        $user = User::findOrFail($id);

        // Delete old failed/rejected/expired/initiated/pending KYC records
        \App\Models\KycVerification::where('user_id', $user->id)
            ->whereIn('status', ['rejected', 'failed', 'expired', 'initiated', 'pending', 'requested'])
            ->delete();

        $kycService = new \App\Services\KycService();
        $result = $kycService->initiateKyc([
            'user_id' => $user->id,
            'name'    => $user->name,
            'mobile'  => $user->mobile,
            'email'   => $user->email,
        ]);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to initiate Digio KYC.'
            ], 500);
        }

        // Log declaration acceptance audit trail
        $declarationText = AppSetting::getValue('kyc', 'declaration', "I hereby authorize Grow Capital Research to retrieve my profile and verify my identity details via Digio secure KYC gateway. I confirm that the Aadhaar and PAN details provided belong to me and are correct.");
        \App\Models\KycVerification::where('digio_document_id', $result['document_id'])->update([
            'declaration_accepted' => true,
            'declaration_accepted_at' => now(),
            'declaration_text' => $declarationText,
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => $result['redirect_url']
        ]);
    }
}
