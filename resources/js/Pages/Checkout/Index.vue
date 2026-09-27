<script setup>
import { useForm, Head, usePage } from '@inertiajs/vue3';
import { formatPrice } from '@/currency';
import { routes } from '@/routes';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    cartItems: { type: Array, required: true },
    subtotal: { type: [Number, String], required: true },
    shippingFee: { type: [Number, String], required: true },
    total: { type: [Number, String], required: true },
});

const page = usePage();
const user = page.props.auth?.user;

const form = useForm({
    shipping_name: user?.name ?? '',
    shipping_phone: user?.phone ?? '',
    shipping_address: '',
    shipping_city: '',
    notes: '',
});

function submit() {
    form.post(routes.checkoutStore());
}
</script>

<template>
    <Head title="Checkout" />

    <MainLayout>
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-2xl font-bold text-gray-900">Checkout</h1>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <form class="space-y-5 lg:col-span-2" @submit.prevent="submit">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h2 class="mb-4 font-bold text-gray-900">Shipping Details</h2>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Full Name</label>
                                <input v-model="form.shipping_name" type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/20 focus:outline-none">
                                <p v-if="form.errors.shipping_name" class="mt-1 text-xs text-rose-600">{{ form.errors.shipping_name }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Phone Number</label>
                                <input v-model="form.shipping_phone" type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/20 focus:outline-none">
                                <p v-if="form.errors.shipping_phone" class="mt-1 text-xs text-rose-600">{{ form.errors.shipping_phone }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Street Address</label>
                                <input v-model="form.shipping_address" type="text" placeholder="House, road, area" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/20 focus:outline-none">
                                <p v-if="form.errors.shipping_address" class="mt-1 text-xs text-rose-600">{{ form.errors.shipping_address }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">City</label>
                                <input v-model="form.shipping_city" type="text" placeholder="Dhaka" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/20 focus:outline-none">
                                <p v-if="form.errors.shipping_city" class="mt-1 text-xs text-rose-600">{{ form.errors.shipping_city }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Order Notes (optional)</label>
                                <textarea v-model="form.notes" rows="3" placeholder="Delivery instructions, landmark, etc." class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/20 focus:outline-none" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h2 class="mb-1 font-bold text-gray-900">Payment</h2>
                        <p class="text-sm text-gray-500">You'll choose bKash, Nagad, Rocket, or Card on the next (demo) SSLCommerz payment screen.</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-full bg-green-600 py-3 font-semibold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ form.processing ? 'Placing order...' : `Continue to Payment -- ${formatPrice(total)}` }}
                    </button>
                </form>

                <div class="h-fit rounded-2xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 font-bold text-gray-900">Order Summary</h2>
                    <ul class="mb-4 max-h-64 space-y-3 overflow-y-auto">
                        <li v-for="item in cartItems" :key="item.id" class="flex items-center gap-3">
                            <img :src="item.product.image" :alt="item.product.name" class="h-12 w-12 shrink-0 rounded-lg bg-gray-100 object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-800">{{ item.product.name }}</p>
                                <p class="text-xs text-gray-400">Qty {{ item.quantity }}</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-800">{{ formatPrice(item.line_total) }}</span>
                        </li>
                    </ul>
                    <div class="space-y-1 border-t border-gray-100 pt-4 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>{{ formatPrice(subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span>{{ formatPrice(shippingFee) }}</span>
                        </div>
                        <div class="flex justify-between pt-2 text-base font-bold text-gray-900">
                            <span>Total</span>
                            <span>{{ formatPrice(total) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
