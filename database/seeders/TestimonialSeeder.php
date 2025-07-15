<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        Testimonial::insert([
            [
                'author_name' => 'Maria',
                'role' => 'Parent',
                'content' => 'eBaryo Library made it so easy for me to find books for my kids!',
                'approved' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'author_name' => 'John',
                'role' => 'Student',
                'content' => 'I love being able to read anywhere, anytime. Highly recommended!',
                'approved' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'author_name' => 'Liza',
                'role' => 'Teacher',
                'content' => 'A wonderful resource for our whole community.',
                'approved' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
