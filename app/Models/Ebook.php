<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    protected $table      = 'ebooks';
    protected $primaryKey = 'ebook_id';

    protected $fillable = [
        'category_id',
        'author_id',
        'format_id',
        'title',
        'isbn',
        'published_year',
        'total_copies',
        'available_copies',
        'file_url',
        'cover_url',
        'description',
        'status',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(EbookCategory::class, 'category_id', 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(EbookAuthor::class, 'author_id', 'author_id');
    }

    public function format()
    {
        return $this->belongsTo(EbookFormat::class, 'format_id', 'format_id');
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'ebook_id', 'ebook_id');
    }

    // ── Helpers ────────────────────────────────────────────────

    public function isAvailable(): bool
    {
        return $this->status === 'active' && $this->available_copies > 0;
    }
}