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

    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }

    public function readingLogs()
    {
        return $this->hasMany(ReadingLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
