<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookFormat extends Model
{
    protected $table      = 'ebook_formats';
    protected $primaryKey = 'format_id';
    public    $timestamps = false; // ebook_formats has no created_at/updated_at

    protected $fillable = [
        'format_type',
        'description',
    ];

    public function ebooks()
    {
        return $this->hasMany(Ebook::class, 'format_id', 'format_id');
    }
}