<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SslCommerzDemoGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SslCommerzController extends Controller
{
    public function __construct(protected SslCommerzDemoGateway $gateway) {}

    /**
     * Render the mock SSLCommerz checkout page for a pending order.
     */
    public function show(Request $request, Order $order): Response|RedirectResponse
    {
        $this->authorizeOwner($request, $order);

        if ($order->isPaid()) {
            return redirect()->route('orders.show', $order);
        }

        return Inertia::render('Payment/Gateway', [
            'order' => $order->load('items'),
            'session' => $this->gateway->initiate($order),
        ]);
    }

    /**
     * Demo "Pay Now": marks the order paid, decrements stock for each
     * item, and clears the cart -- mirroring what a real SSLCommerz IPN
     * callback would trigger after a genuine successful payment.
     */
    public function success(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOwner($request, $order);

        if ($order->isPaid()) {
            return redirect()->route('orders.show', $order);
        }

        $data = $request->validate([
            'method' => ['required', 'string', 'in:card,bkash,nagad,rocket'],
        ]);

        DB::transaction(function () use ($order, $data, $request) {
            $this->gateway->complete($order, $data['method']);

            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->decrement('stock', min($item->quantity, $item->product->stock));
                }
            }

            $request->user()->cartItems()->delete();
        });

        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment successful! Your order is confirmed.');
    }

    /**
     * Demo "Cancel payment": marks the payment failed. The order itself
     * stays pending so the customer can retry from the same page.
     */
    public function fail(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOwner($request, $order);

        $this->gateway->fail($order);

        return redirect()->route('payment.show', $order)
            ->with('error', 'Payment was not completed. You can try again below.');
    }

    protected function authorizeOwner(Request $request, Order $order): void
    {
        abort_if($order->user_id !== $request->user()->id, 403);
    }
}
