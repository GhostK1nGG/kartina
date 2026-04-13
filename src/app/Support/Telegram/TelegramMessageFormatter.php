<?php

namespace App\Support\Telegram;

use App\Models\ProjectRequest;
use App\Models\Review;

class TelegramMessageFormatter
{
    private const REVIEW_PREVIEW_LIMIT = 1400;
    private const PROJECT_PREVIEW_LIMIT = 1400;

    public static function reviewModerationText(Review $review, bool $published = false): string
    {
        return implode("\n", [
            'Новый отзыв',
            '',
            'Автор: ' . $review->author_name,
            'Город: ' . ($review->author_city ?: '—'),
            'Оценка: ' . $review->rating,
            'Текст: ' . static::preview($review->text, self::REVIEW_PREVIEW_LIMIT),
            'Фото: ' . ($review->image_path ? 'приложено' : '—'),
            'Статус: ' . ($published ? 'опубликован' : 'на модерации'),
            'Создан: ' . $review->created_at?->format('Y-m-d H:i:s'),
        ]);
    }

    public static function reviewImageCaption(Review $review): string
    {
        return implode("\n", [
            'Фото к отзыву',
            'Автор: ' . $review->author_name,
            'Оценка: ' . $review->rating,
        ]);
    }

    public static function projectRequestText(ProjectRequest $projectRequest): string
    {
        return implode("\n", [
            'Заявка на проект',
            '',
            'Имя: ' . $projectRequest->name,
            'Контакт: ' . $projectRequest->contact,
            'Задача: ' . static::preview($projectRequest->task, self::PROJECT_PREVIEW_LIMIT),
            'Вложение: ' . ($projectRequest->attachment_path ? 'приложено' : '—'),
            'Создана: ' . $projectRequest->created_at?->format('Y-m-d H:i:s'),
        ]);
    }

    public static function projectAttachmentCaption(ProjectRequest $projectRequest): string
    {
        return implode("\n", [
            'Вложение к заявке',
            'Имя: ' . $projectRequest->name,
            'Контакт: ' . $projectRequest->contact,
        ]);
    }

    public static function reviewFullText(Review $review): ?string
    {
        if (mb_strlen($review->text) <= self::REVIEW_PREVIEW_LIMIT) {
            return null;
        }

        return "Полный текст отзыва:\n" . $review->text;
    }

    public static function projectRequestFullText(ProjectRequest $projectRequest): ?string
    {
        if (mb_strlen($projectRequest->task) <= self::PROJECT_PREVIEW_LIMIT) {
            return null;
        }

        return "Полный текст заявки:\n" . $projectRequest->task;
    }

    private static function preview(string $text, int $limit): string
    {
        $normalized = trim($text);

        if (mb_strlen($normalized) <= $limit) {
            return $normalized;
        }

        return rtrim(mb_substr($normalized, 0, $limit - 1)) . '…';
    }
}
