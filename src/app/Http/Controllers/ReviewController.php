<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Jobs\SendReviewTelegramNotificationJob;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        return view('pages.reviews', [
            'pageKey' => 'reviews',
            'seoTitle' => __('site.meta.reviews.title'),
            'seoDescription' => __('site.meta.reviews.description'),
            'reviews' => Review::query()->published()->latest()->get(),
        ]);
    }

    public function store(StoreReviewRequest $request): RedirectResponse
    {
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('reviews', 'public')
            : null;

        $review = Review::create([
            'author_name' => $request->string('author_name')->toString(),
            'author_city' => $request->string('author_city')->toString() ?: null,
            'text' => $request->string('text')->toString(),
            'rating' => $request->integer('rating'),
            'image_path' => $imagePath,
            'is_published' => false,
        ]);

        SendReviewTelegramNotificationJob::dispatch($review->id);

        return redirect()
            ->route('reviews')
            ->with('review_success', __('site.messages.review_sent'));
    }
}
