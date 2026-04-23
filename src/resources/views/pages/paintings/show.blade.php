@extends('layouts.app')

@section('content')
    @php
        $galleryImages = collect([$painting->main_image_url])
            ->merge($painting->gallery->pluck('image_url'))
            ->filter()
            ->unique()
            ->values();
    @endphp

    <section class="placeholder-page painting-detail-page">
        <div class="container">
            <div class="painting-detail-shell reveal is-visible">
                <div class="painting-detail-visual" data-painting-gallery>
                    <div
                        class="painting-detail-media"
                        data-painting-stage
                        data-image-url="{{ $galleryImages->first() }}"
                        style="background-image:url('{{ $galleryImages->first() }}')"
                        aria-label="{{ $painting->title }}"
                    >
                        @if ($galleryImages->count() > 1)
                            <button class="painting-gallery-nav is-prev" type="button" data-painting-prev aria-label="{{ __('site.catalog.photo_prev') }}">
                                &larr;
                            </button>
                            <button class="painting-gallery-nav is-next" type="button" data-painting-next aria-label="{{ __('site.catalog.photo_next') }}">
                                &rarr;
                            </button>
                        @endif
                    </div>

                    @if ($galleryImages->count() > 1)
                        <div class="painting-gallery-grid">
                            @foreach ($galleryImages as $imageUrl)
                                <button
                                    class="painting-gallery-thumb{{ $loop->first ? ' is-active' : '' }}"
                                    type="button"
                                    data-painting-thumb
                                    data-image-url="{{ $imageUrl }}"
                                    aria-label="{{ $painting->title }} {{ __('site.catalog.photo_next') }} {{ $loop->iteration }}"
                                    @if ($loop->first) aria-current="true" @endif
                                    style="background-image:url('{{ $imageUrl }}')"
                                ></button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="painting-detail-copy">
                    <small data-category-year data-category-slug="{{ $painting->category?->slug }}" data-year="{{ $painting->year }}">
                        {{ collect([$painting->category?->name, $painting->year])->filter()->implode(' · ') }}
                    </small>

                    <h1>{{ $painting->title }}</h1>
                    <p>{{ $painting->full_desc ?: $painting->short_desc }}</p>

                    <div class="painting-meta-grid">
                        @if ($painting->size)
                            <div class="painting-meta-card">
                                <span>{{ __('site.catalog.meta.size') }}</span>
                                <strong>{{ $painting->size }}</strong>
                            </div>
                        @endif

                        @if ($painting->year)
                            <div class="painting-meta-card">
                                <span>{{ __('site.catalog.meta.year') }}</span>
                                <strong>{{ $painting->year }}</strong>
                            </div>
                        @endif

                        <div class="painting-meta-card">
                            <span>{{ __('site.catalog.meta.price') }}</span>
                            <strong
                                data-price
                                data-price-rub="{{ $painting->price_rub !== null ? (float) $painting->price_rub : '' }}"
                                data-price-usd="{{ $painting->price_usd !== null ? (float) $painting->price_usd : '' }}"
                            >
                                @if ($painting->price_rub !== null)
                                    {{ number_format((float) $painting->price_rub, 0, ',', ' ') }} ₽
                                @elseif ($painting->price_usd !== null)
                                    ${{ number_format((float) $painting->price_usd, 0, '.', ' ') }}
                                @else
                                    —
                                @endif
                            </strong>
                        </div>
                    </div>

                    <div class="placeholder-actions">
                        <form method="POST" action="{{ route('cart.add') }}">
                            @csrf
                            <input type="hidden" name="painting_id" value="{{ $painting->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button class="button" type="submit">{{ __('site.buttons.addToCart') }}</button>
                        </form>

                        <form method="POST" action="{{ route('cart.add') }}">
                            @csrf
                            <input type="hidden" name="painting_id" value="{{ $painting->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="redirect_to" value="cart">
                            <button class="button" type="submit">{{ __('site.nav.orderProject') }}</button>
                        </form>

                        <a class="ghost-button" href="{{ route('paintings.index') }}">{{ __('site.buttons.back') }}</a>
                    </div>
                </div>
            </div>

            @if ($relatedPaintings->isNotEmpty())
                <div class="painting-related reveal is-visible">
                    <div class="section-head">
                        <h2>{{ __('site.catalog.related_heading') }}</h2>
                        <p>{{ __('site.catalog.related_description') }}</p>
                    </div>

                    <div class="placeholder-paintings-grid related-paintings-grid">
                        @foreach ($relatedPaintings as $relatedPainting)
                            <article
                                class="placeholder-painting-card catalog-card reveal is-visible"
                                tabindex="0"
                                role="link"
                                data-related-painting-card
                                data-related-url="{{ route('paintings.show', $relatedPainting->slug) }}"
                            >
                                <div class="placeholder-painting-media" style="background-image:url('{{ $relatedPainting->main_image_url }}')"></div>

                                <div class="placeholder-painting-copy">
                                    <small data-category-year data-category-slug="{{ $relatedPainting->category?->slug }}" data-year="{{ $relatedPainting->year }}">
                                        {{ collect([$relatedPainting->category?->name, $relatedPainting->year])->filter()->implode(' · ') }}
                                    </small>

                                    <h3>{{ $relatedPainting->title }}</h3>
                                    <p>{{ $relatedPainting->short_desc }}</p>

                                    <div class="catalog-card-meta">
                                        @if ($relatedPainting->size)
                                            <span>{{ $relatedPainting->size }}</span>
                                        @endif

                                        @if ($relatedPainting->year)
                                            <span>{{ $relatedPainting->year }}</span>
                                        @endif
                                    </div>

                                    <div class="catalog-card-actions">
                                        <form method="POST" action="{{ route('cart.add') }}">
                                            @csrf
                                            <input type="hidden" name="painting_id" value="{{ $relatedPainting->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button class="button" type="submit">{{ __('site.buttons.addToCart') }}</button>
                                        </form>

                                        <div
                                            class="catalog-card-price"
                                            data-price
                                            data-price-rub="{{ $relatedPainting->price_rub !== null ? (float) $relatedPainting->price_rub : '' }}"
                                            data-price-usd="{{ $relatedPainting->price_usd !== null ? (float) $relatedPainting->price_usd : '' }}"
                                        >
                                            @if ($relatedPainting->price_rub !== null)
                                                {{ number_format((float) $relatedPainting->price_rub, 0, ',', ' ') }} ₽
                                            @elseif ($relatedPainting->price_usd !== null)
                                                ${{ number_format((float) $relatedPainting->price_usd, 0, '.', ' ') }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>

                                    <a class="catalog-card-link" href="{{ route('paintings.show', $relatedPainting->slug) }}">{{ __('site.buttons.openPage') }}</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
