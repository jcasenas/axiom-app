<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table      = 'user_roles';
    protected $primaryKey = 'role_id';
    public    $timestamps = false;

    protected $fillable = [
        'role_name',
        'borrow_limit',
        'borrow_days',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}