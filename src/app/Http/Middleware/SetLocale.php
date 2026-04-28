<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['ru', 'en'];

    private const CIS_COUNTRY_CODES = [
        'AM',
        'AZ',
        'BY',
        'GE',
        'KG',
        'KZ',
        'MD',
        'RU',
        'TJ',
        'TM',
        'UZ',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale_preference');

        if (!in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = $this->detectLocale($request);
        }

        app()->setLocale($locale);

        return $next($request);
    }

    private function detectLocale(Request $request): string
    {
        $countryCode = $this->resolveCountryCode($request);

        if ($countryCode !== null) {
            return in_array($countryCode, self::CIS_COUNTRY_CODES, true) ? 'ru' : 'en';
        }

        return $request->getPreferredLanguage(self::SUPPORTED_LOCALES) ?? 'ru';
    }

    private function resolveCountryCode(Request $request): ?string
    {
        foreach ([
            'CF-IPCountry',
            'CloudFront-Viewer-Country',
            'X-Country-Code',
            'X-Country',
            'X-Geo-Country',
        ] as $header) {
            $countryCode = strtoupper(trim((string) $request->headers->get($header)));

            if (preg_match('/^[A-Z]{2}$/', $countryCode) === 1) {
                return $countryCode;
            }
        }

        return null;
    }
}
