<section class="hero">
    @php
        $widgetPaintings = array_values($gallery['paintings'] ?? []);
        $featuredPainting = $widgetPaintings[0] ?? null;
        $listPaintings = array_slice($widgetPaintings, 1);
        $widgetTotal = max(count($widgetPaintings), 1);
        $selectedLabel = app()->getLocale() === 'en' ? 'Selected Works' : 'Избранные работы';
        $catalogLabel = app()->getLocale() === 'en' ? 'View All' : 'Смотреть все';
        $phoneAriaLabel = app()->getLocale() === 'en' ? 'Showcase phone widget' : 'Телефонный виджет с работами';
    @endphp

    <div class="hero-atmosphere" aria-hidden="true">
        <span class="hero-beam hero-beam-1"></span>
        <span class="hero-beam hero-beam-2"></span>
        <span class="hero-beam hero-beam-3"></span>
        <span class="hero-cloudbank hero-cloudbank-back"></span>
        <span class="hero-cloudbank hero-cloudbank-mid"></span>
        <span class="hero-cloudbank hero-cloudbank-front"></span>
        <span class="hero-cloudmist hero-cloudmist-left"></span>
        <span class="hero-cloudmist hero-cloudmist-right"></span>
        <span class="hero-cloudglow hero-cloudglow-top"></span>
        <span class="hero-cloudglow hero-cloudglow-bottom"></span>
    </div>

    <div class="container hero-grid">
        <div class="hero-copy reveal">
            @if (!empty($hero['eyebrow']))
                <span class="eyebrow">{{ $hero['eyebrow'] }}</span>
            @endif
            <h1>{{ $hero['title'] }}</h1>
            <p>{{ $hero['description'] }}</p>

            <div class="hero-actions">
                <a class="button" href="#collection">{{ __('site.home.hero.actions.collection') }}</a>
                <a class="ghost-button" href="#concept">{{ __('site.home.hero.actions.concept') }}</a>
            </div>
        </div>

        <div class="hero-device reveal" id="heroStage">
            <div class="hero-phone" aria-label="{{ $phoneAriaLabel }}">
                <span class="hero-phone-button hero-phone-button-action" aria-hidden="true"></span>
                <span class="hero-phone-button hero-phone-button-volume-up" aria-hidden="true"></span>
                <span class="hero-phone-button hero-phone-button-volume-down" aria-hidden="true"></span>
                <span class="hero-phone-button hero-phone-button-power" aria-hidden="true"></span>

                <div class="hero-phone-shell">
                    <div class="hero-phone-screen">
                        <span class="hero-phone-island" aria-hidden="true"></span>

                        <div class="hero-phone-stars" aria-hidden="true">
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                            <span class="hero-phone-star"></span>
                        </div>

                        <section class="hero-widget">
                            <div class="hero-widget-head">
                                <span class="hero-widget-kicker">{{ $selectedLabel }}</span>
                                <span class="hero-widget-counter">
                                    <span data-widget-current>01</span>
                                    <span>/ {{ str_pad((string) $widgetTotal, 2, '0', STR_PAD_LEFT) }}</span>
                                </span>
                            </div>

                            <div class="hero-widget-scroll" data-widget-scroll>
                                @if ($featuredPainting)
                                    @php
                                        $featuredTagString = str_replace([' · ', ' 路 '], ' / ', (string) ($featuredPainting['chip'] ?? ''));
                                        $featuredTags = array_values(array_filter(explode(' / ', $featuredTagString)));
                                        $featuredMeta = implode(' / ', array_slice($featuredTags, 0, 2));
                                    @endphp

                                    <a href="{{ $featuredPainting['detail_url'] }}" class="hero-widget-card hero-widget-card-featured" data-widget-card>
                                        <div class="hero-widget-media hero-widget-media-featured">
                                            <img src="{{ $featuredPainting['image_url'] }}" alt="{{ $featuredPainting['title'] }}">
                                        </div>

                                        <div class="hero-widget-copy">
                                            @if ($featuredMeta !== '')
                                                <span class="hero-widget-card-meta">{{ $featuredMeta }}</span>
                                            @endif

                                            <h3>{{ $featuredPainting['title'] }}</h3>
                                            <p>{{ $featuredPainting['story_preview'] ?? $featuredPainting['excerpt'] }}</p>

                                            @if ($featuredTagString !== '')
                                                <div class="hero-widget-meta-line">{{ $featuredTagString }}</div>
                                            @endif
                                        </div>
                                    </a>
                                @endif

                                @foreach ($listPaintings as $painting)
                                    @php
                                        $tagString = str_replace([' · ', ' 路 '], ' / ', (string) ($painting['chip'] ?? ''));
                                        $tags = array_values(array_filter(explode(' / ', $tagString)));
                                    @endphp

                                    <a href="{{ $painting['detail_url'] }}" class="hero-widget-card hero-widget-card-compact" data-widget-card>
                                        <div class="hero-widget-media hero-widget-media-compact">
                                            <img src="{{ $painting['image_url'] }}" alt="{{ $painting['title'] }}">
                                        </div>

                                        <div class="hero-widget-copy">
                                            <h3>{{ $painting['title'] }}</h3>
                                            <p>{{ $painting['excerpt'] }}</p>

                                            @if ($tags !== [])
                                                <div class="hero-widget-tags">
                                                    @foreach ($tags as $tag)
                                                        <span>{{ $tag }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <div class="hero-widget-footer">
                                <a class="hero-widget-cta" href="{{ route('paintings.index') }}">{{ $catalogLabel }}</a>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
