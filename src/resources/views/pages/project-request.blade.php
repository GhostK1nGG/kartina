@extends('layouts.app')

@section('content')
    @php
        $projectRequestTitle = app()->getLocale() === 'en'
            ? 'Tell Me About the Task'
            : 'Расскажите о задаче';

        $projectRequestDescription = app()->getLocale() === 'en'
            ? 'Describe the idea, desired size, and mood of the future work. If you have references, you can attach them to convey the atmosphere, palette, and details more precisely.'
            : 'Опишите идею, желаемый размер и настроение будущей работы. Если есть референсы, их можно приложить, чтобы точнее передать атмосферу, палитру и детали.';

        $projectRequestSocialLead = app()->getLocale() === 'en'
            ? 'You can also contact me on social media. I will be glad to hear from you.'
            : 'Вы также можете связаться со мной в соц сетях! С радостью жду именно тебя!';

        $projectRequestSocials = [
            [
                'label' => 'Telegram',
                'href' => 'https://t.me/Ka_i_Na',
                'icon' => 'telegram',
            ],
            [
                'label' => 'VK',
                'href' => 'https://vk.com/heavenly_goddess',
                'icon' => 'vk',
            ],
            [
                'label' => 'Instagram',
                'href' => 'https://www.instagram.com/kerri_nessa/',
                'icon' => 'instagram',
            ],
        ];
    @endphp

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
                    <h1>{{ $projectRequestTitle }}</h1>
                    <p>{{ $projectRequestDescription }}</p>

                    <div class="project-socials">
                        <p class="project-socials-lead">{{ $projectRequestSocialLead }}</p>

                        <div class="project-socials-grid">
                            @foreach ($projectRequestSocials as $social)
                                <a
                                    class="project-social-link"
                                    href="{{ $social['href'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="{{ $social['label'] }}"
                                >
                                    <span class="project-social-icon" aria-hidden="true">
                                        @if ($social['icon'] === 'telegram')
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M21.2 4.8 18 19.2c-.24 1.02-.87 1.27-1.77.8l-4.9-3.61-2.36 2.27c-.26.26-.48.48-.99.48l.35-5.02 9.14-8.26c.4-.35-.08-.55-.61-.2L5.55 12.8.7 11.29c-1.05-.33-1.07-1.05.22-1.56L19.9 2.41c.88-.33 1.65.2 1.3 2.39Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                            </svg>
                                        @elseif ($social['icon'] === 'vk')
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M4.8 7.5c.12 5.7 2.97 9.12 7.98 9.12h.28v-3.27c1.84.18 3.22 1.51 3.78 3.27H19.4c-.72-2.63-2.61-4.09-3.79-4.65 1.18-.68 2.83-2.33 3.23-4.47h-2.33c-.52 1.73-2.04 3.38-3.45 3.53V7.5h-2.33v6.18c-1.42-.36-3.21-2.13-3.29-6.18H4.8Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <rect x="4.25" y="4.25" width="15.5" height="15.5" rx="4.75" stroke="currentColor" stroke-width="1.5"/>
                                                <circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="1.5"/>
                                                <circle cx="17.35" cy="6.65" r="1.1" fill="currentColor"/>
                                            </svg>
                                        @endif
                                    </span>

                                    <span class="project-social-label">{{ $social['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
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
