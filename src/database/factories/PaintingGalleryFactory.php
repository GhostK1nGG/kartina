<?php

namespace Database\Factories;

use App\Models\Painting;
use App\Models\PaintingGallery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaintingGallery>
 */
class PaintingGalleryFactory extends Factory
{
    protected $model = PaintingGallery::class;

    public function definition(): array
    {
        return [
            'painting_id' => Painting::factory(),
            'image_path' => 'paintings/gallery-' . fake()->unique()->numberBetween(1, 9999) . '.jpg',
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
