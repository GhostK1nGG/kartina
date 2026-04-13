<?php

namespace App\Support;

use App\Models\Painting;
use Illuminate\Session\SessionManager;

class CartManager
{
    public const SESSION_KEY = 'cart';

    public function __construct(
        protected SessionManager $session,
    ) {
    }

    public function content(): array
    {
        $items = array_values($this->session->get(self::SESSION_KEY, []));

        $totalRub = 0.0;
        $totalUsd = 0.0;
        $totalQuantity = 0;

        foreach ($items as &$item) {
            $item['price_rub'] = $item['price_rub'] !== null ? (float) $item['price_rub'] : null;
            $item['price_usd'] = $item['price_usd'] !== null ? (float) $item['price_usd'] : null;
            $item['quantity'] = (int) $item['quantity'];
            $item['subtotal_rub'] = $item['price_rub'] !== null ? $item['price_rub'] * $item['quantity'] : null;
            $item['subtotal_usd'] = $item['price_usd'] !== null ? $item['price_usd'] * $item['quantity'] : null;

            $totalRub += $item['subtotal_rub'] ?? 0;
            $totalUsd += $item['subtotal_usd'] ?? 0;
            $totalQuantity += $item['quantity'];
        }
        unset($item);

        return [
            'items' => $items,
            'total_rub' => round($totalRub, 2),
            'total_usd' => round($totalUsd, 2),
            'total_quantity' => $totalQuantity,
            'is_empty' => $totalQuantity === 0,
        ];
    }

    public function add(Painting $painting, int $quantity = 1): array
    {
        $cart = $this->session->get(self::SESSION_KEY, []);
        $key = (string) $painting->getKey();
        $existingQuantity = isset($cart[$key]) ? (int) $cart[$key]['quantity'] : 0;

        $cart[$key] = $this->makeItem($painting, $existingQuantity + max(1, $quantity));

        $this->session->put(self::SESSION_KEY, $cart);

        return $this->content();
    }

    public function update(Painting $painting, int $quantity): array
    {
        return $this->updateById((int) $painting->getKey(), $quantity, $painting);
    }

    public function updateById(int $paintingId, int $quantity, ?Painting $painting = null): array
    {
        $cart = $this->session->get(self::SESSION_KEY, []);
        $key = (string) $paintingId;

        if (!isset($cart[$key])) {
            return $this->content();
        }

        if ($quantity <= 0) {
            unset($cart[$key]);
        } elseif ($painting) {
            $cart[$key] = $this->makeItem($painting, $quantity);
        } else {
            $cart[$key]['quantity'] = $quantity;
        }

        $this->session->put(self::SESSION_KEY, $cart);

        return $this->content();
    }

    public function remove(Painting $painting): array
    {
        return $this->removeById((int) $painting->getKey());
    }

    public function removeById(int $paintingId): array
    {
        $cart = $this->session->get(self::SESSION_KEY, []);
        unset($cart[(string) $paintingId]);

        $this->session->put(self::SESSION_KEY, $cart);

        return $this->content();
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    protected function makeItem(Painting $painting, int $quantity): array
    {
        return [
            'painting_id' => $painting->id,
            'slug' => $painting->slug,
            'title' => $painting->title,
            'main_image' => $painting->main_image,
            'main_image_url' => $painting->main_image_url,
            'category' => $painting->category?->name,
            'category_slug' => $painting->category?->slug,
            'size' => $painting->size,
            'price_rub' => $painting->price_rub !== null ? (float) $painting->price_rub : null,
            'price_usd' => $painting->price_usd !== null ? (float) $painting->price_usd : null,
            'quantity' => $quantity,
        ];
    }
}
