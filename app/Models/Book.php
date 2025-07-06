<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'published_year',
        'description',
        'cover_image',
        'ebook_file',
        'category_id',
        'publisher',
        'language',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function savers()
    {
        return $this->belongsToMany(User::class, 'book_user')->withTimestamps();
    }
}
