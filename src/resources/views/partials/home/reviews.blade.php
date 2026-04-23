<section id="reviews-showcase">
    <div class="container">
        <div class="section-head reveal">
            <h2>{{ $reviewsShowcase['heading'] }}</h2>
            @if (!empty($reviewsShowcase['description']))
                <p>{{ $reviewsShowcase['description'] }}</p>
            @endif
        </div>

        @if (count($reviewsShowcase['items']) > 0)
            <div class="reviews-showcase reveal">
                <div class="reviews-showcase-shell">
                    <button class="reviews-arrow is-prev" type="button" aria-label="{{ __('site.home.reviews.prev') }}">
                        &larr;
                    </button>

                    <div class="reviews-rail" data-reviews-rail>
                        <div class="reviews-track" data-reviews-track>
                            @foreach ($reviewsShowcase['items'] as $review)
                                <article class="review-slide">
                                    @if ($review['image_url'])
                                        <div class="review-slide-image" style="background-image:url('{{ $review['image_url'] }}')"></div>
                                    @endif

                                    <div class="review-slide-copy">
                                        <div class="review-slide-rating" aria-label="{{ __('site.home.reviews.rating', ['rating' => $review['rating']]) }}">
                                            @for ($star = 1; $star <= 5; $star++)
                                                <span class="{{ $star <= $review['rating'] ? 'is-active' : '' }}">★</span>
                                            @endfor
                                        </div>

                                        <p>{{ $review['text'] }}</p>

                                        <div class="review-slide-meta">
                                            <strong>{{ $review['author'] }}</strong>
                                            @if ($review['city'])
                                                <span>{{ $review['city'] }}</span>
                                            @endif
                                            @if ($review['date'])
                                                <span>{{ $review['date'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <button class="reviews-arrow is-next" type="button" aria-label="{{ __('site.home.reviews.next') }}">
                        &rarr;
                    </button>
                </div>

                <div class="reviews-showcase-actions">
                    <a class="button" href="{{ $reviewsShowcase['cta_url'] }}">{{ $reviewsShowcase['cta_label'] }}</a>
                </div>
            </div>
        @else
            <div class="placeholder-shell reveal is-visible">
                <small>{{ __('site.home.reviews.eyebrow') }}</small>
                <h1>{{ __('site.home.reviews.empty.title') }}</h1>
                <p>{{ __('site.home.reviews.empty.description') }}</p>
                <div class="placeholder-actions">
                    <a class="button" href="{{ route('reviews') }}">{{ __('site.home.reviews.cta') }}</a>
                </div>
            </div>
        @endif
    </div>
</section>
