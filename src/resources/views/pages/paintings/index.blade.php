@extends('layouts.app')

@section('content')
    @php
        $catalogPayload = $paintings->mapWithKeys(function ($painting) {
            $images = collect([$painting->main_image_url])
                ->merge($painting->gallery->pluck('image_url'))
                ->filter()
                ->unique()
                ->values();

            return [
                $painting->id => [
                    'id' => $painting->id,
                    'title' => $painting->title,
                    'categorySlug' => $painting->category?->slug,
                    'year' => $painting->year,
                    'size' => $painting->size,
                    'shortDescription' => $painting->short_desc,
                    'fullDescription' => $painting->full_desc ?: $painting->short_desc,
                    'priceRub' => $painting->price_rub !== null ? (float) $painting->price_rub : null,
                    'priceUsd' => $painting->price_usd !== null ? (float) $painting->price_usd : null,
                    'images' => $images->values()->all(),
                    'detailUrl' => route('paintings.show', $painting->slug),
                ],
            ];
        });
    @endphp

    <section class="placeholder-page paintings-page">
        <div class="container">
            <div class="section-head reveal is-visible">
                <h2>{{ __('site.catalog.heading') }}</h2>
            </div>

            <form class="catalog-filters reveal is-visible" method="GET" action="{{ route('paintings.index') }}">
                <div class="catalog-filters-grid">
                    <label class="filter-group">
                        <span>{{ __('site.catalog.filters.category') }}</span>
                        <select class="field filter-field" name="category">
                            <option value="">{{ __('site.catalog.filters.allCategories') }}</option>
                            @foreach ($categories as $category)
                                @php
                                    $categoryLabel = __('site.categories.' . $category->slug);
                                    if ($categoryLabel === 'site.categories.' . $category->slug) {
                                        $categoryLabel = $category->name;
                                    }
                                @endphp
                                <option value="{{ $category->slug }}" @selected($filters['category'] === $category->slug)>{{ $categoryLabel }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="filter-group">
                        <span>{{ __('site.catalog.filters.size') }}</span>
                        <select class="field filter-field" name="size">
                            <option value="">{{ __('site.catalog.filters.anySize') }}</option>
                            @foreach ($filterOptions['sizes'] as $size)
                                <option value="{{ $size }}" @selected($filters['size'] === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="filter-group">
                        <span>{{ __('site.catalog.filters.year') }}</span>
                        <select class="field filter-field" name="year">
                            <option value="">{{ __('site.catalog.filters.anyYear') }}</option>
                            @foreach ($filterOptions['years'] as $year)
                                <option value="{{ $year }}" @selected($filters['year'] === (string) $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="filter-group">
                        <span>{{ __('site.catalog.filters.priceFrom') }}</span>
                        <input class="field filter-field" type="number" min="0" step="1000" name="price_min" value="{{ $filters['price_min'] }}" placeholder="150000">
                    </label>

                    <label class="filter-group">
                        <span>{{ __('site.catalog.filters.priceTo') }}</span>
                        <input class="field filter-field" type="number" min="0" step="1000" name="price_max" value="{{ $filters['price_max'] }}" placeholder="300000">
                    </label>
                </div>

                <div class="catalog-filter-actions">
                    <button class="button" type="submit">{{ __('site.catalog.filters.apply') }}</button>
                    <a class="ghost-button" href="{{ route('paintings.index') }}">{{ __('site.catalog.filters.reset') }}</a>
                </div>
            </form>

            <div class="catalog-toolbar reveal is-visible">
                <div class="catalog-count">
                    <span>{{ __('site.catalog.found') }}</span>
                    <strong>{{ $paintings->total() }}</strong>
                </div>

                <div class="catalog-count">
                    <span>{{ __('site.catalog.page') }}</span>
                    <strong>{{ $paintings->currentPage() }}</strong>
                    <span>{{ __('site.catalog.pageOf') }}</span>
                    <strong>{{ $paintings->lastPage() }}</strong>
                </div>
            </div>

            @if ($paintings->count() > 0)
                <div class="placeholder-paintings-grid catalog-results">
                    @foreach ($paintings as $painting)
                        @php
                            $images = collect([$painting->main_image_url])
                                ->merge($painting->gallery->pluck('image_url'))
                                ->filter()
                                ->unique()
                                ->values();
                        @endphp

                        <article
                            class="placeholder-painting-card catalog-card reveal is-visible"
                            tabindex="0"
                            role="button"
                            aria-haspopup="dialog"
                            data-catalog-card
                            data-painting-id="{{ $painting->id }}"
                        >
                            <div class="placeholder-painting-media catalog-card-media" style="background-image:url('{{ $images->first() }}')">
                                @if ($images->count() > 1)
                                    <span class="catalog-card-badge" data-photo-count="{{ $images->count() }}">{{ $images->count() }} фото</span>
                                @endif
                            </div>

                            <div class="placeholder-painting-copy">
                                <small data-category-year data-category-slug="{{ $painting->category?->slug }}" data-year="{{ $painting->year }}">
                                    {{ collect([$painting->category?->name, $painting->year])->filter()->implode(' · ') }}
                                </small>

                                <h3>{{ $painting->title }}</h3>
                                <p>{{ $painting->short_desc }}</p>

                                <div class="catalog-card-meta">
                                    @if ($painting->size)
                                        <span>{{ $painting->size }}</span>
                                    @endif

                                    @if ($painting->year)
                                        <span>{{ $painting->year }}</span>
                                    @endif

                                    @if ($images->count() > 1)
                                        <span data-photo-count="{{ $images->count() }}">{{ $images->count() }} фото</span>
                                    @endif
                                </div>

                                <button class="catalog-card-link" type="button" data-open-catalog-modal data-painting-id="{{ $painting->id }}" data-stop-modal>
                                    {{ __('site.buttons.moreDetails') }}
                                </button>

                                <div class="catalog-card-actions">
                                    <form method="POST" action="{{ route('cart.add') }}" data-stop-modal>
                                        @csrf
                                        <input type="hidden" name="painting_id" value="{{ $painting->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="button" type="submit">{{ __('site.buttons.addToCart') }}</button>
                                    </form>

                                    <div
                                        class="catalog-card-price"
                                        data-price
                                        data-price-rub="{{ $painting->price_rub !== null ? (float) $painting->price_rub : '' }}"
                                        data-price-usd="{{ $painting->price_usd !== null ? (float) $painting->price_usd : '' }}"
                                    >
                                        @if ($painting->price_rub !== null)
                                            {{ number_format((float) $painting->price_rub, 0, ',', ' ') }} ₽
                                        @elseif ($painting->price_usd !== null)
                                            ${{ number_format((float) $painting->price_usd, 0, '.', ' ') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($paintings->hasPages())
                    <nav class="catalog-pagination reveal is-visible" aria-label="{{ __('site.catalog.heading') }}">
                        @if ($paintings->onFirstPage())
                            <span class="ghost-button is-disabled">{{ __('site.catalog.pagination.prev') }}</span>
                        @else
                            <a class="ghost-button" href="{{ $paintings->previousPageUrl() }}">{{ __('site.catalog.pagination.prev') }}</a>
                        @endif

                        <div class="catalog-pagination-pages">
                            @foreach ($paintings->getUrlRange(1, $paintings->lastPage()) as $page => $url)
                                @if ($page === $paintings->currentPage())
                                    <span class="catalog-page-link is-current">{{ $page }}</span>
                                @else
                                    <a class="catalog-page-link" href="{{ $url }}">{{ $page }}</a>
                                @endif
                            @endforeach
                        </div>

                        @if ($paintings->hasMorePages())
                            <a class="ghost-button" href="{{ $paintings->nextPageUrl() }}">{{ __('site.catalog.pagination.next') }}</a>
                        @else
                            <span class="ghost-button is-disabled">{{ __('site.catalog.pagination.next') }}</span>
                        @endif
                    </nav>
                @endif
            @else
                <div class="placeholder-shell reveal is-visible">
                    <small>catalog</small>
                    <h1>{{ __('site.catalog.emptyTitle') }}</h1>
                    <p>{{ __('site.catalog.emptyDescription') }}</p>

                    <div class="placeholder-actions">
                        <a class="button" href="{{ route('paintings.index') }}">{{ __('site.catalog.emptyReset') }}</a>
                        <a class="ghost-button" href="{{ route('home') }}">{{ __('site.catalog.emptyHome') }}</a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if ($paintings->count() > 0)
        <script id="catalogPaintingsData" type="application/json">@json($catalogPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>

        <div class="catalog-modal" id="catalogModal" aria-hidden="true">
            <div class="catalog-modal-backdrop" data-close-catalog-modal></div>

            <div class="catalog-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="catalogModalTitle">
                <button class="catalog-modal-close" type="button" aria-label="{{ __('site.catalog.close') }}" data-close-catalog-modal>&times;</button>

                <div class="catalog-modal-layout">
                    <div class="catalog-modal-visual">
                        <div class="catalog-modal-media">
                            <button class="catalog-modal-nav is-prev" type="button" id="catalogModalPrev" aria-label="{{ __('site.catalog.photo_prev') }}">
                                &larr;
                            </button>
                            <div class="catalog-modal-stage" id="catalogModalStage"></div>
                            <button class="catalog-modal-nav is-next" type="button" id="catalogModalNext" aria-label="{{ __('site.catalog.photo_next') }}">
                                &rarr;
                            </button>
                        </div>
                        <div class="catalog-modal-thumbs" id="catalogModalThumbs"></div>
                    </div>

                    <div class="catalog-modal-copy">
                        <small id="catalogModalSubtitle"></small>
                        <h3 id="catalogModalTitle"></h3>
                        <p id="catalogModalDescription"></p>

                        <div class="catalog-modal-meta">
                            <div class="painting-meta-card">
                                <span>{{ __('site.catalog.meta.size') }}</span>
                                <strong id="catalogModalSize">—</strong>
                            </div>

                            <div class="painting-meta-card">
                                <span>{{ __('site.catalog.meta.year') }}</span>
                                <strong id="catalogModalYear">—</strong>
                            </div>

                            <div class="painting-meta-card catalog-modal-price-card">
                                <span>{{ __('site.catalog.meta.price') }}</span>
                                <strong id="catalogModalPrice" data-price>—</strong>
                            </div>
                        </div>

                        <div class="catalog-modal-actions">
                            <form method="POST" action="{{ route('cart.add') }}" id="catalogModalCartForm">
                                @csrf
                                <input type="hidden" name="painting_id" id="catalogModalPaintingId" value="">
                                <input type="hidden" name="quantity" value="1">
                                <button class="button" type="submit">{{ __('site.buttons.addToCart') }}</button>
                            </form>

                            <a class="ghost-button" href="#" id="catalogModalPageLink">{{ __('site.buttons.openPage') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
