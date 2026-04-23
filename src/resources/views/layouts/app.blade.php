<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($seoTitle ?? config('app.name')) . ' | ' . config('app.name') }}</title>
    <meta name="description" content="{{ $seoDescription ?? __('site.meta.default_description') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    data-page="{{ $pageKey ?? 'default' }}"
    data-default-locale="{{ app()->getLocale() }}"
    data-default-currency="rub"
>
    @include('partials.layout.background')

    <div class="page">
        @include('partials.layout.navbar')

        <main id="top">
            @yield('content')
        </main>

        @include('partials.layout.image-lightbox')
        @include('partials.layout.footer')
    </div>
</body>
</html>
