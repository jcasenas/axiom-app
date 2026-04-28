<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table      = 'system_settings';
    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
        'updated_by',
    ];

    // ── Relationship ───────────────────────────────────────────

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    // ── Static Helper ──────────────────────────────────────────

    /**
     * Retrieve a single setting value by key.
     *
     * Usage: SystemSetting::getValue('system_status', 'active')
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::where('setting_key', $key)->value('setting_value') ?? $default;
    }
}