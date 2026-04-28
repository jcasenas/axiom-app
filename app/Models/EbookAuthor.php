<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookAuthor extends Model
{
    protected $table      = 'ebook_authors';
    protected $primaryKey = 'author_id';
    public $timestamps = false;

    protected $fillable = [
        'author_name',
        'bio',
    ];

    public function ebooks()
    {
        return $this->hasMany(Ebook::class, 'author_id', 'author_id');
    }
}