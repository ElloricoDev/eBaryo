<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_name',
        'first_name',
        'last_name',
        'middle_name',
        'contact_number',
        'address',
        'student',
        'role',
        'email',
        'google_id',
        'facebook_id',
        'avatar',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function savedBooks()
    {
        return $this->belongsToMany(Book::class, 'book_user', 'user_id', 'book_id')->withTimestamps();
    }

    public function bookReviews()
    {
        return $this->hasMany(BookReview::class);
    }

    public function readingLogs()
    {
        return $this->hasMany(ReadingLog::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Category::class, 'user_genres', 'user_id', 'category_id')->withTimestamps();
    }


}
