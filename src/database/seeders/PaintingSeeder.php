<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Painting;
use App\Models\PaintingGallery;
use Illuminate\Database\Seeder;

class PaintingSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->get()->keyBy('slug');

        $paintings = [
            [
                'category_slug' => 'heavenly-light',
                'title' => 'Ember Field',
                'slug' => 'ember-field',
                'year' => '2026',
                'size' => '140×100',
                'price_rub' => 320000.00,
                'price_usd' => 3490.00,
                'short_desc' => 'Большая работа для главного акцента экспозиции.',
                'full_desc' => 'Картина о свете, который остаётся в памяти после заката. Тёплые золотистые слои встречаются с прохладной глубиной, создавая ощущение тихого внутреннего свечения.',
                'main_image' => 'assets/images/home/gallery-ember-field.jpg',
                'is_active' => true,
                'is_featured' => true,
                'gallery' => [
                    'assets/images/home/gallery-ember-field.jpg',
                    'assets/images/home/hero-night-bloom.jpg',
                    'assets/images/home/hero-soft-horizon.jpg',
                ],
            ],
            [
                'category_slug' => 'heavenly-light',
                'title' => 'Night Bloom',
                'slug' => 'night-bloom',
                'year' => '2025',
                'size' => '120×80',
                'price_rub' => 280000.00,
                'price_usd' => 3090.00,
                'short_desc' => 'Мягкое ночное сияние и глухая глубина тона.',
                'full_desc' => 'Полотно выстроено как тихая вспышка в темноте: золото не спорит с тенью, а медленно возникает из неё и удерживает взгляд.',
                'main_image' => 'assets/images/home/hero-night-bloom.jpg',
                'is_active' => true,
                'is_featured' => true,
                'gallery' => [
                    'assets/images/home/hero-night-bloom.jpg',
                    'assets/images/home/gallery-quiet-dust.jpg',
                ],
            ],
            [
                'category_slug' => 'golden-textures',
                'title' => 'Mineral Light',
                'slug' => 'mineral-light',
                'year' => '2024',
                'size' => '90×90',
                'price_rub' => 210000.00,
                'price_usd' => 2290.00,
                'short_desc' => 'Свет и текстура как драгоценная пыль.',
                'full_desc' => 'Работа исследует ощущение света внутри материала, как будто камень сам начинает светиться изнутри и теряет свою тяжесть.',
                'main_image' => 'assets/images/home/gallery-mineral-light.jpg',
                'is_active' => true,
                'is_featured' => true,
                'gallery' => [
                    'assets/images/home/gallery-mineral-light.jpg',
                    'assets/images/home/gallery-ember-field.jpg',
                ],
            ],
            [
                'category_slug' => 'golden-textures',
                'title' => 'Quiet Dust',
                'slug' => 'quiet-dust',
                'year' => '2023',
                'size' => '100×70',
                'price_rub' => 175000.00,
                'price_usd' => 1890.00,
                'short_desc' => 'Тонкая тишина и нежный цветовой след.',
                'full_desc' => 'Почти невесомая композиция, в которой цвет ведёт себя как пыль в луче света: незаметно, медленно и очень поэтично.',
                'main_image' => 'assets/images/home/gallery-quiet-dust.jpg',
                'is_active' => true,
                'is_featured' => false,
                'gallery' => [
                    'assets/images/home/gallery-quiet-dust.jpg',
                    'assets/images/home/gallery-mineral-light.jpg',
                ],
            ],
            [
                'category_slug' => 'quiet-landscapes',
                'title' => 'After Rain',
                'slug' => 'after-rain',
                'year' => '2026',
                'size' => '110×75',
                'price_rub' => 198000.00,
                'price_usd' => 2190.00,
                'short_desc' => 'Мягкий воздух после дождя и отражения света.',
                'full_desc' => 'Это состояние мира после ливня, когда всё становится тише, чище и чуть более прозрачным, чем было минуту назад.',
                'main_image' => 'assets/images/home/gallery-after-rain.jpg',
                'is_active' => true,
                'is_featured' => true,
                'gallery' => [
                    'assets/images/home/gallery-after-rain.jpg',
                    'assets/images/home/hero-soft-horizon.jpg',
                ],
            ],
            [
                'category_slug' => 'quiet-landscapes',
                'title' => 'Soft Horizon',
                'slug' => 'soft-horizon',
                'year' => '2025',
                'size' => '130×90',
                'price_rub' => 265000.00,
                'price_usd' => 2890.00,
                'short_desc' => 'Дышащий горизонт с мягким золотым слоем.',
                'full_desc' => 'Пейзаж построен как длинный спокойный вдох: пространство почти растворено, но в нём остаётся ясная линия света и глубины.',
                'main_image' => 'assets/images/home/hero-soft-horizon.jpg',
                'is_active' => true,
                'is_featured' => false,
                'gallery' => [
                    'assets/images/home/hero-soft-horizon.jpg',
                    'assets/images/home/gallery-after-rain.jpg',
                ],
            ],
            [
                'category_slug' => 'heavenly-light',
                'title' => 'Amber Trace',
                'slug' => 'amber-trace',
                'year' => '2026',
                'size' => '80×120',
                'price_rub' => 240000.00,
                'price_usd' => 2590.00,
                'short_desc' => 'Вертикальная работа о тепле, следе и свете.',
                'full_desc' => 'Композиция держится на вытянутом движении вверх, будто свет поднимается сквозь ткань воздуха и оставляет тонкий золотой след.',
                'main_image' => 'assets/images/home/hero-amber-trace.jpg',
                'is_active' => true,
                'is_featured' => false,
                'gallery' => [
                    'assets/images/home/hero-amber-trace.jpg',
                    'assets/images/home/hero-night-bloom.jpg',
                ],
            ],
            [
                'category_slug' => 'quiet-landscapes',
                'title' => 'Velvet Echo',
                'slug' => 'velvet-echo',
                'year' => '2024',
                'size' => '100×100',
                'price_rub' => 225000.00,
                'price_usd' => 2450.00,
                'short_desc' => 'Лёгкая и загадочная работа с мягкой глубиной.',
                'full_desc' => 'Мягкие оттенки и туманная фактура создают образ эха — чего-то сказанного когда-то давно, но до сих пор живущего внутри пространства.',
                'main_image' => 'assets/images/home/gallery-velvet-echo.jpg',
                'is_active' => true,
                'is_featured' => true,
                'gallery' => [
                    'assets/images/home/gallery-velvet-echo.jpg',
                    'assets/images/home/gallery-after-rain.jpg',
                ],
            ],
            [
                'category_slug' => 'golden-textures',
                'title' => 'Cloud Veil',
                'slug' => 'cloud-veil',
                'year' => '2022',
                'size' => '95×65',
                'price_rub' => 162000.00,
                'price_usd' => 1790.00,
                'short_desc' => 'Полупрозрачный слой света и воздуха.',
                'full_desc' => 'Небольшая работа с почти призрачной фактурой: золотой оттенок не доминирует, а как будто всплывает изнутри облачного слоя.',
                'main_image' => 'assets/images/home/gallery-mineral-light.jpg',
                'is_active' => true,
                'is_featured' => false,
                'gallery' => [
                    'assets/images/home/gallery-mineral-light.jpg',
                    'assets/images/home/hero-amber-trace.jpg',
                ],
            ],
        ];

        foreach ($paintings as $index => $data) {
            $category = $categories[$data['category_slug']];
            $galleryImages = $data['gallery'];
            unset($data['category_slug'], $data['gallery']);

            $painting = Painting::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['category_id' => $category->id]),
            );

            foreach ($galleryImages as $sortOrder => $imagePath) {
                PaintingGallery::updateOrCreate(
                    [
                        'painting_id' => $painting->id,
                        'image_path' => $imagePath,
                    ],
                    [
                        'sort_order' => $sortOrder,
                    ],
                );
            }

            PaintingGallery::query()
                ->where('painting_id', $painting->id)
                ->whereNotIn('image_path', $galleryImages)
                ->delete();
        }
    }
}
