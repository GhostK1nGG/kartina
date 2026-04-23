<section id="collection">
    <div class="container">
        <div class="section-head reveal">
            <h2>{{ $gallery['heading'] }}</h2>
            @if (!empty($gallery['description']))
                <p>{{ $gallery['description'] }}</p>
            @endif
        </div>

        @if (count($gallery['paintings']) > 0)
            <div class="gallery-grid">
                @foreach ($gallery['paintings'] as $painting)
                    @php
                        $payload = [
                            'title' => $painting['title'],
                            'chip' => $painting['chip'],
                            'description' => $painting['story'],
                            'detailUrl' => $painting['detail_url'],
                            'images' => $painting['images'],
                        ];
                    @endphp

                    <article
                        class="gallery-card {{ $painting['size'] }} reveal tilt-card flip-toggle"
                        tabindex="0"
                        data-slug="{{ $painting['slug'] }}"
                    >
                        <div class="gallery-flip">
                            <div class="gallery-face gallery-front">
                                <div class="frame-art">
                                    <div class="gallery-media" style="background-image:url('{{ $painting['image_url'] }}')"></div>
                                    <div class="gallery-info">
                                        <div>
                                            <h3>{{ $painting['title'] }}</h3>
                                            <p>{{ $painting['excerpt'] }}</p>
                                        </div>
                                        <span class="gallery-tag">{{ $gallery['flip_label'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="gallery-face gallery-back">
                                <span class="back-chip">{{ $painting['chip'] }}</span>
                                <h3>{{ $painting['title'] }}</h3>
                                <p>{{ $painting['story_preview'] }}</p>
                                <div class="gallery-back-actions">
                                    <button class="button open-gallery-modal" type="button">{{ __('site.buttons.moreDetails') }}</button>
                                </div>
                            </div>
                        </div>

                        <script class="gallery-payload" type="application/json">{!! json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
                    </article>
                @endforeach
            </div>

            <div class="gallery-footer reveal is-visible">
                <a class="button" href="{{ route('paintings.index') }}">{{ $gallery['catalog_cta_label'] }}</a>
            </div>
        @else
            <div class="placeholder-shell reveal is-visible">
                <small>{{ __('site.home.gallery.eyebrow') }}</small>
                <h1>{{ __('site.home.gallery.empty.title') }}</h1>
                <p>{{ __('site.home.gallery.empty.description') }}</p>
                <div class="placeholder-actions">
                    <a class="button" href="{{ route('paintings.index') }}">{{ __('site.home.gallery.empty.catalog') }}</a>
                    <a class="ghost-button" href="/admin/paintings">{{ __('site.home.gallery.empty.admin') }}</a>
                </div>
            </div>
        @endif
    </div>

    <div class="gallery-modal" id="galleryModal" aria-hidden="true">
        <div class="gallery-modal-backdrop" data-close-gallery-modal></div>
        <div class="gallery-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="galleryModalTitle">
            <button class="gallery-modal-close" type="button" aria-label="{{ __('site.catalog.close') }}" data-close-gallery-modal>&times;</button>

            <div class="gallery-modal-layout">
                <div class="gallery-modal-media">
                    <button class="gallery-modal-nav is-prev" type="button" aria-label="{{ __('site.catalog.photo_prev') }}">&lt;</button>
                    <div class="gallery-modal-stage" id="galleryModalStage"></div>
                    <button class="gallery-modal-nav is-next" type="button" aria-label="{{ __('site.catalog.photo_next') }}">&gt;</button>
                    <div class="gallery-modal-thumbs" id="galleryModalThumbs"></div>
                </div>

                <div class="gallery-modal-copy">
                    <span class="back-chip" id="galleryModalChip"></span>
                    <h3 id="galleryModalTitle"></h3>
                    <p id="galleryModalDescription"></p>
                </div>
            </div>
        </div>
    </div>
</section>
