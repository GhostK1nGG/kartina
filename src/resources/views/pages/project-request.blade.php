@extends('layouts.app')

@section('content')
    <section class="placeholder-page project-request-page">
        <div class="container">
            <div class="section-head reveal is-visible">
                <h2>{{ __('site.project_request.heading') }}</h2>
                <p>{{ __('site.project_request.lead') }}</p>
            </div>

            @if (session('project_request_success'))
                <div class="status-banner success-banner reveal is-visible">
                    {{ session('project_request_success') }}
                </div>
            @endif

            @error('duplicate_request')
                <div class="status-banner error-banner reveal is-visible">
                    {{ $message }}
                </div>
            @enderror

            <div class="form-page-shell reveal is-visible">
                <div class="form-page-copy">
                    <small>{{ __('site.project_request.eyebrow') }}</small>
                    <h1>{{ __('site.project_request.title') }}</h1>
                    <p>{{ __('site.project_request.description') }}</p>
                </div>

                <form
                    class="form-page-form"
                    method="POST"
                    action="{{ route('project-request.store') }}"
                    enctype="multipart/form-data"
                    data-submit-once
                >
                    @csrf
                    <input class="field" type="text" name="name" placeholder="{{ __('site.project_request.fields.name') }}" value="{{ old('name') }}" autocomplete="off">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                    <input class="field" type="text" name="contact" placeholder="{{ __('site.project_request.fields.contact') }}" value="{{ old('contact') }}" autocomplete="off">
                    @error('contact')
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                    <textarea class="field" name="task" placeholder="{{ __('site.project_request.fields.task') }}">{{ old('task') }}</textarea>
                    @error('task')
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                    <label class="upload-field">
                        <span>{{ __('site.project_request.fields.attachment') }}</span>
                        <input class="field" type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.zip">
                    </label>
                    @error('attachment')
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                    <button class="button" type="submit" data-loading-label="{{ __('site.project_request.loading') }}">{{ __('site.project_request.submit') }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection
