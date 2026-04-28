<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    const CREATED_AT = 'requested_at'; 
    const UPDATED_AT = 'updated_at';
    protected $table      = 'borrowings';
    protected $primaryKey = 'borrow_id';

    protected $fillable = [
        'user_id',
        'ebook_id',
        'approved_by',
        'borrow_date',
        'due_date',
        'access_url',
        'access_expires_at',
        'status',
    ];

    protected $casts = [
        'borrow_date'       => 'date',     // ->format('M d, Y') works in blade
        'due_date'          => 'date',
        'access_expires_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function ebook()
    {
        return $this->belongsTo(Ebook::class, 'ebook_id', 'ebook_id');
    }

    /** Librarian/Admin who approved the request. */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    public function getRouteKeyName()
    {
        return 'borrow_id';
    }

    // ── Helpers ────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->access_expires_at && $this->access_expires_at->isPast());
    }
}