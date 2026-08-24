<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['group', 'key', 'value'];

    /**
     * Get all settings for a specific group as a key=>value array.
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Bulk upsert settings for a group.
     */
    public static function setGroup(string $group, array $data): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(
                ['group' => $group, 'key' => $key],
                ['value' => $value]
            );
        }
    }

    /**
     * Get a single setting value.
     */
    public static function getValue(string $group, string $key, $default = null)
    {
        return static::where('group', $group)
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    /**
     * Get active Digio credentials based on the active_mode toggle.
     * Returns ['client_id', 'client_secret', 'base_url', 'workflow', 'mode'].
     * ponytail: single source of truth for sandbox/live credential resolution
     */
    public static function getActiveDigio(): array
    {
        $all  = static::getGroup('digio');
        $mode = trim($all['active_mode'] ?? 'live');

        if ($mode === 'sandbox') {
            return [
                'client_id'     => trim($all['sandbox_client_id'] ?? ''),
                'client_secret' => trim($all['sandbox_client_secret'] ?? ''),
                'base_url'      => rtrim(trim($all['sandbox_base_url'] ?? 'https://ext.digio.in:444'), '/'),
                'workflow'      => trim($all['sandbox_workflow'] ?? $all['workflow'] ?? 'kyc_with_aadhaar'),
                'mode'          => 'sandbox',
            ];
        }

        return [
            'client_id'     => trim($all['client_id'] ?? ''),
            'client_secret' => trim($all['client_secret'] ?? ''),
            'base_url'      => rtrim(trim($all['base_url'] ?? ''), '/'),
            'workflow'      => trim($all['workflow'] ?? 'kyc_with_aadhaar'),
            'mode'          => 'live',
        ];
    }
}
