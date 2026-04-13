<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'author_name' => fake()->name(),
            'author_city' => fake()->city(),
            'text' => fake()->paragraph(),
            'rating' => fake()->numberBetween(1, 5),
            'image_path' => null,
            'is_published' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'is_published' => true,
        ]);
    }
}
