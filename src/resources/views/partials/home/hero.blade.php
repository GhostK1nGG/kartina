<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy reveal">
            <span class="eyebrow">{{ $hero['eyebrow'] }}</span>
            <h1>{{ $hero['title'] }}</h1>
            <p>{{ $hero['description'] }}</p>

            <div class="hero-actions">
                <a class="button" href="#collection">{{ __('site.home.hero.actions.collection') }}</a>
                <a class="ghost-button" href="#concept">{{ __('site.home.hero.actions.concept') }}</a>
            </div>

            <div class="hero-metrics">
                @foreach ($hero['metrics'] as $metric)
                    <div class="metric">
                        <strong>{{ $metric['label'] }}</strong>
                        <span>{{ $metric['description'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="hero-stage reveal" id="heroStage">
            @foreach ($hero['floating_cards'] as $card)
                <article class="floating-card {{ $card['class'] }} tilt-card">
                    <div class="image" style="background-image:url('{{ $card['image'] }}')"></div>
                    <div class="label">
                        <div>
                            <h3>{{ $card['title'] }}</h3>
                            <span>{{ $card['subtitle'] }}</span>
                        </div>

                        @if (!empty($card['tag']))
                            <span class="gallery-tag">{{ __('site.home.hero.featured_tag') }}</span>
                        @endif
                    </div>
                </article>
            @endforeach

            <div class="glass-note">
                <strong>{{ $hero['note']['title'] }}</strong>
                <p>{{ $hero['note']['description'] }}</p>
            </div>
        </div>
    </div>
</section>
