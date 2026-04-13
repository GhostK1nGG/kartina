<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartUpdateRequest;
use App\Models\Painting;
use App\Support\CartManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(CartManager $cart): View
    {
        return view('pages.cart', [
            'pageKey' => 'cart',
            'seoTitle' => __('site.meta.cart.title'),
            'seoDescription' => __('site.meta.cart.description'),
            'cart' => $cart->content(),
        ]);
    }

    public function add(Request $request, CartManager $cart): RedirectResponse
    {
        $validated = $request->validate([
            'painting_id' => ['required', 'integer', 'exists:paintings,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'redirect_to' => ['nullable', 'in:back,cart'],
        ]);

        $painting = Painting::query()->active()->findOrFail($validated['painting_id']);
        $cart->add($painting, (int) ($validated['quantity'] ?? 1));

        $message = __('site.messages.added_to_cart', ['title' => $painting->title]);

        if (($validated['redirect_to'] ?? 'back') === 'cart') {
            return redirect()
                ->route('cart.index')
                ->with('cart_status', $message);
        }

        return back()->with('cart_status', $message);
    }

    public function update(CartUpdateRequest $request, CartManager $cart): RedirectResponse
    {
        $cart->updateById($request->integer('painting_id'), $request->integer('quantity'));

        return back()->with('cart_status', __('site.messages.cart_updated'));
    }

    public function remove(Request $request, CartManager $cart): RedirectResponse
    {
        $validated = $request->validate([
            'painting_id' => ['required', 'integer'],
        ]);

        $cart->removeById((int) $validated['painting_id']);

        return back()->with('cart_status', __('site.messages.cart_removed'));
    }
}
