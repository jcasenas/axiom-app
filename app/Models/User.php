<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table      = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'role_id',
        'department_id',
        'full_name',
        'email',
        'password',
        'profile_photo',
        'account_status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password'      => 'hashed',
        'last_login_at' => 'datetime', // Carbon methods work in blade
    ];

    // ── Relationships ──────────────────────────────────────────

    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id', 'role_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'user_id', 'user_id');
    }

    // ── Helpers ────────────────────────────────────────────────

    public function hasRole(string $roleName): bool
    {
        return $this->role?->role_name === $roleName;
    }

    public function isAdmin(): bool     { return $this->hasRole('Admin'); }
    public function isLibrarian(): bool { return $this->hasRole('Librarian'); }
    public function isFaculty(): bool   { return $this->hasRole('Faculty'); }
    public function isStudent(): bool   { return $this->hasRole('Student'); }
}