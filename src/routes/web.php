<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectRequestController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TelegramWebhookController;
use App\Models\Painting;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/locale/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, ['ru', 'en'], true), 404);

    $request->session()->put('locale', $locale);

    $redirect = (string) $request->query('redirect', route('home'));

    if ($redirect === '' || !str_starts_with($redirect, '/')) {
        $redirect = route('home');
    }

    return redirect($redirect);
})->name('locale.switch');

Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/paintings', [CatalogController::class, 'index'])->name('paintings.index');
Route::get('/paintings/{slug}', [CatalogController::class, 'show'])->name('paintings.show');
Route::get('/painting/{painting}', fn (Painting $painting) => redirect()->route('paintings.show', $painting))
    ->name('painting.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/order/create', [OrderController::class, 'store'])->name('order.create');

Route::get('/project-request', [ProjectRequestController::class, 'create'])->name('project-request');
Route::post('/project-request', [ProjectRequestController::class, 'store'])->name('project-request.store');
Route::get('/project', [ProjectRequestController::class, 'create'])->name('project.create');
Route::post('/project', [ProjectRequestController::class, 'store'])->name('project.store');

Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::post('/telegram/webhook', TelegramWebhookController::class)
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('telegram.webhook');
