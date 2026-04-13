@extends('layouts.app')

@section('content')
    <section class="placeholder-page">
        <div class="container">
            <div class="placeholder-shell reveal is-visible">
                <small>{{ $pageEyebrow }}</small>
                <h1>{{ $pageTitle }}</h1>
                <p>{{ $pageDescription }}</p>

                <div class="placeholder-actions">
                    <a class="button" href="{{ route('home') }}">{{ __('site.buttons.toHome') }}</a>
                    <a class="ghost-button" href="{{ route('paintings.index') }}">{{ __('site.buttons.openCatalog') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection
