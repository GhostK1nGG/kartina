<footer class="footer">
    <div class="container footer-shell">
        <span>{{ config('app.name') }} · {{ now()->year }}</span>
        <span>
            <a href="mailto:{{ config('services.public_contacts.email', 'trubchaninov2005@mail.ru') }}">{{ config('services.public_contacts.email', 'hello@example.com') }}</a>
            ·
            <a href="{{ config('services.public_contacts.telegram', 'https://t.me/Ghost_K1nG') }}" target="_blank" rel="noreferrer">{{ __('site.footer.telegram') }}</a>
            ·
            <a href="{{ config('services.public_contacts.vk', 'https://vk.com/example') }}" target="_blank" rel="noreferrer">{{ __('site.footer.vk') }}</a>
            ·
            <a href="{{ config('services.public_contacts.instagram', 'https://instagram.com/example') }}" target="_blank" rel="noreferrer">{{ __('site.footer.instagram') }}</a>
        </span>
    </div>
</footer>
