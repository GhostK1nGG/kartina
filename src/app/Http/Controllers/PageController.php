<?php

namespace App\Http\Controllers;

use App\Models\Painting;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $works = Painting::query()
            ->active()
            ->orderByDesc('is_featured')
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get()
            ->map(fn (Painting $painting) => [
                'title' => $painting->title,
                'subtitle' => collect([
                    $this->categoryLabel($painting->category?->slug, $painting->category?->name),
                    $painting->year,
                ])->filter()->implode(' · '),
                'description' => $painting->short_desc ?: __('site.about.works_default_description'),
                'image_url' => $painting->main_image_url,
                'url' => route('paintings.show', $painting),
            ])
            ->all();

        if ($works === []) {
            $fallbackWorks = trans('site.about.fallback_works');

            $works = [
                [
                    ...$fallbackWorks[0],
                    'image_url' => asset('assets/images/home/gallery-after-rain.jpg'),
                    'url' => route('paintings.index'),
                ],
                [
                    ...$fallbackWorks[1],
                    'image_url' => asset('assets/images/home/gallery-ember-field.jpg'),
                    'url' => route('paintings.index'),
                ],
                [
                    ...$fallbackWorks[2],
                    'image_url' => asset('assets/images/home/gallery-quiet-dust.jpg'),
                    'url' => route('paintings.index'),
                ],
            ];
        }

        return view('pages.about', [
            'pageKey' => 'about',
            'seoTitle' => __('site.meta.about.title'),
            'seoDescription' => __('site.meta.about.description'),
            'about' => [
                'eyebrow' => __('site.about.eyebrow'),
                'name' => __('site.about.name'),
                'education' => __('site.about.education'),
                'portrait_url' => asset('assets/images/about/artist-photo.jpg'),
                'lead' => __('site.about.lead'),
                'description' => __('site.about.description'),
                'details' => trans('site.about.details'),
                'principles_heading' => __('site.about.principles_heading'),
                'principles_description' => __('site.about.principles_description'),
                'principles' => trans('site.about.principles'),
                'story_heading' => __('site.about.story_heading'),
                'story_blocks' => trans('site.about.story_blocks'),
                'works_heading' => __('site.about.works_heading'),
                'works_description' => __('site.about.works_description'),
            ],
            'aboutWorks' => $works,
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
