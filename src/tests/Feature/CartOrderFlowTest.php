<?php

namespace Tests\Feature;

use App\Jobs\SendOrderTelegramNotificationJob;
use App\Models\Category;
use App\Models\Order;
use App\Models\Painting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CartOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_add_update_and_remove_flow(): void
    {
        $painting = Painting::factory()->for(Category::factory())->create([
            'title' => 'Картина для корзины',
            'slug' => 'cart-painting',
        ]);

        $this->post(route('cart.add'), [
            'painting_id' => $painting->id,
            'quantity' => 2,
        ])->assertRedirect();

        $this->assertSame(2, session('cart')[(string) $painting->id]['quantity']);

        $this->post(route('cart.update'), [
            'painting_id' => $painting->id,
            'quantity' => 3,
        ])->assertRedirect();

        $this->assertSame(3, session('cart')[(string) $painting->id]['quantity']);

        $this->post(route('cart.remove'), [
            'painting_id' => $painting->id,
        ])->assertRedirect();

        $this->assertArrayNotHasKey((string) $painting->id, session('cart', []));
    }

    public function test_order_create_persists_snapshot_clears_cart_and_dispatches_job(): void
    {
        Queue::fake();

        $painting = Painting::factory()->for(Category::factory())->create([
            'title' => 'Картина для заказа',
            'slug' => 'order-painting',
            'price_rub' => 22000,
            'price_usd' => 250,
        ]);

        $cart = [
            (string) $painting->id => [
                'painting_id' => $painting->id,
                'slug' => $painting->slug,
                'title' => $painting->title,
                'main_image' => $painting->main_image,
                'main_image_url' => $painting->main_image_url,
                'category' => $painting->category?->name,
                'size' => $painting->size,
                'price_rub' => (float) $painting->price_rub,
                'price_usd' => (float) $painting->price_usd,
                'quantity' => 2,
            ],
        ];

        $response = $this
            ->withSession(['cart' => $cart])
            ->post(route('order.create'), [
                'customer_name' => 'Екатерина',
                'contact' => '@katya_art',
                'phone' => '+70000000000',
                'address' => 'Москва',
                'comment' => 'Позвоните перед доставкой',
            ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionMissing('cart');

        $order = Order::query()->first();

        $this->assertNotNull($order);
        $this->assertSame('Екатерина', $order->customer_name);
        $this->assertSame(2, $order->cart_snapshot['total_quantity']);

        Queue::assertPushed(SendOrderTelegramNotificationJob::class, fn (SendOrderTelegramNotificationJob $job) => $job->orderId === $order->id);
    }
}
