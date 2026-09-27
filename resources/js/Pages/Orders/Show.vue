<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2, MapPin, Package } from '@lucide/vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { formatPrice } from '@/currency';
import { routes } from '@/routes';

const props = defineProps({
    order: { type: Object, required: true },
});

const statusStyles = {
    pending: 'bg-amber-100 text-amber-700',
    processing: 'bg-blue-100 text-blue-700',
    completed: 'bg-green-100 text-green-700',
    cancelled: 'bg-gray-200 text-gray-600',
};

const paymentStyles = {
    pending: 'bg-amber-100 text-amber-700',
    paid: 'bg-green-100 text-green-700',
    failed: 'bg-rose-100 text-rose-700',
};

function formatDate(value) {
    return new Date(value).toLocaleString(undefined, {
        year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit',
    });
}
</script>

<template>
    <Head :title="`Order ${order.order_number}`" />

    <MainLayout>
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <div v-if="order.payment_status === 'paid'" class="mb-6 flex items-center gap-3 rounded-2xl border border-green-200 bg-green-50 p-4">
                <CheckCircle2 class="h-6 w-6 shrink-0 text-green-600" />
                <div>
                    <p class="font-semibold text-green-800">Payment confirmed</p>
                    <p class="text-sm text-green-700">Transaction ID: {{ order.transaction_id }}</p>
                </div>
            </div>

            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="font-mono text-xl font-bold text-gray-900">{{ order.order_number }}</h1>
                    <p class="text-sm text-gray-500">Placed {{ formatDate(order.created_at) }}</p>
                </div>
                <div class="flex gap-2">
                    <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="statusStyles[order.status]">{{ order.status }}</span>
                    <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="paymentStyles[order.payment_status]">{{ order.payment_status }}</span>
                </div>
            </div>

            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5">
                <h2 class="mb-3 flex items-center gap-2 font-bold text-gray-900">
                    <Package class="h-4 w-4" /> Items
                </h2>
                <ul class="divide-y divide-gray-100">
                    <li v-for="item in order.items" :key="item.id" class="flex items-center gap-4 py-3">
                        <img :src="item.product_image" :alt="item.product_name" class="h-14 w-14 shrink-0 rounded-lg bg-gray-100 object-cover">
                        <div class="min-w-0 flex-1">
                            <Link
                                v-if="item.product"
                                :href="routes.productShow(item.product.slug)"
                                class="truncate text-sm font-semibold text-gray-800 hover:text-green-700"
                            >
                                {{ item.product_name }}
                            </Link>
                            <p v-else class="truncate text-sm font-semibold text-gray-800">{{ item.product_name }}</p>
                            <p class="text-xs text-gray-400">Qty {{ item.quantity }} &times; {{ formatPrice(item.price) }}</p>
                        </div>
                        <span class="font-semibold text-gray-900">{{ formatPrice(item.subtotal) }}</span>
                    </li>
                </ul>

                <div class="mt-4 space-y-1 border-t border-gray-100 pt-4 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>{{ formatPrice(order.subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span>{{ formatPrice(order.shipping_fee) }}</span>
                    </div>
                    <div class="flex justify-between pt-1 text-base font-bold text-gray-900">
                        <span>Total</span>
                        <span>{{ formatPrice(order.total_amount) }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <h2 class="mb-3 flex items-center gap-2 font-bold text-gray-900">
                    <MapPin class="h-4 w-4" /> Shipping Details
                </h2>
                <p class="text-sm text-gray-700">{{ order.shipping_name }} &middot; {{ order.shipping_phone }}</p>
                <p class="text-sm text-gray-500">{{ order.shipping_address }}, {{ order.shipping_city }}</p>
                <p v-if="order.notes" class="mt-2 text-sm text-gray-500 italic">"{{ order.notes }}"</p>
            </div>

            <Link :href="routes.ordersIndex()" class="mt-6 inline-block text-sm font-medium text-green-700 hover:underline">
                &larr; Back to Order History
            </Link>
        </div>
    </MainLayout>
</template>
