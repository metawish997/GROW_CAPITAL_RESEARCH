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
}
