<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoOrderSeeder extends Seeder
{
    /**
     * A ready-to-log-in demo account with a bit of order history, so
     * the "Order History" screen has something to show without first
     * placing a live order. Sign in as this user the same way as any
     * other: OTP login with the email below (see README for how the
     * demo OTP is delivered).
     */
    public const DEMO_EMAIL = 'demo@shopease.test';

    /**
     * [[slug => qty, ...], status, days ago]
     *
     * @var list<array{0: array<string, int>, 1: string, 2: int}>
     */
    public const ORDERS = [
        [['iphone-15-pro-max' => 1, 'airpods-pro-2' => 1], Order::STATUS_COMPLETED, 6],
        [['air-max-270' => 1], Order::STATUS_PROCESSING, 1],
    ];

    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => self::DEMO_EMAIL],
            [
                'name' => 'Demo Customer',
                'phone' => '01700000000',
                'email_verified_at' => now(),
            ],
        );

        if ($user->orders()->exists()) {
            return;
        }

        foreach (self::ORDERS as [$lines, $status, $daysAgo]) {
            $subtotal = 0;
            $items = [];

            foreach ($lines as $slug => $quantity) {
                $product = Product::where('slug', $slug)->first();

                if (! $product) {
                    continue;
                }

                $lineSubtotal = round($product->final_price * $quantity, 2);
                $subtotal += $lineSubtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image,
                    'price' => $product->final_price,
                    'quantity' => $quantity,
                    'subtotal' => $lineSubtotal,
                ];
            }

            if (empty($items)) {
                continue;
            }

            $shippingFee = config('shop.shipping_fee');
            $placedAt = now()->subDays($daysAgo);

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total_amount' => round($subtotal + $shippingFee, 2),
                'status' => $status,
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => 'sslcommerz (demo)',
                'transaction_id' => strtoupper('DEMO'.$placedAt->format('ymdHis').bin2hex(random_bytes(3))),
                'shipping_name' => $user->name,
                'shipping_phone' => $user->phone,
                'shipping_address' => 'House 12, Road 5, Dhanmondi',
                'shipping_city' => 'Dhaka',
            ]);

            // created_at/updated_at aren't mass-assignable (by design --
            // see Order::$fillable), so backdating this demo order's
            // "placed on" date needs an explicit forceFill().
            $order->forceFill([
                'created_at' => $placedAt,
                'updated_at' => $placedAt,
            ])->save();

            foreach ($items as $item) {
                OrderItem::create([...$item, 'order_id' => $order->id]);
            }
        }
    }
}
