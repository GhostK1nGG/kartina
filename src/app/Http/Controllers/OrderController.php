<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Jobs\SendOrderTelegramNotificationJob;
use App\Models\Order;
use App\Support\CartManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, CartManager $cart): RedirectResponse
    {
        $content = $cart->content();

        if ($content['is_empty']) {
            return redirect()
                ->route('cart.index')
                ->withErrors(['cart' => __('site.messages.cart_empty')]);
        }

        $order = Order::create([
            'session_id' => Session::getId(),
            'customer_name' => $request->string('customer_name')->toString(),
            'contact' => $request->string('contact')->toString(),
            'phone' => $request->string('phone')->toString() ?: null,
            'address' => $request->string('address')->toString() ?: null,
            'comment' => $request->string('comment')->toString() ?: null,
            'cart_snapshot' => $content,
            'total_rub' => $content['total_rub'],
            'total_usd' => $content['total_usd'],
            'status' => 'new',
        ]);

        SendOrderTelegramNotificationJob::dispatch($order->id);

        $cart->clear();

        return redirect()
            ->route('cart.index')
            ->with('order_success', __('site.messages.order_success', ['id' => $order->id]));
    }
}
