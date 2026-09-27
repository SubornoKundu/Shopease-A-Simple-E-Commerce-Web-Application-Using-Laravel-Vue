<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Minus, Plus, ShoppingBag, Trash2 } from '@lucide/vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { formatPrice } from '@/currency';
import { routes } from '@/routes';

const props = defineProps({
    cartItems: { type: Array, required: true },
    total: { type: [Number, String], required: true },
});

function updateQuantity(item, quantity) {
    if (quantity < 1) return;
    router.patch(routes.cartUpdate(item.id), { quantity }, { preserveScroll: true, preserveState: true });
}

function removeItem(item) {
    router.delete(routes.cartDestroy(item.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Your Cart" />

    <MainLayout>
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-2xl font-bold text-gray-900">Your Cart</h1>

            <div v-if="!cartItems.length" class="rounded-2xl border border-dashed border-gray-300 py-20 text-center">
                <ShoppingBag class="mx-auto h-10 w-10 text-gray-300" />
                <p class="mt-3 text-gray-500">Your cart is empty.</p>
                <Link :href="routes.productsIndex()" class="mt-3 inline-block rounded-full bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-700">
                    Start Shopping
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <ul class="space-y-4 lg:col-span-2">
                    <li v-for="item in cartItems" :key="item.id" class="flex gap-4 rounded-2xl border border-gray-200 bg-white p-4">
                        <Link :href="routes.productShow(item.product.slug)" class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-100">
                            <img :src="item.product.image" :alt="item.product.name" class="h-full w-full object-cover">
                        </Link>

                        <div class="flex flex-1 flex-col justify-between">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p v-if="item.product.brand" class="text-xs font-medium text-gray-400 uppercase">{{ item.product.brand.name }}</p>
                                    <Link :href="routes.productShow(item.product.slug)" class="font-semibold text-gray-900 hover:text-green-700">
                                        {{ item.product.name }}
                                    </Link>
                                </div>
                                <button type="button" class="text-gray-400 hover:text-rose-600" title="Remove" @click="removeItem(item)">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>

                            <div class="flex items-end justify-between">
                                <div class="flex items-center rounded-full border border-gray-200">
                                    <button type="button" class="flex h-8 w-8 items-center justify-center text-gray-600 disabled:opacity-30" :disabled="item.quantity <= 1" @click="updateQuantity(item, item.quantity - 1)">
                                        <Minus class="h-3.5 w-3.5" />
                                    </button>
                                    <span class="w-6 text-center text-sm font-semibold">{{ item.quantity }}</span>
                                    <button type="button" class="flex h-8 w-8 items-center justify-center text-gray-600 disabled:opacity-30" :disabled="item.quantity >= item.product.stock" @click="updateQuantity(item, item.quantity + 1)">
                                        <Plus class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                                <p class="font-bold text-gray-900">{{ formatPrice(item.line_total) }}</p>
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="h-fit rounded-2xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 font-bold text-gray-900">Order Summary</h2>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>{{ formatPrice(total) }}</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Shipping calculated at checkout.</p>
                    <Link
                        :href="routes.checkoutIndex()"
                        class="mt-5 block rounded-full bg-green-600 py-3 text-center font-semibold text-white hover:bg-green-700"
                    >
                        Proceed to Checkout
                    </Link>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
