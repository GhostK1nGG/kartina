<?php

namespace App\Jobs;

use App\Models\Review;
use App\Services\Telegram\TelegramBotService;
use App\Support\LocalMedia;
use App\Support\Telegram\TelegramMessageFormatter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendReviewTelegramNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $reviewId,
    ) {
    }

    public function handle(TelegramBotService $telegram): void
    {
        $review = Review::query()->find($this->reviewId);

        if (!$review) {
            return;
        }

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => 'Опубликовать',
                        'callback_data' => 'review:publish:' . $review->id,
                    ],
                ],
            ],
        ];

        $imagePath = LocalMedia::resolvePath($review->image_path);

        if ($imagePath && LocalMedia::isImage($review->image_path)) {
            $telegram->sendPhoto($imagePath, TelegramMessageFormatter::reviewImageCaption($review));
        }

        $telegram->sendText(
            TelegramMessageFormatter::reviewModerationText($review),
            ['reply_markup' => $keyboard],
        );

        $fullText = TelegramMessageFormatter::reviewFullText($review);

        if ($fullText) {
            $telegram->sendText($fullText);
        }
    }
}
