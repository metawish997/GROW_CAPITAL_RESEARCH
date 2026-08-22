<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserAuthController extends Controller
{
    /**
     * Step 1: Send OTP to email for login / registration.
     * POST /api/user/send-otp
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $otp       = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(10);

        Log::info('[OTP] ── Send OTP Request ──────────────────────');
        Log::info('[OTP] Email     : ' . $request->email);
        Log::info('[OTP] Generated : ' . $otp);
        Log::info('[OTP] Expires   : ' . $expiresAt);

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => explode('@', $request->email)[0],
                'role' => 'user',
            ]
        );

        Log::info('[OTP] User ID   : ' . $user->id . ' (new=' . ($user->wasRecentlyCreated ? 'YES' : 'NO') . ')');

        // Save OTP to user
        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        Log::info('[OTP] OTP saved to DB ✅');

        // Attempt to send OTP Email
        $mailStatus = 'not_sent';
        $mailError  = null;
        $mailDriver = config('mail.default');
        $smtpUsed   = [];

        try {
            [$mailDriver, $smtpUsed] = $this->sendOtpEmail($user->email, $otp);
            $mailStatus = 'sent';
            Log::info('[OTP] Mail sent ✅ via driver: ' . $mailDriver);
        } catch (\Exception $e) {
            $mailStatus = 'failed';
            $mailError  = $e->getMessage();
            Log::error('[OTP] Mail FAILED ❌ : ' . $mailError);
        }

        Log::info('[OTP] ─────────────────────────────────────────');

        // Build response
        $response = [
            'success' => true,
            'message' => 'OTP sent to your email. Valid for 10 minutes.',
            'debug'   => null,
        ];

        // In DEBUG mode: expose debug info in API response (browser/Postman visible)
        if (config('app.debug')) {
            $response['debug'] = [
                'otp'         => $otp,           // OTP visible in response for easy testing
                'mail_status' => $mailStatus,     // sent | failed | not_sent
                'mail_driver' => $mailDriver,     // smtp | log | etc.
                'mail_error'  => $mailError,      // null or error message
                'smtp_host'   => $smtpUsed['host'] ?? config('mail.mailers.smtp.host'),
                'smtp_port'   => $smtpUsed['port'] ?? config('mail.mailers.smtp.port'),
                'smtp_user'   => $smtpUsed['username'] ?? config('mail.mailers.smtp.username'),
                'from'        => $smtpUsed['from_address'] ?? config('mail.from.address'),
                'note'        => 'This debug block is hidden when APP_DEBUG=false',
            ];
        }

        return response()->json($response);
    }

    /**
     * Step 2: Verify OTP and issue Sanctum token.
     * POST /api/user/verify-otp
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        Log::info('[OTP] Verify attempt for: ' . $request->email . ' OTP: ' . $request->otp);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::warning('[OTP] User not found: ' . $request->email);
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        if ((string) $user->otp !== (string) $request->otp) {
            Log::warning('[OTP] Invalid OTP for: ' . $request->email . ' | DB: ' . $user->otp . ' | Given: ' . $request->otp);
            return response()->json(['success' => false, 'message' => 'Invalid OTP.'], 422);
        }

        if (Carbon::now()->isAfter($user->otp_expires_at)) {
            Log::warning('[OTP] Expired OTP for: ' . $request->email);
            return response()->json(['success' => false, 'message' => 'OTP has expired. Please request a new one.'], 422);
        }

        // Clear OTP after successful verification
        $user->update([
            'otp'               => null,
            'otp_expires_at'    => null,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        // Issue Sanctum Token
        $token = $user->createToken('user-token', ['role:user'])->plainTextToken;

        Log::info('[OTP] Login successful for: ' . $user->email);

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    /**
     * Logout - Revoke current token.
     * POST /api/user/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Get authenticated user profile.
     * GET /api/user/me
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user'    => $request->user(),
        ]);
    }

    /**
     * Configure mailer dynamically from DB SMTP settings and send OTP email.
     * Returns [driver_used, smtp_config_array].
     */
    private function sendOtpEmail(string $email, string $otp): array
    {
        $smtpSettings = AppSetting::getGroup('smtp');
        $smtpUsed     = [];

        Log::info('[SMTP] Settings from DB: ' . json_encode($smtpSettings));

        if (!empty($smtpSettings) && !empty($smtpSettings['host'])) {
            $smtpUsed = [
                'host'         => $smtpSettings['host'],
                'port'         => $smtpSettings['port'] ?? 587,
                'username'     => $smtpSettings['username'] ?? '',
                'encryption'   => $smtpSettings['encryption'] ?? 'tls',
                'from_address' => $smtpSettings['from_address'] ?? 'noreply@growcapitals.com',
                'from_name'    => $smtpSettings['from_name'] ?? 'Grow Capitals',
            ];

            // Dynamically override mailer config at runtime from DB
            config([
                'mail.default'                 => 'smtp',
                'mail.mailers.smtp.host'       => $smtpUsed['host'],
                'mail.mailers.smtp.port'       => $smtpUsed['port'],
                'mail.mailers.smtp.username'   => $smtpUsed['username'],
                'mail.mailers.smtp.password'   => $smtpSettings['password'] ?? '',
                'mail.mailers.smtp.encryption' => $smtpUsed['encryption'],
                'mail.from.address'            => $smtpUsed['from_address'],
                'mail.from.name'               => $smtpUsed['from_name'],
            ]);

            Log::info('[SMTP] Dynamic config applied ✅ Host: ' . $smtpUsed['host'] . ':' . $smtpUsed['port']);
        } else {
            Log::warning('[SMTP] ⚠️ No SMTP found in DB — using .env default (driver: ' . config('mail.default') . ')');
            Log::warning('[SMTP] If driver is "log", check storage/logs/laravel.log for the OTP email content.');
        }

        $driver = config('mail.default');
        Log::info('[SMTP] Sending via driver: ' . $driver . ' → to: ' . $email);

        Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($email) {
            $message->to($email)->subject('Your Grow Capital Login OTP');
        });

        return [$driver, $smtpUsed];
    }
}
