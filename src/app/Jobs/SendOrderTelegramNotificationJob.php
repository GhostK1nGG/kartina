<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOrderTelegramNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $orderId,
    ) {
    }

    public function handle(TelegramBotService $telegram): void
    {
        $order = Order::query()->find($this->orderId);

        if (!$order) {
            return;
        }

        $items = collect($order->cart_snapshot['items'] ?? [])
            ->map(function (array $item): string {
                $title = $item['title'] ?? 'Без названия';
                $category = $item['category'] ?? null;
                $quantity = (int) ($item['quantity'] ?? 0);
                $subtotalRub = isset($item['subtotal_rub']) && $item['subtotal_rub'] !== null
                    ? number_format((float) $item['subtotal_rub'], 0, ',', ' ') . ' ₽'
                    : null;
                $subtotalUsd = isset($item['subtotal_usd']) && $item['subtotal_usd'] !== null
                    ? number_format((float) $item['subtotal_usd'], 0, '.', ' ') . ' $'
                    : null;
                $subtotal = $subtotalRub ?: $subtotalUsd ?: 'цена не указана';

                return '— «' . $title . '»'
                    . ($category ? ' (категория: ' . $category . ')' : '')
                    . ' x' . $quantity
                    . ' = ' . $subtotal;
            })
            ->implode("\n");

        $totals = array_filter([
            $order->total_rub !== null ? number_format((float) $order->total_rub, 0, ',', ' ') . ' ₽' : null,
            $order->total_usd !== null ? number_format((float) $order->total_usd, 0, '.', ' ') . ' $' : null,
        ]);

        $message = implode("\n", [
            '🖼 НОВЫЙ ЗАКАЗ #' . $order->id,
            '',
            '👤 Имя: ' . $order->customer_name,
            '📱 Контакт: ' . $order->contact,
            '☎️ Телефон: ' . ($order->phone ?: '—'),
            '🏠 Адрес: ' . ($order->address ?: '—'),
            '📦 Состав:',
            $items !== '' ? $items : '- пусто',
            '💰 Итого: ' . ($totals !== [] ? implode(' / ', $totals) : '0'),
            '💬 Комментарий: ' . ($order->comment ?: '—'),
            '🕒 Создан: ' . $order->created_at?->format('Y-m-d H:i:s'),
        ]);

        $telegram->sendText($message);
    }
}
