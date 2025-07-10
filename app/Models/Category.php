<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'user_genres', 'category_id', 'user_id')->withTimestamps();
    }
} 