<section id="process">
    <div class="container">
        <div class="section-head reveal">
            <h2>{{ $process['heading'] }}</h2>
            <p>{{ $process['description'] }}</p>
        </div>

        <div class="process">
            @foreach ($process['steps'] as $step)
                <article class="step reveal tilt-card">
                    <div class="step-index">{{ $step['index'] }}</div>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
