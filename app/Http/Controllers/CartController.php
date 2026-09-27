<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    /**
     * View the logged-in user's cart, with a running total.
     */
    public function index(Request $request): Response
    {
        $cartItems = $request->user()->cartItems()
            ->with('product.brand')
            ->latest()
            ->get();

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'total' => round($cartItems->sum('line_total'), 2),
        ]);
    }

    /**
     * Add a product to the cart. If it's already there, the requested
     * quantity is added on top of what's already in the cart.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $product = Product::query()->active()->findOrFail($data['product_id']);
        $quantity = $data['quantity'] ?? 1;

        if (! $product->inStock()) {
            return back()->with('error', $product->name.' is currently out of stock.');
        }

        $cartItem = CartItem::firstOrNew([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
        ]);
        $requestedTotal = ($cartItem->exists ? $cartItem->quantity : 0) + $quantity;
        $cartItem->quantity = min($requestedTotal, $product->stock);
        $cartItem->save();

        return back()->with('success', $product->name.' added to your cart.');
    }

    /**
     * Update a cart line's quantity.
     */
    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_if($cartItem->user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $cartItem->update([
            'quantity' => min($data['quantity'], $cartItem->product->stock),
        ]);

        return back()->with('success', 'Cart updated.');
    }

    /**
     * Remove a line from the cart.
     */
    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_if($cartItem->user_id !== $request->user()->id, 403);

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }
}
