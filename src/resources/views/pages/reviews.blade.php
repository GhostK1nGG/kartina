@extends('layouts.app')

@section('content')
    @php
        $reviewFormTitle = app()->getLocale() === 'en'
            ? 'Share Your Impression'
            : 'Поделитесь впечатлением';

        $reviewFormDescription = app()->getLocale() === 'en'
            ? 'Tell us what stayed with you after meeting the work. A warm, sincere review helps other collectors feel the atmosphere before they make their choice.'
            : 'Расскажите, что вы почувствовали после знакомства с работой. Тёплый и искренний отзыв помогает другим людям почувствовать атмосферу картин ещё до покупки.';
    @endphp

    <section class="placeholder-page reviews-page">
        <div class="container">
            <div class="section-head reveal is-visible">
                <h2>{{ __('site.reviews.heading') }}</h2>
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
                        <h1>{{ $reviewFormTitle }}</h1>
                        <p>{{ $reviewFormDescription }}</p>
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
                                <option value="{{ $rating }}" @selected((string) old('rating') === (string) $rating)>{{ $rating }} ⭐</option>
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
