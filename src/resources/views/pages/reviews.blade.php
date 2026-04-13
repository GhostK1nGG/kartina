@extends('layouts.app')

@section('content')
    <section class="placeholder-page reviews-page">
        <div class="container">
            <div class="section-head reveal is-visible">
                <h2>{{ __('site.reviews.heading') }}</h2>
                <p>{{ __('site.reviews.lead') }}</p>
            </div>

            @if (session('review_success'))
                <div class="status-banner success-banner reveal is-visible">
                    {{ session('review_success') }}
                </div>
            @endif

            <div class="reviews-layout">
                <div class="reviews-list">
                    @forelse ($reviews as $review)
                        <article class="review-card reveal is-visible">
                            @if ($review->image_url)
                                <div class="review-image" style="background-image:url('{{ $review->image_url }}')"></div>
                            @endif

                            <div class="review-copy">
                                <div class="review-rating">
                                    @for ($star = 1; $star <= 5; $star++)
                                        <span class="{{ $star <= $review->rating ? 'is-active' : '' }}">★</span>
                                    @endfor
                                </div>

                                <blockquote>{{ $review->text }}</blockquote>
                                <div class="review-meta">
                                    <strong>{{ $review->author_name }}</strong>
                                    @if ($review->author_city)
                                        <span>{{ $review->author_city }}</span>
                                    @endif
                                    <span>{{ $review->created_at?->format('d.m.Y') }}</span>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="placeholder-shell reveal is-visible">
                            <small>{{ __('site.reviews.eyebrow') }}</small>
                            <h1>{{ __('site.reviews.empty.title') }}</h1>
                            <p>{{ __('site.reviews.empty.description') }}</p>
                        </div>
                    @endforelse
                </div>

                <aside class="form-page-shell review-form-shell reveal is-visible">
                    <div class="form-page-copy">
                        <small>{{ __('site.reviews.form.eyebrow') }}</small>
                        <h1>{{ __('site.reviews.form.title') }}</h1>
                        <p>{{ __('site.reviews.form.description') }}</p>
                    </div>

                    <form class="form-page-form" method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input class="field" type="text" name="author_name" placeholder="{{ __('site.reviews.form.fields.name') }}" value="{{ old('author_name') }}">
                        @error('author_name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror

                        <input class="field" type="text" name="author_city" placeholder="{{ __('site.reviews.form.fields.city') }}" value="{{ old('author_city') }}">
                        @error('author_city')
                            <p class="form-error">{{ $message }}</p>
                        @enderror

                        <select class="field" name="rating">
                            <option value="">{{ __('site.reviews.form.fields.rating') }}</option>
                            @for ($rating = 5; $rating >= 1; $rating--)
                                <option value="{{ $rating }}" @selected((string) old('rating') === (string) $rating)>{{ __('site.reviews.form.rating_option', ['rating' => $rating]) }}</option>
                            @endfor
                        </select>
                        @error('rating')
                            <p class="form-error">{{ $message }}</p>
                        @enderror

                        <textarea class="field" name="text" placeholder="{{ __('site.reviews.form.fields.text') }}">{{ old('text') }}</textarea>
                        @error('text')
                            <p class="form-error">{{ $message }}</p>
                        @enderror

                        <label class="upload-field">
                            <span>{{ __('site.reviews.form.fields.image') }}</span>
                            <input class="field" type="file" name="image" accept="image/*">
                        </label>
                        @error('image')
                            <p class="form-error">{{ $message }}</p>
                        @enderror

                        <button class="button" type="submit">{{ __('site.reviews.form.submit') }}</button>
                    </form>
                </aside>
            </div>
        </div>
    </section>
@endsection
