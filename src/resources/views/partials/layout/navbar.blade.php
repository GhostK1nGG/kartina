@php
    $homeAnchor = static fn (string $section): string => request()->routeIs('home')
        ? "#{$section}"
        : route('home') . "#{$section}";
    $cartQuantity = collect(session('cart', []))->sum('quantity');
    $redirectTarget = request()->getRequestUri();
@endphp

<header class="topbar container">
    <div class="nav-shell">
        <a href="{{ request()->routeIs('home') ? '#top' : route('home') }}" class="brand" aria-label="k.">
            <span class="brand-mark is-monogram">k.</span>
            <span class="brand-wordmark" aria-label="k.">
                <span class="brand-wordmark-letter">k</span>
                <span class="brand-wordmark-dot">.</span>
            </span>
        </a>

        <nav class="nav-links" aria-label="{{ __('site.nav.label') }}">
            <a href="{{ $homeAnchor('concept') }}">{{ __('site.nav.aboutProject') }}</a>
            <a href="{{ route('about') }}">{{ __('site.nav.aboutArtist') }}</a>
            <a href="{{ route('paintings.index') }}">{{ __('site.nav.catalog') }}</a>
            <a href="{{ route('reviews') }}">{{ __('site.nav.reviews') }}</a>
        </nav>

        <div class="nav-actions">
            <a class="ghost-button" href="{{ route('cart.index') }}">
                <span>{{ __('site.nav.cart') }}</span>
                @if ($cartQuantity > 0)
                    <span class="nav-badge">{{ $cartQuantity }}</span>
                @endif
            </a>

            <div class="nav-pref-group" data-pref-dropdown>
                <button class="ghost-button nav-pref-trigger" type="button" data-pref-trigger aria-haspopup="true" aria-expanded="false">
                    <span class="nav-pref-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M3 12h18"></path>
                            <path d="M12 3a14 14 0 0 1 0 18"></path>
                            <path d="M12 3a14 14 0 0 0 0 18"></path>
                        </svg>
                    </span>
                    <span class="nav-pref-stack">
                        <span class="nav-pref-label">{{ __('site.controls.language') }}</span>
                        <strong class="nav-pref-value">{{ __('site.controls.localeCurrent') }}</strong>
                    </span>
                </button>

                <div class="nav-pref-menu" data-pref-menu>
                    <a class="nav-pref-option" href="{{ route('locale.switch', ['locale' => 'ru', 'redirect' => $redirectTarget]) }}">
                        {{ __('site.controls.languageRussian') }}
                    </a>
                    <a class="nav-pref-option" href="{{ route('locale.switch', ['locale' => 'en', 'redirect' => $redirectTarget]) }}">
                        {{ __('site.controls.languageEnglish') }}
                    </a>
                </div>
            </div>

            <div class="nav-pref-group" data-pref-dropdown>
                <button class="ghost-button nav-pref-trigger" type="button" data-pref-trigger aria-haspopup="true" aria-expanded="false">
                    <span class="nav-pref-stack">
                        <span class="nav-pref-label">{{ __('site.controls.currency') }}</span>
                        <strong class="nav-pref-value" data-currency-label>{{ __('site.controls.currencyCurrent') }}</strong>
                    </span>
                </button>

                <div class="nav-pref-menu" data-pref-menu>
                    <button class="nav-pref-option" type="button" data-set-currency="rub">
                        {{ __('site.controls.currencyRub') }}
                    </button>
                    <button class="nav-pref-option" type="button" data-set-currency="usd">
                        {{ __('site.controls.currencyUsd') }}
                    </button>
                </div>
            </div>

            <a class="button" href="{{ route('project-request') }}">{{ __('site.nav.orderProject') }}</a>
        </div>
    </div>
</header>
