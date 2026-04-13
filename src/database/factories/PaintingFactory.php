<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Painting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Painting>
 */
class PaintingFactory extends Factory
{
    protected $model = Painting::class;

    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(3, true));

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'year' => (string) fake()->numberBetween(2018, 2026),
            'size' => fake()->randomElement(['30x40 см', '50x70 см', '80x100 см']),
            'price_rub' => fake()->numberBetween(10000, 300000),
            'price_usd' => fake()->numberBetween(150, 4000),
            'short_desc' => fake()->sentence(),
            'full_desc' => fake()->paragraph(),
            'main_image' => 'paintings/main.jpg',
            'is_active' => true,
            'is_featured' => false,
        ];
    }
}
