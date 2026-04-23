@extends('layouts.app')

@section('content')
    <section class="placeholder-page cart-page">
        <div class="container">
            <div class="section-head reveal is-visible">
                <h2>{{ __('site.cart.heading') }}</h2>
            </div>

            @if (session('order_success'))
                <div class="status-banner success-banner reveal is-visible">
                    {{ session('order_success') }}
                </div>
            @endif

            @if (session('cart_status'))
                <div class="status-banner reveal is-visible">
                    {{ session('cart_status') }}
                </div>
            @endif

            @if ($errors->has('cart'))
                <div class="status-banner error-banner reveal is-visible">
                    {{ $errors->first('cart') }}
                </div>
            @endif

            @if ($cart['is_empty'])
                <div class="placeholder-shell reveal is-visible">
                    <small>{{ __('site.cart.eyebrow') }}</small>
                    <h1>{{ __('site.cart.empty.title') }}</h1>
                    <p>{{ __('site.cart.empty.description') }}</p>

                    <div class="placeholder-actions">
                        <a class="button" href="{{ route('paintings.index') }}">{{ __('site.buttons.openCatalog') }}</a>
                        <a class="ghost-button" href="{{ route('home') }}">{{ __('site.buttons.toHome') }}</a>
                    </div>
                </div>
            @else
                <div class="cart-layout">
                    <div class="cart-items">
                        @foreach ($cart['items'] as $item)
                            @php
                                $categoryLabel = !empty($item['category_slug'])
                                    ? __('site.categories.' . $item['category_slug'])
                                    : ($item['category'] ?? null);
                                if (!empty($item['category_slug']) && $categoryLabel === 'site.categories.' . $item['category_slug']) {
                                    $categoryLabel = $item['category'] ?? null;
                                }
                            @endphp

                            <article class="cart-item-card reveal is-visible">
                                <div class="cart-item-media" style="background-image:url('{{ $item['main_image_url'] ?? asset($item['main_image']) }}')"></div>

                                <div class="cart-item-copy">
                                    <small>{{ collect([$categoryLabel, $item['size']])->filter()->implode(' · ') }}</small>
                                    <h3>{{ $item['title'] }}</h3>

                                    <div class="cart-price-line">
                                        <span
                                            data-price
                                            data-price-rub="{{ $item['price_rub'] !== null ? $item['price_rub'] : '' }}"
                                            data-price-usd="{{ $item['price_usd'] !== null ? $item['price_usd'] : '' }}"
                                        >
                                            @if ($item['price_rub'] !== null)
                                                {{ number_format($item['price_rub'], 0, ',', ' ') }} ₽
                                            @elseif ($item['price_usd'] !== null)
                                                ${{ number_format($item['price_usd'], 0, '.', ' ') }}
                                            @endif
                                        </span>
                                        <span>{{ __('site.cart.unit') }}</span>
                                    </div>

                                    <div class="cart-item-actions">
                                        <form class="cart-inline-form" method="POST" action="{{ route('cart.update') }}">
                                            @csrf
                                            <input type="hidden" name="painting_id" value="{{ $item['painting_id'] }}">
                                            <input class="field quantity-field" type="number" min="0" max="99" name="quantity" value="{{ $item['quantity'] }}">
                                            <button class="ghost-button" type="submit">{{ __('site.cart.update') }}</button>
                                        </form>

                                        <form method="POST" action="{{ route('cart.remove') }}">
                                            @csrf
                                            <input type="hidden" name="painting_id" value="{{ $item['painting_id'] }}">
                                            <button class="ghost-button" type="submit">{{ __('site.cart.remove') }}</button>
                                        </form>

                                        @if (!empty($item['slug']))
                                            <a class="ghost-button" href="{{ route('paintings.show', $item['slug']) }}">{{ __('site.cart.to_painting') }}</a>
                                        @endif
                                    </div>
                                </div>

                                <div class="cart-subtotal">
                                    <strong
                                        data-price
                                        data-price-rub="{{ $item['subtotal_rub'] !== null ? $item['subtotal_rub'] : '' }}"
                                        data-price-usd="{{ $item['subtotal_usd'] !== null ? $item['subtotal_usd'] : '' }}"
                                    >
                                        @if ($item['subtotal_rub'] !== null)
                                            {{ number_format($item['subtotal_rub'], 0, ',', ' ') }} ₽
                                        @elseif ($item['subtotal_usd'] !== null)
                                            ${{ number_format($item['subtotal_usd'], 0, '.', ' ') }}
                                        @endif
                                    </strong>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <aside class="cart-summary-card reveal is-visible">
                        <small>{{ __('site.cart.checkout.eyebrow') }}</small>
                        <h3>{{ __('site.cart.checkout.title') }}</h3>

                        <div class="cart-summary-totals">
                            <div>
                                <span>{{ __('site.cart.checkout.positions') }}</span>
                                <strong>{{ $cart['total_quantity'] }}</strong>
                            </div>
                            <div>
                                <span>{{ __('site.cart.checkout.total') }}</span>
                                <strong
                                    data-price
                                    data-price-rub="{{ $cart['total_rub'] }}"
                                    data-price-usd="{{ $cart['total_usd'] }}"
                                >
                                    {{ number_format($cart['total_rub'], 0, ',', ' ') }} ₽
                                </strong>
                            </div>
                        </div>

                        <form class="checkout-form" method="POST" action="{{ route('order.create') }}">
                            @csrf
                            <input class="field" type="text" name="customer_name" placeholder="{{ __('site.cart.checkout.fields.name') }}" value="{{ old('customer_name') }}">
                            @error('customer_name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror

                            <input class="field" type="text" name="contact" placeholder="{{ __('site.cart.checkout.fields.contact') }}" value="{{ old('contact') }}">
                            @error('contact')
                                <p class="form-error">{{ $message }}</p>
                            @enderror

                            <input class="field" type="text" name="phone" placeholder="{{ __('site.cart.checkout.fields.phone') }}" value="{{ old('phone') }}">
                            @error('phone')
                                <p class="form-error">{{ $message }}</p>
                            @enderror

                            <input class="field" type="text" name="address" placeholder="{{ __('site.cart.checkout.fields.address') }}" value="{{ old('address') }}">
                            @error('address')
                                <p class="form-error">{{ $message }}</p>
                            @enderror

                            <textarea class="field" name="comment" placeholder="{{ __('site.cart.checkout.fields.comment') }}">{{ old('comment') }}</textarea>
                            @error('comment')
                                <p class="form-error">{{ $message }}</p>
                            @enderror

                            <button class="button" type="submit">{{ __('site.cart.checkout.submit') }}</button>
                        </form>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection
