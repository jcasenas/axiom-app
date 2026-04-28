<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookCategory extends Model
{
    protected $table      = 'ebook_categories';
    protected $primaryKey = 'category_id';
    public    $timestamps = false;

    protected $fillable = [
        'category_name',
        'description',
    ];

    public function ebooks()
    {
        return $this->hasMany(Ebook::class, 'category_id', 'category_id');
    }
}