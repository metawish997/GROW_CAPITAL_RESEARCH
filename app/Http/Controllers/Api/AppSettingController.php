<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\KraSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    // Available setting groups
    private array $groups = ['smtp', 'digio', 'kyc', 'kra'];

    // Allowed keys per group (prevents arbitrary key injection)
    private array $allowedKeys = [
        'smtp' => ['host', 'port', 'username', 'password', 'encryption', 'from_address', 'from_name'],
        'digio'    => ['client_id', 'client_secret', 'base_url', 'environment', 'workflow', 'sandbox_client_id', 'sandbox_client_secret', 'sandbox_base_url', 'sandbox_workflow', 'active_mode'],
        'kyc'      => ['declaration'],
    ];

    private function authorizeSuperAdmin(Request $request)
    {
        if (!$request->user() || !$request->user()->isSuperAdmin()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Unauthorized. Super Admin access required.'
            ], 403));
        }
    }

    /**
     * Get all settings for a group.
     * GET /api/admin/settings/{group}
     */
    public function show(Request $request, string $group)
    {
        $this->authorizeSuperAdmin($request);
        if (!in_array($group, $this->groups)) {
            return response()->json(['success' => false, 'message' => 'Invalid settings group.'], 404);
        }

        if ($group === 'kra') {
            $settings = KraSetting::first() ?? new KraSetting();
            $settingsArr = $settings->toArray();
            
            // Mask sensitive fields
            $sensitiveKeys = ['kfin_password', 'sftp_password'];
            foreach ($sensitiveKeys as $key) {
                if (isset($settingsArr[$key]) && $settingsArr[$key] !== '') {
                    $settingsArr[$key] = '••••••••';
                }
            }
            return response()->json([
                'success'  => true,
                'group'    => 'kra',
                'settings' => $settingsArr,
            ]);
        }

        $settings = AppSetting::getGroup($group);

        // Mask sensitive fields before returning
        $sensitiveKeys = ['password', 'key_secret', 'webhook_secret', 'client_secret', 'sandbox_client_secret', 'api_key'];
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
        $this->authorizeSuperAdmin($request);
        if (!in_array($group, $this->groups)) {
            return response()->json(['success' => false, 'message' => 'Invalid settings group.'], 404);
        }

        if ($group === 'kra') {
            $allowedKeys = [
                'kfin_user_id', 'kfin_password', 'kfin_pos_code', 'kfin_uat_mode', 
                'sftp_host', 'sftp_port', 'sftp_username', 'sftp_password', 'auto_upload_on_approval'
            ];
            $data = $request->only($allowedKeys);

            // Don't overwrite masked fields
            $sensitiveKeys = ['kfin_password', 'sftp_password'];
            foreach ($sensitiveKeys as $key) {
                if (isset($data[$key]) && $data[$key] === '••••••••') {
                    unset($data[$key]);
                }
            }

            // Ensure booleans are cast correctly from request
            if (isset($data['kfin_uat_mode'])) {
                $data['kfin_uat_mode'] = filter_var($data['kfin_uat_mode'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
            if (isset($data['auto_upload_on_approval'])) {
                $data['auto_upload_on_approval'] = filter_var($data['auto_upload_on_approval'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }

            $settings = KraSetting::first();
            if ($settings) {
                $settings->update($data);
            } else {
                KraSetting::create($data);
            }

            return response()->json([
                'success' => true,
                'message' => 'KRA settings saved successfully.',
            ]);
        }

        $allowedKeys = $this->allowedKeys[$group];
        $data        = $request->only($allowedKeys);

        if (empty($data)) {
            return response()->json(['success' => false, 'message' => 'No valid settings provided.'], 422);
        }

        // Don't overwrite masked passwords (user left field unchanged)
        $sensitiveKeys = ['password', 'key_secret', 'webhook_secret', 'client_secret', 'sandbox_client_secret', 'api_key'];
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
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin($request);
        $summary = [];
        foreach ($this->groups as $group) {
            if ($group === 'kra') {
                $settings = KraSetting::first();
                $summary[$group] = [
                    'configured' => !empty($settings->kfin_user_id),
                    'uat_mode'   => $settings ? $settings->kfin_uat_mode : true,
                ];
                continue;
            }

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
