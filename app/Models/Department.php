<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table      = 'departments';
    protected $primaryKey = 'department_id';
    public    $timestamps = false;

    protected $fillable = [
        'department_name',
        'description',
    ];

    // ── Relationships ──────────────────────────────────────────

    /**
     * DepartmentController calls withCount('users') — this relationship
     * must exist or Laravel throws "Call to undefined relationship".
     */
    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'department_id');
    }
}