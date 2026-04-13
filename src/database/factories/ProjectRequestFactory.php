<?php

namespace Database\Factories;

use App\Models\ProjectRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectRequest>
 */
class ProjectRequestFactory extends Factory
{
    protected $model = ProjectRequest::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'contact' => fake()->safeEmail(),
            'task' => fake()->paragraph(),
            'attachment_path' => null,
        ];
    }
}
