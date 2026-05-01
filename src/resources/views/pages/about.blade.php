@extends('layouts.app')

@section('content')
    @php
        $artistContacts = app()->getLocale() === 'en'
            ? [
                ['label' => 'Email', 'text' => 'Karulin.cherry@yahoo.com', 'href' => 'mailto:Karulin.cherry@yahoo.com'],
                ['label' => 'VK', 'text' => '@heavenly_goddess', 'href' => 'https://vk.com/heavenly_goddess'],
                ['label' => 'Telegram', 'text' => '@Ka_i_Na', 'href' => 'https://t.me/Ka_i_Na'],
                ['label' => 'Instagram', 'text' => '@kerri_nessa', 'href' => 'https://www.instagram.com/kerri_nessa/'],
            ]
            : [
                ['label' => 'Почта', 'text' => 'Karulin.cherry@yahoo.com', 'href' => 'mailto:Karulin.cherry@yahoo.com'],
                ['label' => 'ВК', 'text' => '@heavenly_goddess', 'href' => 'https://vk.com/heavenly_goddess'],
                ['label' => 'Телеграм', 'text' => '@Ka_i_Na', 'href' => 'https://t.me/Ka_i_Na'],
                ['label' => 'Инстаграм', 'text' => '@kerri_nessa', 'href' => 'https://www.instagram.com/kerri_nessa/'],
            ];

        $contactsButtonLabel = app()->getLocale() === 'en' ? 'My Contacts' : 'Мои контакты';
        $contactsModalTitle = app()->getLocale() === 'en' ? 'Contact the Artist' : 'Связаться с художником';
        $contactsModalLead = app()->getLocale() === 'en'
            ? 'Choose the channel that is most convenient for you.'
            : 'Выберите удобный способ связи.';
    @endphp

    <section class="about-page">
        <div class="container">
            <div class="about-hero reveal is-visible">
                <div class="about-copy">
                    <small>{{ $about['eyebrow'] }}</small>
                    <h1>{{ $about['title'] }}</h1>
                    <p class="about-lead">{{ $about['lead'] }}</p>
                    <p>{{ $about['description'] }}</p>

                    @if (!empty($about['intro_blocks']))
                        <div class="about-manifesto">
                            @foreach ($about['intro_blocks'] as $introBlock)
                                <p>{{ $introBlock }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="about-actions">
                        <a class="button" href="{{ route('paintings.index') }}">{{ __('site.about.actions.catalog') }}</a>
                        <a class="ghost-button" href="{{ route('project-request') }}">{{ __('site.about.actions.project') }}</a>
                    </div>
                </div>

                <aside class="about-portrait-shell">
                    <div class="about-portrait-frame">
                        <img class="about-portrait" src="{{ $about['portrait_url'] }}" alt="{{ $about['name'] }}">
                    </div>

                    <div class="about-portrait-overlay">
                        <small>{{ $about['cover_label'] }}</small>
                        <strong>{{ $about['cover_title'] }}</strong>
                        <span>{{ $about['cover_caption'] }}</span>
                    </div>

                    <div class="about-portrait-note">
                        <button class="ghost-button about-contact-trigger" type="button" data-open-about-contacts>
                            {{ $contactsButtonLabel }}
                        </button>
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
                @if (!empty($about['principles_description']))
                    <p>{{ $about['principles_description'] }}</p>
                @endif
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
                    <small>{{ $about['story_label'] }}</small>
                    <h3>{{ $about['story_heading'] }}</h3>

                    @foreach ($about['story_blocks'] as $storyBlock)
                        <p>{{ $storyBlock }}</p>
                    @endforeach
                </article>

                <article class="about-quote-card">
                    <small>{{ $about['quote_label'] }}</small>
                    <blockquote>{{ $about['quote'] }}</blockquote>
                    <div class="about-quote-meta">
                        <strong>{{ $about['name'] }}</strong>
                        <span>{{ $about['quote_meta'] }}</span>
                    </div>
                </article>
            </div>

            @if ($aboutWorks !== [])
                <div class="section-head reveal is-visible">
                    <h2>{{ $about['works_heading'] }}</h2>
                    @if (!empty($about['works_description']))
                        <p>{{ $about['works_description'] }}</p>
                    @endif
                </div>

                <div class="placeholder-paintings-grid reveal is-visible">
                    @foreach ($aboutWorks as $work)
                        <article class="placeholder-painting-card about-work-card">
                            <div class="placeholder-painting-media" style="background-image:url('{{ $work['image_url'] }}')"></div>

                            <div class="placeholder-painting-copy">
                                <small>{{ $work['subtitle'] }}</small>
                                <h3>{{ $work['title'] }}</h3>
                                <p>{{ $work['description'] }}</p>
                                <a class="ghost-button" href="{{ $work['url'] }}">{{ $about['works_more'] }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <div class="about-contact-modal" id="aboutContactModal" aria-hidden="true">
        <div class="about-contact-modal-backdrop" data-close-about-contacts></div>
        <div class="about-contact-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="aboutContactModalTitle">
            <button class="about-contact-modal-close" type="button" aria-label="Close" data-close-about-contacts>&times;</button>

            <div class="about-contact-modal-copy">
                <small>{{ $contactsButtonLabel }}</small>
                <h3 id="aboutContactModalTitle">{{ $contactsModalTitle }}</h3>
                <p>{{ $contactsModalLead }}</p>
            </div>

            <div class="about-contact-links">
                @foreach ($artistContacts as $contact)
                    <a class="about-contact-link" href="{{ $contact['href'] }}" target="_blank" rel="noopener noreferrer">
                        <span>{{ $contact['label'] }}</span>
                        <strong>{{ $contact['text'] }}</strong>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
