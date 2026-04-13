<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Небесный свет', 'slug' => 'heavenly-light', 'sort_order' => 10],
            ['name' => 'Тихие ландшафты', 'slug' => 'quiet-landscapes', 'sort_order' => 20],
            ['name' => 'Золотые текстуры', 'slug' => 'golden-textures', 'sort_order' => 30],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
