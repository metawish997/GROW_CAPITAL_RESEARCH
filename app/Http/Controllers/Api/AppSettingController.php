<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    // Available setting groups
    private array $groups = ['smtp', 'sms', 'digio', 'razorpay'];

    // Allowed keys per group (prevents arbitrary key injection)
    private array $allowedKeys = [
        'smtp' => ['host', 'port', 'username', 'password', 'encryption', 'from_address', 'from_name'],
        'sms'  => ['api_key', 'sender_id', 'base_url', 'provider'],
        'digio'    => ['client_id', 'client_secret', 'base_url', 'environment', 'workflow'],
        'razorpay' => ['key_id', 'key_secret', 'webhook_secret', 'environment'],
    ];

    /**
     * Get all settings for a group.
     * GET /api/admin/settings/{group}
     */
    public function show(string $group)
    {
        if (!in_array($group, $this->groups)) {
            return response()->json(['success' => false, 'message' => 'Invalid settings group.'], 404);
        }

        $settings = AppSetting::getGroup($group);

        // Mask sensitive fields before returning
        $sensitiveKeys = ['password', 'key_secret', 'webhook_secret', 'client_secret', 'api_key'];
        foreach ($sensitiveKeys as $key) {
            if (isset($settings[$key]) && $settings[$key] !== '') {
                $settings[$key] = '••••••••'; // masked
            }
        }

        return response()->json([
            'success'  => true,
            'group'    => $group,
            'settings' => $settings,
        ]);
    }

    /**
     * Save/update settings for a group.
     * POST /api/admin/settings/{group}
     */
    public function update(Request $request, string $group)
    {
        if (!in_array($group, $this->groups)) {
            return response()->json(['success' => false, 'message' => 'Invalid settings group.'], 404);
        }

        $allowedKeys = $this->allowedKeys[$group];
        $data        = $request->only($allowedKeys);

        if (empty($data)) {
            return response()->json(['success' => false, 'message' => 'No valid settings provided.'], 422);
        }

        // Don't overwrite masked passwords (user left field unchanged)
        $sensitiveKeys = ['password', 'key_secret', 'webhook_secret', 'client_secret', 'api_key'];
        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key]) && $data[$key] === '••••••••') {
                unset($data[$key]); // skip, keep existing value
            }
        }

        AppSetting::setGroup($group, $data);

        return response()->json([
            'success' => true,
            'message' => ucfirst($group) . ' settings saved successfully.',
        ]);
    }

    /**
     * Get summary of all settings groups (which ones are configured).
     * GET /api/admin/settings
     */
    public function index()
    {
        $summary = [];
        foreach ($this->groups as $group) {
            $settings        = AppSetting::getGroup($group);
            $summary[$group] = [
                'configured' => !empty($settings),
                'keys_set'   => count($settings),
            ];
        }

        return response()->json([
            'success'  => true,
            'settings' => $summary,
        ]);
    }
}
