<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LocaleDetectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get('/test-locale', function (Request $request) {
            return response()->json([
                'locale' => app()->getLocale(),
                'session_locale' => $request->session()->get('locale_preference'),
            ]);
        });
    }

    public function test_first_visit_uses_russian_for_cis_visitors(): void
    {
        $response = $this
            ->withHeaders(['CF-IPCountry' => 'KZ'])
            ->get('/test-locale');

        $response
            ->assertOk()
            ->assertJson([
                'locale' => 'ru',
                'session_locale' => null,
            ]);
    }

    public function test_first_visit_uses_english_for_non_cis_visitors(): void
    {
        $response = $this
            ->withHeaders(['CF-IPCountry' => 'DE'])
            ->get('/test-locale');

        $response
            ->assertOk()
            ->assertJson([
                'locale' => 'en',
                'session_locale' => null,
            ]);
    }

    public function test_accept_language_is_used_when_country_header_is_missing(): void
    {
        $response = $this
            ->withHeaders(['Accept-Language' => 'en-US,en;q=0.9,ru;q=0.8'])
            ->get('/test-locale');

        $response
            ->assertOk()
            ->assertJson([
                'locale' => 'en',
                'session_locale' => null,
            ]);
    }

    public function test_manual_locale_selection_has_priority_over_auto_detection(): void
    {
        $response = $this
            ->withSession(['locale_preference' => 'ru'])
            ->withHeaders(['CF-IPCountry' => 'DE'])
            ->get('/test-locale');

        $response
            ->assertOk()
            ->assertJson([
                'locale' => 'ru',
                'session_locale' => 'ru',
            ]);
    }

    public function test_auto_locale_is_recalculated_without_manual_selection(): void
    {
        $this->withHeaders(['CF-IPCountry' => 'DE'])->get('/test-locale')
            ->assertOk()
            ->assertJson([
                'locale' => 'en',
                'session_locale' => null,
            ]);

        $this->withHeaders(['CF-IPCountry' => 'KZ'])->get('/test-locale')
            ->assertOk()
            ->assertJson([
                'locale' => 'ru',
                'session_locale' => null,
            ]);
    }
}
