<section id="concept">
    <div class="container">
        <div class="section-head reveal">
            <h2>{{ $concept['heading'] }}</h2>
            <p>{{ $concept['description'] }}</p>
        </div>

        <div class="bento">
            <article class="panel hero-panel reveal tilt-card">
                <small>{{ $concept['panel']['eyebrow'] }}</small>
                <h3>{{ $concept['panel']['title'] }}</h3>
                <p>{{ $concept['panel']['description'] }}</p>

                <div class="mini-stack">
                    @foreach ($concept['panel']['mini_cards'] as $miniCard)
                        <div class="mini-card">
                            <strong>{{ $miniCard['title'] }}</strong>
                            <span>{{ $miniCard['subtitle'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <div class="feature-grid">
                @foreach ($concept['features'] as $feature)
                    <article class="feature-card reveal tilt-card">
                        <div class="icon-pill">{{ $feature['icon'] }}</div>
                        <h3>{{ $feature['title'] }}</h3>
                        <p>{{ $feature['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
