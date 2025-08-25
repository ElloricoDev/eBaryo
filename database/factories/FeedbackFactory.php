<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'message' => $this->faker->paragraph(),
            'status' => 'pending',
            'type' => 'general',
            'notified' => false,
            'admin_notified' => false,
            'response' => null,
            'responded_at' => null,
            'book_id' => null,
        ];
    }

    /**
     * Indicate that the feedback has been responded to.
     */
    public function responded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'responded',
            'response' => $this->faker->paragraph(),
            'responded_at' => now(),
        ]);
    }

    /**
     * Indicate that the feedback is a book report.
     */
    public function bookReport(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'book_report',
            'message' => 'Book Report - Book ID: 1\nReason: ' . $this->faker->sentence() . '\nDescription: ' . $this->faker->paragraph(),
        ]);
    }
}
