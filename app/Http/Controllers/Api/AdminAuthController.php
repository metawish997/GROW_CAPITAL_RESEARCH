<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    /**
     * Admin Login with Email + Password.
     * POST /api/admin/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $admin = User::where('email', $request->email)
            ->whereIn('role', ['admin', 'staff'])
            ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Issue a new token for the session
        $token = $admin->createToken('admin-token', ['role:admin'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Admin login successful.',
            'token'   => $token,
            'admin'   => [
                'id'    => $admin->id,
                'name'  => $admin->name,
                'email' => $admin->email,
                'role'  => $admin->role,
            ],
        ]);
    }

    /**
     * Admin Logout.
     * POST /api/admin/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin logged out successfully.',
        ]);
    }

    /**
     * Get authenticated admin profile.
     * GET /api/admin/me
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'admin'   => $request->user(),
        ]);
    }

    /**
     * Change password for logged-in admin/staff.
     * POST /api/admin/change-password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'          => 'required|string',
            'new_password'              => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }

    /**
     * Send password reset link for admin/staff.
     * POST /api/admin/forgot-password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)
            ->whereIn('role', ['admin', 'staff'])
            ->first();

        // ponytail: always return success to prevent email enumeration
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'If a matching account exists, a reset link has been sent.',
            ]);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = url("/admin/reset-password?token={$token}&email=" . urlencode($user->email));

        try {
            $this->configureDynamicSmtp();

            Mail::send([], [], function ($message) use ($user, $resetUrl) {
                $message->to($user->email)
                    ->subject('Reset Your Admin Password - Grow Capitals')
                    ->html(
                        '<div style="font-family:Arial,sans-serif;max-width:500px;margin:0 auto;padding:32px;background:#f8fafc;border-radius:16px;">' .
                        '<div style="text-align:center;margin-bottom:24px;"><strong style="font-size:20px;color:#004b87;">Grow Capitals Research</strong></div>' .
                        '<div style="background:#ffffff;padding:24px;border-radius:12px;border:1px solid #e2e8f0;">' .
                        '<p style="margin:0 0 12px;color:#334155;">Hi ' . e($user->name) . ',</p>' .
                        '<p style="margin:0 0 20px;color:#334155;">You requested a password reset. Click the button below to set a new password:</p>' .
                        '<div style="text-align:center;margin:24px 0;"><a href="' . $resetUrl . '" style="display:inline-block;padding:14px 32px;background:#004b87;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:700;font-size:14px;">Reset Password</a></div>' .
                        '<p style="margin:0;color:#94a3b8;font-size:12px;">This link expires in 60 minutes. If you did not request this, ignore this email.</p>' .
                        '</div></div>'
                    );
            });

            Log::info('[ADMIN] Password reset email sent to: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('[ADMIN] Failed to send reset email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset email. Please check SMTP settings.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'If a matching account exists, a reset link has been sent.',
        ]);
    }

    /**
     * Reset password using token.
     * POST /api/admin/reset-password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.',
            ], 422);
        }

        // ponytail: 60-min expiry check
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Reset link has expired. Please request a new one.',
            ], 422);
        }

        $user = User::where('email', $request->email)
            ->whereIn('role', ['admin', 'staff'])
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully. You can now sign in.',
        ]);
    }

    /**
     * Configure SMTP dynamically from DB settings.
     */
    private function configureDynamicSmtp(): void
    {
        $smtp = AppSetting::getGroup('smtp');
        if (!empty($smtp) && !empty($smtp['host'])) {
            config([
                'mail.default'                 => 'smtp',
                'mail.mailers.smtp.host'       => $smtp['host'],
                'mail.mailers.smtp.port'       => $smtp['port'] ?? 587,
                'mail.mailers.smtp.username'   => $smtp['username'] ?? '',
                'mail.mailers.smtp.password'   => $smtp['password'] ?? '',
                'mail.mailers.smtp.encryption' => $smtp['encryption'] ?? 'tls',
                'mail.from.address'            => $smtp['from_address'] ?? 'noreply@growcapitals.com',
                'mail.from.name'               => $smtp['from_name'] ?? 'Grow Capitals',
            ]);
        }
    }
}
