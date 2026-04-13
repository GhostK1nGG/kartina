<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Painting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'category' => $request->string('category')->toString(),
            'price_min' => $request->input('price_min'),
            'price_max' => $request->input('price_max'),
            'size' => $request->string('size')->toString(),
            'year' => $request->string('year')->toString(),
        ];

        $paintings = Painting::query()
            ->with(['category', 'gallery'])
            ->active()
            ->when($filters['category'], function (Builder $query, string $categorySlug) {
                $query->whereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('slug', $categorySlug));
            })
            ->when($filters['price_min'] !== null && $filters['price_min'] !== '', function (Builder $query) use ($filters) {
                $query->where('price_rub', '>=', (float) $filters['price_min']);
            })
            ->when($filters['price_max'] !== null && $filters['price_max'] !== '', function (Builder $query) use ($filters) {
                $query->where('price_rub', '<=', (float) $filters['price_max']);
            })
            ->when($filters['size'], fn (Builder $query, string $size) => $query->where('size', $size))
            ->when($filters['year'], fn (Builder $query, string $year) => $query->where('year', $year))
            ->orderByDesc('is_featured')
            ->orderBy('title')
            ->paginate(6)
            ->withQueryString();

        return view('pages.paintings.index', [
            'pageKey' => 'paintings',
            'seoTitle' => __('site.meta.catalog.title'),
            'seoDescription' => __('site.meta.catalog.description'),
            'paintings' => $paintings,
            'categories' => Category::query()->orderBy('sort_order')->orderBy('name')->get(),
            'filterOptions' => [
                'sizes' => Painting::query()->active()->whereNotNull('size')->distinct()->orderBy('size')->pluck('size'),
                'years' => Painting::query()->active()->whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year'),
            ],
            'filters' => $filters,
        ]);
    }

    public function show(Painting $slug): View
    {
        $painting = $slug->loadMissing(['category', 'gallery']);

        $relatedPaintings = Painting::query()
            ->with(['category', 'gallery'])
            ->active()
            ->where('category_id', $painting->category_id)
            ->whereKeyNot($painting->getKey())
            ->orderByDesc('is_featured')
            ->orderBy('title')
            ->limit(3)
            ->get();

        return view('pages.paintings.show', [
            'pageKey' => 'painting',
            'seoTitle' => $painting->title,
            'seoDescription' => $painting->short_desc ?: __('site.meta.catalog.description'),
            'painting' => $painting,
            'relatedPaintings' => $relatedPaintings,
        ]);
    }
}
