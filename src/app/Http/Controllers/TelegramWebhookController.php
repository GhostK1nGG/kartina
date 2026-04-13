<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\Telegram\TelegramBotService;
use App\Support\Telegram\TelegramMessageFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, TelegramBotService $telegram): JsonResponse
    {
        if (!$this->hasValidSecret($request)) {
            return response()->json(['ok' => false], Response::HTTP_FORBIDDEN);
        }

        $callbackQuery = $request->input('callback_query');

        if (is_array($callbackQuery)) {
            $this->handleCallbackQuery($callbackQuery, $telegram);
        }

        return response()->json(['ok' => true]);
    }

    private function hasValidSecret(Request $request): bool
    {
        $secret = (string) config('services.telegram.webhook_secret');

        if ($secret === '') {
            return true;
        }

        return hash_equals($secret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token'));
    }

    private function handleCallbackQuery(array $callbackQuery, TelegramBotService $telegram): void
    {
        $callbackId = (string) ($callbackQuery['id'] ?? '');
        $callbackData = (string) ($callbackQuery['data'] ?? '');
        $chatId = (string) data_get($callbackQuery, 'message.chat.id', '');
        $messageId = (int) data_get($callbackQuery, 'message.message_id', 0);
        $configuredChatId = (string) config('services.telegram.chat_id');

        if ($callbackId === '' || $chatId === '' || $messageId <= 0) {
            return;
        }

        if ($configuredChatId !== '' && $chatId !== $configuredChatId) {
            $telegram->answerCallbackQuery($callbackId, 'Недостаточно прав.', ['show_alert' => true]);

            return;
        }

        if (!preg_match('/^review:publish:(\d+)$/', $callbackData, $matches)) {
            $telegram->answerCallbackQuery($callbackId, 'Неизвестное действие.', ['show_alert' => true]);

            return;
        }

        $review = Review::query()->find((int) $matches[1]);

        if (!$review) {
            $telegram->answerCallbackQuery($callbackId, 'Отзыв не найден.', ['show_alert' => true]);

            return;
        }

        if (!$review->is_published) {
            $review->forceFill(['is_published' => true])->save();
        }

        $telegram->editMessageText(
            $chatId,
            $messageId,
            TelegramMessageFormatter::reviewModerationText($review->fresh(), published: true),
        );

        $telegram->answerCallbackQuery($callbackId, 'Отзыв опубликован.');
    }
}
