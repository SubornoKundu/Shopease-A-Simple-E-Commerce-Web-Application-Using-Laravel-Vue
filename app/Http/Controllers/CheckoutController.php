<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    /**
     * Checkout page: order summary + shipping details form. Bounces
     * back to the cart if there's nothing to check out.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        $cartItems = $request->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = round($cartItems->sum('line_total'), 2);
        $shippingFee = config('shop.shipping_fee');

        return Inertia::render('Checkout/Index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
            'total' => round($subtotal + $shippingFee, 2),
        ]);
    }

    /**
     * Turn the current cart into a pending order, then hand off to the
     * (demo) SSLCommerz payment step. Stock is intentionally NOT
     * decremented and the cart is NOT cleared here -- see
     * Payment\SslCommerzController::success(), which only runs once the
     * demo payment actually "succeeds".
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $cartItems = $request->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        foreach ($cartItems as $cartItem) {
            if (! $cartItem->product->is_active || ! $cartItem->product->inStock()) {
                return redirect()->route('cart.index')
                    ->with('error', $cartItem->product->name.' is no longer available. Please remove it to continue.');
            }
        }

        $subtotal = round($cartItems->sum('line_total'), 2);
        $shippingFee = config('shop.shipping_fee');

        $order = DB::transaction(function () use ($request, $data, $cartItems, $subtotal, $shippingFee) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => Order::generateOrderNumber(),
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total_amount' => round($subtotal + $shippingFee, 2),
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'payment_method' => 'sslcommerz',
                'shipping_name' => $data['shipping_name'],
                'shipping_phone' => $data['shipping_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_city' => $data['shipping_city'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_image' => $cartItem->product->image,
                    'price' => $cartItem->product->final_price,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->line_total,
                ]);
            }

            return $order;
        });

        return redirect()->route('payment.show', $order);
    }
}
