<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycVerification;
use App\Services\KycService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KycController extends Controller
{
    public function __construct(protected KycService $kycService) {}

    /**
     * Get current user's KYC status.
     * GET /api/user/kyc/status
     */
    public function status(Request $request)
    {
        $user = $request->user();
        $kyc  = KycVerification::where('user_id', $user->id)->latest()->first();

        // Sync mobile if already approved but user mobile is missing
        if ($kyc && $kyc->isApproved() && !empty($kyc->customer_mobile) && $user->mobile !== $kyc->customer_mobile) {
            $user->update(['mobile' => $kyc->customer_mobile]);
        }

        if (!$kyc) {
            $declarationText = \App\Models\AppSetting::getValue('kyc', 'declaration', "I hereby authorize Grow Capital Research to retrieve my profile and verify my identity details via Digio secure KYC gateway. I confirm that the Aadhaar and PAN details provided belong to me and are correct.");
            return response()->json([
                'success'    => true,
                'kyc_status' => 'not_started',
                'kyc'        => null,
                'declaration_text' => $declarationText,
                'message'    => 'KYC not started yet.',
            ]);
        }

        $declarationText = \App\Models\AppSetting::getValue('kyc', 'declaration', "I hereby authorize Grow Capital Research to retrieve my profile and verify my identity details via Digio secure KYC gateway. I confirm that the Aadhaar and PAN details provided belong to me and are correct.");
        return response()->json([
            'success'    => true,
            'kyc_status' => $kyc->status,
            'declaration_text' => $declarationText,
            'kyc'        => [
                'id'                => $kyc->id,
                'status'            => $kyc->status,
                'is_approved'       => $kyc->isApproved(),
                'is_pending'        => $kyc->isPending(),
                'is_expired'        => $kyc->isExpired(),
                'customer_name'     => $kyc->customer_name,
                'customer_mobile'   => $kyc->customer_mobile,
                'kyc_completed_at'  => $kyc->kyc_completed_at?->toDateTimeString(),
                'kyc_expires_at'    => $kyc->kyc_expires_at?->toDateTimeString(),
                'days_until_expiry' => $kyc->days_until_expiry,
            ],
        ]);
    }

    /**
     * Initiate KYC — creates Digio request and returns redirect URL.
     * POST /api/user/kyc/initiate
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|min:10|max:15',
            'name'   => 'nullable|string|max:100',
        ]);

        $user = $request->user();

        // Check if active KYC already exists
        $existingKyc = KycVerification::where('user_id', $user->id)
            ->whereIn('status', ['approval_pending', 'approved'])
            ->latest()
            ->first();

        if ($existingKyc) {
            if ($existingKyc->isApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'KYC is already approved.',
                    'kyc_status' => 'approved',
                ], 422);
            }

            return response()->json([
                'success'    => false,
                'message'    => 'A KYC request is currently awaiting admin approval.',
                'kyc_status' => $existingKyc->status,
            ], 422);
        }

        // Delete old failed/rejected/expired/initiated/pending KYC records so user can restart
        KycVerification::where('user_id', $user->id)
            ->whereIn('status', ['rejected', 'failed', 'expired', 'initiated', 'pending', 'requested'])
            ->delete();

        $result = $this->kycService->initiateKyc([
            'user_id' => $user->id,
            'name'    => $request->name ?? $user->name,
            'mobile'  => $request->mobile,
            'email'   => $user->email,
        ]);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to initiate KYC.',
            ], 500);
        }

        // Log declaration acceptance audit trail
        $declarationText = \App\Models\AppSetting::getValue('kyc', 'declaration', "I hereby authorize Grow Capital Research to retrieve my profile and verify my identity details via Digio secure KYC gateway. I confirm that the Aadhaar and PAN details provided belong to me and are correct.");
        KycVerification::where('digio_document_id', $result['document_id'])->update([
            'declaration_accepted' => true,
            'declaration_accepted_at' => now(),
            'declaration_text' => $declarationText,
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'KYC initiated. Please complete via the Digio portal.',
            'document_id'  => $result['document_id'],
            'redirect_url' => $result['redirect_url'],
        ]);
    }

    /**
     * Sync KYC status from Digio — call this after user completes the Digio flow.
     * POST /api/user/kyc/sync
     */
    public function sync(Request $request)
    {
        $user = $request->user();
        $kyc  = $this->kycService->syncUserKyc($user);

        if (!$kyc) {
            return response()->json([
                'success' => false,
                'message' => 'No KYC record found or sync failed.',
            ], 404);
        }

        return response()->json([
            'success'    => true,
            'kyc_status' => $kyc->status,
            'is_approved' => $kyc->isApproved(),
            'message'    => 'KYC status synced.',
        ]);
    }

    /**
     * Digio callback — called after user completes KYC on Digio portal.
     * GET /kyc/callback (web route)
     */
    public function callback(Request $request)
    {
        Log::info('[KYC] ===== DIGIO CALLBACK START =====');
        Log::info('[KYC] Payload', $request->all());

        $digioDocId     = $request->input('digio_doc_id');
        $callbackStatus = $request->input('status');

        if (!$digioDocId) {
            Log::error('[KYC] Callback missing digio_doc_id');
            return redirect('/dashboard')->with('error', 'Invalid KYC callback.');
        }

        $kyc = KycVerification::findByDocumentId($digioDocId);

        if (!$kyc) {
            Log::error('[KYC] Callback: KYC not found for doc ID: ' . $digioDocId);
            return redirect('/dashboard')->with('error', 'KYC record not found.');
        }

        // Attempt manual approval if status is in actionable states
        if (in_array($kyc->status, ['initiated', 'pending', 'approval_pending', 'requested'])) {
            $this->kycService->approveKycManually($digioDocId);
        }

        // Fetch and update final status from Digio
        $kyc = $this->kycService->fetchAndUpdateKycStatus($digioDocId);

        Log::info('[KYC] ===== DIGIO CALLBACK END =====', ['status' => $kyc?->status]);

        if ($kyc?->isApproved()) {
            return redirect('/dashboard')->with('success', '✅ KYC verified successfully!');
        }

        if ($kyc?->isPending()) {
            return redirect('/dashboard')->with('info', '⏳ KYC submitted and under review.');
        }

        return redirect('/dashboard')->with('error', '❌ KYC verification failed. Please retry.');
    }

    // ─────────────────── ADMIN ROUTES ─────────────────────────────

    /**
     * Admin: Get all KYC records with pagination.
     * GET /api/admin/kyc
     */
    public function adminIndex(Request $request)
    {
        $status = $request->query('status');

        $query = KycVerification::with('user:id,name,email')
            ->orderByDesc('created_at');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $records = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $records,
        ]);
    }

    /**
     * Admin: Get single KYC detail.
     * GET /api/admin/kyc/{id}
     */
    public function adminShow(int $id)
    {
        $kyc = KycVerification::with('user:id,name,email,mobile')->findOrFail($id);

        return response()->json([
            'success' => true,
            'kyc'     => $kyc,
        ]);
    }

    /**
     * Admin: Manually approve a KYC.
     * POST /api/admin/kyc/{id}/approve
     */
    public function adminApprove(int $id)
    {
        $kyc = KycVerification::findOrFail($id);
        $this->kycService->approveKycManually($kyc->digio_document_id);
        $kyc = $this->kycService->fetchAndUpdateKycStatus($kyc->digio_document_id);

        return response()->json([
            'success' => true,
            'message' => 'KYC approved and synced.',
            'status'  => $kyc?->status,
        ]);
    }

    /**
     * Admin: Manually reject a KYC.
     * POST /api/admin/kyc/{id}/reject
     */
    public function adminReject(Request $request, int $id)
    {
        $kyc = KycVerification::findOrFail($id);
        $kyc->reject($request->input('reason', 'Rejected by admin.'));

        return response()->json([
            'success' => true,
            'message' => 'KYC rejected.',
        ]);
    }

    /**
     * Admin: Force sync KYC status from Digio.
     * POST /api/admin/kyc/{id}/sync
     */
    public function adminSync(int $id)
    {
        $kyc = KycVerification::findOrFail($id);
        $updated = $this->kycService->fetchAndUpdateKycStatus($kyc->digio_document_id);

        return response()->json([
            'success' => true,
            'message' => 'KYC synced.',
            'status'  => $updated?->status,
        ]);
    }
}
