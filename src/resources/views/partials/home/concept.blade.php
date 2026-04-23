<section id="concept">
    <div class="container">
        <div class="section-head reveal">
            <h2>{{ $concept['heading'] }}</h2>
            @if (!empty($concept['description']))
                <p>{{ $concept['description'] }}</p>
            @endif
        </div>

        <div class="bento">
            <article class="panel hero-panel reveal tilt-card">
                <small>{{ $concept['panel']['eyebrow'] }}</small>
                <h3>{{ $concept['panel']['title'] }}</h3>
                <p class="concept-lead">{{ $concept['panel']['lead'] }}</p>
                <p class="concept-description">{{ $concept['panel']['description'] }}</p>

                <div class="concept-list">
                    @foreach ($concept['panel']['items'] as $item)
                        <div class="concept-list-item">{{ $item }}</div>
                    @endforeach
                </div>
            </article>

            <div class="feature-grid">
                @foreach ($concept['features'] as $feature)
                    <article class="feature-card reveal tilt-card">
                        <small>{{ $feature['eyebrow'] }}</small>
                        <h3>{{ $feature['title'] }}</h3>
                        <p>{{ $feature['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
