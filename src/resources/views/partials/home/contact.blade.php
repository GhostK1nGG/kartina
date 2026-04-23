<section>
    <div class="container quote-layout">
        <article class="quote-card contact-process-card reveal tilt-card">
            <small>{{ $contact['process']['eyebrow'] }}</small>
            <h3>{{ $contact['process']['title'] }}</h3>
            <p class="contact-process-lead">{{ $contact['process']['description'] }}</p>

            <div class="contact-process-steps">
                @foreach ($contact['process']['steps'] as $step)
                    <article class="contact-process-step">
                        <div class="contact-process-index">{{ $step['index'] }}</div>
                        <div class="contact-process-copy">
                            <h4>{{ $step['title'] }}</h4>
                            <p>{{ $step['description'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>

        <div class="contact-flip reveal" id="contact" tabindex="0">
            <div class="contact-inner">
                <article class="contact-face contact-front">
                    <small>{{ $contact['form']['eyebrow'] }}</small>
                    <h3>{{ $contact['form']['front_title'] }}</h3>
                    <p>{{ $contact['form']['front_description'] }}</p>

                    <div class="contact-checklist">
                        <span>{{ $contact['form']['front_list_label'] }}</span>
                        <ul>
                            @foreach ($contact['form']['front_list'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="contact-cta">
                        <button class="button" type="button" id="flipContactBtn">{{ $contact['form']['front_button'] }}</button>
                    </div>
                </article>

                <article class="contact-face contact-back">
                    <small>{{ $contact['form']['eyebrow'] }}</small>
                    <h3>{{ $contact['form']['back_title'] }}</h3>
                    <p>{{ $contact['form']['back_description'] }}</p>

                    @error('duplicate_request')
                        <div class="status-banner error-banner">
                            {{ $message }}
                        </div>
                    @enderror

                    <form action="{{ route('project-request.store') }}" method="POST" data-submit-once>
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

                        <button class="button" type="submit" data-loading-label="{{ __('site.project_request.loading') }}">{{ $contact['form']['submit_label'] }}</button>
                    </form>
                </article>
            </div>
        </div>
    </div>
</section>
