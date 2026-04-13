<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Painting;
use App\Models\PaintingGallery;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaintingsCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_only_featured_paintings_from_catalog(): void
    {
        $category = Category::factory()->create();

        Review::factory()->published()->create([
            'author_name' => 'Опубликованный отзыв для главной',
            'text' => 'Очень доволен работой художника.',
        ]);

        Review::factory()->create([
            'author_name' => 'Скрытый отзыв для главной',
            'text' => 'Этот отзыв не должен попасть в ленту.',
        ]);

        Painting::factory()->count(5)->for($category)->create([
            'is_featured' => true,
            'is_active' => true,
        ]);

        $hiddenFeatured = Painting::factory()->for($category)->create([
            'title' => 'Скрытая featured картина',
            'slug' => 'hidden-featured-painting',
            'is_featured' => true,
            'is_active' => false,
        ]);

        $nonFeatured = Painting::factory()->for($category)->create([
            'title' => 'Обычная картина',
            'slug' => 'regular-painting',
            'is_featured' => false,
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Опубликованный отзыв для главной');
        $response->assertDontSee('Скрытый отзыв для главной');
        $response->assertDontSee($hiddenFeatured->title);
        $response->assertDontSee($nonFeatured->title);
        $this->assertCount(5, $response->viewData('gallery')['paintings']);
    }

    public function test_paintings_index_filters_results(): void
    {
        $category = Category::factory()->create(['name' => 'Классика', 'slug' => 'classic']);
        $otherCategory = Category::factory()->create();

        Painting::factory()->for($category)->create([
            'title' => 'Подходящая картина',
            'slug' => 'matching-painting',
            'size' => '50x70 см',
            'year' => '2024',
            'price_rub' => 50000,
        ]);

        Painting::factory()->for($otherCategory)->create([
            'title' => 'Чужая картина',
            'slug' => 'other-painting',
            'size' => '80x100 см',
            'year' => '2021',
            'price_rub' => 150000,
        ]);

        $response = $this->get(route('paintings.index', [
            'category' => 'classic',
            'size' => '50x70 см',
            'year' => '2024',
            'price_max' => 60000,
        ]));

        $response->assertOk();
        $response->assertSee('Подходящая картина');
        $response->assertDontSee('Чужая картина');
    }

    public function test_paintings_index_paginates_active_items(): void
    {
        $category = Category::factory()->create();

        Painting::factory()->count(6)->for($category)->create();
        Painting::factory()->for($category)->create([
            'title' => 'Картина со второй страницы',
            'slug' => 'second-page-painting',
        ]);
        Painting::factory()->for($category)->create([
            'title' => 'Скрытая картина',
            'slug' => 'inactive-painting',
            'is_active' => false,
        ]);

        $firstPage = $this->get(route('paintings.index'));
        $secondPage = $this->get(route('paintings.index', ['page' => 2]));

        $firstPage->assertOk();
        $firstPage->assertDontSee('Картина со второй страницы');
        $firstPage->assertDontSee('Скрытая картина');

        $secondPage->assertOk();
        $secondPage->assertSee('Картина со второй страницы');
        $secondPage->assertDontSee('Скрытая картина');
    }

    public function test_painting_show_displays_gallery_and_related_paintings(): void
    {
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();

        $painting = Painting::factory()->for($category)->create([
            'title' => 'Главная картина',
            'slug' => 'hero-painting',
        ]);

        PaintingGallery::factory()->count(2)->for($painting)->create();

        Painting::factory()->for($category)->create([
            'title' => 'Похожая картина',
            'slug' => 'related-painting',
        ]);

        Painting::factory()->for($otherCategory)->create([
            'title' => 'Не связанная картина',
            'slug' => 'unrelated-painting',
        ]);

        $response = $this->get(route('paintings.show', $painting));

        $response->assertOk();
        $response->assertSee('Главная картина');
        $response->assertSee('Похожая картина');
        $response->assertDontSee('Не связанная картина');
        $response->assertSee('data-painting-gallery', false);
        $response->assertSee('data-painting-thumb', false);
        $this->assertCount(2, $response->viewData('painting')->gallery);
    }
}
