<?php

namespace App\Http\Controllers;

use App\Models\Painting;
use App\Models\Review;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredPaintings = Painting::query()
            ->with(['category', 'gallery'])
            ->active()
            ->featured()
            ->orderByDesc('updated_at')
            ->orderBy('title')
            ->limit(5)
            ->get();

        $gallerySizes = ['large', 'medium', 'small', 'small', 'small'];

        $galleryPaintings = $featuredPaintings->values()->map(function (Painting $painting, int $index) use ($gallerySizes) {
            $images = collect([$painting->main_image_url])
                ->merge($painting->gallery->pluck('image_url'))
                ->filter()
                ->unique()
                ->values()
                ->all();

            return [
                'slug' => $painting->slug,
                'size' => $gallerySizes[$index] ?? 'small',
                'title' => $painting->title,
                'excerpt' => $painting->short_desc ?: Str::limit(strip_tags((string) $painting->full_desc), 110),
                'chip' => trim(implode(' · ', array_filter([
                    $this->categoryLabel($painting->category?->slug, $painting->category?->name),
                    $painting->year,
                    $painting->size,
                ]))),
                'story' => $painting->full_desc ?: $painting->short_desc ?: __('site.messages.description_coming_soon'),
                'story_preview' => Str::limit($painting->full_desc ?: $painting->short_desc ?: __('site.messages.description_coming_soon'), 180),
                'image_url' => $painting->main_image_url,
                'detail_url' => route('paintings.show', $painting),
                'images' => $images,
            ];
        })->all();

        $publishedReviews = Review::query()
            ->published()
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Review $review) => [
                'author' => $review->author_name,
                'city' => $review->author_city,
                'text' => Str::limit($review->text, 220),
                'rating' => $review->rating,
                'date' => $review->created_at?->format('d.m.Y'),
                'image_url' => $review->image_url,
            ])
            ->values()
            ->all();

        return view('home', [
            'pageKey' => 'home',
            'seoTitle' => __('site.meta.home.title'),
            'seoDescription' => __('site.meta.home.description'),
            'hero' => [
                'eyebrow' => __('site.home.hero.eyebrow'),
                'title' => __('site.home.hero.title'),
                'description' => __('site.home.hero.description'),
                'metrics' => trans('site.home.hero.metrics'),
                'floating_cards' => [
                    [
                        'class' => 'large',
                        'title' => $featuredPaintings->get(0)?->title ?? __('site.home.hero.defaults.featured.title'),
                        'subtitle' => $featuredPaintings->get(0)?->size ?? __('site.home.hero.defaults.featured.subtitle'),
                        'tag' => 'featured',
                        'image' => $featuredPaintings->get(0)?->main_image_url ?? asset('assets/images/home/hero-night-bloom.jpg'),
                    ],
                    [
                        'class' => 'tall',
                        'title' => $featuredPaintings->get(1)?->title ?? __('site.home.hero.defaults.gallery.title'),
                        'subtitle' => $featuredPaintings->get(1)?->category?->name
                            ? $this->categoryLabel($featuredPaintings->get(1)?->category?->slug, $featuredPaintings->get(1)?->category?->name)
                            : __('site.home.hero.defaults.gallery.subtitle'),
                        'tag' => null,
                        'image' => $featuredPaintings->get(1)?->main_image_url ?? asset('assets/images/home/hero-amber-trace.jpg'),
                    ],
                    [
                        'class' => 'wide',
                        'title' => $featuredPaintings->get(2)?->title ?? __('site.home.hero.defaults.collection.title'),
                        'subtitle' => $featuredPaintings->get(2)?->year ?? __('site.home.hero.defaults.collection.subtitle'),
                        'tag' => null,
                        'image' => $featuredPaintings->get(2)?->main_image_url ?? asset('assets/images/home/hero-soft-horizon.jpg'),
                    ],
                ],
                'note' => [
                    'title' => __('site.home.hero.note.title'),
                    'description' => __('site.home.hero.note.description'),
                ],
            ],
            'concept' => [
                'heading' => __('site.home.concept.heading'),
                'description' => __('site.home.concept.description'),
                'panel' => [
                    'eyebrow' => __('site.home.concept.panel.eyebrow'),
                    'title' => __('site.home.concept.panel.title'),
                    'description' => __('site.home.concept.panel.description'),
                    'mini_cards' => trans('site.home.concept.panel.mini_cards'),
                ],
                'features' => trans('site.home.concept.features'),
            ],
            'gallery' => [
                'heading' => __('site.home.gallery.heading'),
                'description' => __('site.home.gallery.description'),
                'paintings' => $galleryPaintings,
            ],
            'reviewsShowcase' => [
                'heading' => __('site.home.reviews.heading'),
                'description' => __('site.home.reviews.description'),
                'items' => $publishedReviews,
                'cta_label' => __('site.home.reviews.cta'),
                'cta_url' => route('reviews'),
            ],
            'contact' => [
                'quote' => [
                    'eyebrow' => __('site.home.contact.quote.eyebrow'),
                    'text' => __('site.home.contact.quote.text'),
                    'author' => __('site.home.contact.quote.author'),
                    'role' => __('site.home.contact.quote.role'),
                ],
                'form' => [
                    'eyebrow' => __('site.home.contact.form.eyebrow'),
                    'front_title' => __('site.home.contact.form.front_title'),
                    'front_description' => __('site.home.contact.form.front_description'),
                    'front_button' => __('site.home.contact.form.front_button'),
                    'back_title' => __('site.home.contact.form.back_title'),
                    'back_description' => __('site.home.contact.form.back_description'),
                    'submit_label' => __('site.home.contact.form.submit_label'),
                ],
            ],
        ]);
    }

    private function categoryLabel(?string $slug, ?string $fallback = null): ?string
    {
        if (!$slug) {
            return $fallback;
        }

        $key = "site.categories.{$slug}";
        $translated = __($key);

        return $translated === $key ? $fallback : $translated;
    }
}
