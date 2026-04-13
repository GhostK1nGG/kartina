@extends('layouts.app')

@section('content')
    <section class="about-page">
        <div class="container">
            <div class="about-hero reveal is-visible">
                <div class="about-copy">
                    <small>{{ $about['eyebrow'] }}</small>
                    <h1>{{ $about['name'] }}</h1>
                    <p class="about-lead">{{ $about['lead'] }}</p>
                    <p>{{ $about['description'] }}</p>

                    <div class="about-actions">
                        <a class="button" href="{{ route('paintings.index') }}">{{ __('site.about.actions.catalog') }}</a>
                        <a class="ghost-button" href="{{ route('project-request') }}">{{ __('site.about.actions.project') }}</a>
                    </div>
                </div>

                <aside class="about-portrait-shell">
                    <div class="about-portrait-frame">
                        <img class="about-portrait" src="{{ $about['portrait_url'] }}" alt="{{ $about['name'] }}">
                    </div>

                    <div class="about-portrait-note">
                        <strong>{{ $about['name'] }}</strong>
                        <span>{{ $about['education'] }}</span>
                    </div>
                </aside>
            </div>

            <div class="about-facts-grid reveal is-visible">
                @foreach ($about['details'] as $detail)
                    <article class="about-fact-card">
                        <span>{{ $detail['label'] }}</span>
                        <strong>{{ $detail['value'] }}</strong>
                    </article>
                @endforeach
            </div>

            <div class="section-head reveal is-visible">
                <h2>{{ $about['principles_heading'] }}</h2>
                <p>{{ $about['principles_description'] }}</p>
            </div>

            <div class="about-principles-grid reveal is-visible">
                @foreach ($about['principles'] as $principle)
                    <article class="about-panel">
                        <small>{{ __('site.about.principle_label') }}</small>
                        <h3>{{ $principle['title'] }}</h3>
                        <p>{{ $principle['text'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="about-story-grid reveal is-visible">
                <article class="about-panel about-panel-large">
                    <small>{{ __('site.about.story_label') }}</small>
                    <h3>{{ $about['story_heading'] }}</h3>

                    @foreach ($about['story_blocks'] as $storyBlock)
                        <p>{{ $storyBlock }}</p>
                    @endforeach
                </article>

                <article class="about-quote-card">
                    <small>{{ __('site.about.quote_label') }}</small>
                    <blockquote>{{ __('site.about.quote') }}</blockquote>
                    <div class="about-quote-meta">
                        <strong>{{ $about['name'] }}</strong>
                        <span>{{ $about['education'] }}</span>
                    </div>
                </article>
            </div>

            <div class="section-head reveal is-visible">
                <h2>{{ $about['works_heading'] }}</h2>
                <p>{{ $about['works_description'] }}</p>
            </div>

            <div class="placeholder-paintings-grid reveal is-visible">
                @foreach ($aboutWorks as $work)
                    <article class="placeholder-painting-card about-work-card">
                        <div class="placeholder-painting-media" style="background-image:url('{{ $work['image_url'] }}')"></div>

                        <div class="placeholder-painting-copy">
                            <small>{{ $work['subtitle'] }}</small>
                            <h3>{{ $work['title'] }}</h3>
                            <p>{{ $work['description'] }}</p>
                            <a class="ghost-button" href="{{ $work['url'] }}">{{ __('site.about.works_more') }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
