<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Get a paginated list of all users.
     */
    public function index(Request $request)
    {
        $query = User::query();

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
        // Eager load KYC verification
        $users = $query->with('kyc')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'users'   => $users
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
}
