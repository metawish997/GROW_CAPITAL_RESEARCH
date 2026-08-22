<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\KycVerification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class KycMediaUploadController extends Controller
{
    /**
     * Show the upload form.
     */
    public function showForm($user_id, $token)
    {
        $expectedToken = sha1($user_id . 'grow-capital-media');
        if ($token !== $expectedToken) {
            abort(403, 'Invalid or expired media upload token.');
        }

        $user = User::findOrFail($user_id);
        $kyc = $user->kyc;

        return view('kyc.upload_media', [
            'user' => $user,
            'kyc' => $kyc,
            'token' => $token,
        ]);
    }

    /**
     * Handle form submission.
     */
    public function submitForm(Request $request, $user_id, $token)
    {
        $expectedToken = sha1($user_id . 'grow-capital-media');
        if ($token !== $expectedToken) {
            abort(403, 'Invalid or expired media upload token.');
        }

        $request->validate([
            'selfie' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'signature_data' => 'nullable|string', // Base64 signature from canvas
        ]);

        $user = User::findOrFail($user_id);
        $kyc = $user->kyc;
        if (!$kyc) {
            $kyc = KycVerification::create([
                'user_id' => $user->id,
                'digio_document_id' => 'MANUAL_' . time(),
                'customer_name' => $user->name ?? 'Manual Upload',
                'customer_mobile' => $user->mobile ?? '—',
                'reference_id' => 'MANUAL_' . time(),
                'status' => 'approved',
                'kyc_details' => [],
            ]);
        }

        $details = $kyc->kyc_details ?? [];

        // 1. Process Selfie Upload (File Input)
        if ($request->hasFile('selfie')) {
            $file = $request->file('selfie');
            $selfieName = "kyc_media/{$user->id}_selfie_manual_" . time() . ".jpg";
            Storage::disk('local')->put($selfieName, file_get_contents($file));
            $details['selfie_local_path'] = Storage::disk('local')->path($selfieName);
            $details['selfie_file'] = 'manual_' . time();
        }

        // 2. Process Signature (Base64 Canvas string)
        if ($request->filled('signature_data')) {
            $base64Image = $request->input('signature_data');
            
            // Clean base64 string
            $imageParts = explode(";base64,", $base64Image);
            if (count($imageParts) === 2) {
                $imageTypeAux = explode("image/", $imageParts[0]);
                $imageType = $imageTypeAux[1] ?? 'png';
                $imageBase64 = base64_decode($imageParts[1]);
                
                $sigName = "kyc_media/{$user->id}_signature_manual_" . time() . ".{$imageType}";
                Storage::disk('local')->put($sigName, $imageBase64);
                
                $details['signature_local_path'] = Storage::disk('local')->path($sigName);
                $details['signature_file'] = 'manual_' . time();
            }
        }

        $kyc->update([
            'kyc_details' => $details
        ]);

        return back()->with('success', 'Documents uploaded successfully! You can close this window now.');
    }
}
