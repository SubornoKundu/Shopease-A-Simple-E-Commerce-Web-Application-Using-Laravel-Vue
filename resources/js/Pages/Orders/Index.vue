<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ChevronRight, PackageOpen } from '@lucide/vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { formatPrice } from '@/currency';
import { routes } from '@/routes';

defineProps({
    orders: { type: Object, required: true },
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
</script>

<template>
    <Head title="Order History" />

    <MainLayout>
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-2xl font-bold text-gray-900">Order History</h1>

            <div v-if="!orders.data.length" class="rounded-2xl border border-dashed border-gray-300 py-20 text-center">
                <PackageOpen class="mx-auto h-10 w-10 text-gray-300" />
                <p class="mt-3 text-gray-500">You haven't placed any orders yet.</p>
                <Link :href="routes.productsIndex()" class="mt-3 inline-block rounded-full bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-700">
                    Start Shopping
                </Link>
            </div>

            <ul v-else class="space-y-3">
                <li v-for="order in orders.data" :key="order.id">
                    <Link
                        :href="routes.orderShow(order.id)"
                        class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-green-300 hover:shadow-md"
                    >
                        <div>
                            <p class="font-mono text-sm font-semibold text-gray-800">{{ order.order_number }}</p>
                            <p class="mt-1 text-xs text-gray-400">
                                {{ new Date(order.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) }}
                                &middot; {{ order.items_count }} item{{ order.items_count === 1 ? '' : 's' }}
                            </p>
                            <div class="mt-2 flex gap-2">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize" :class="statusStyles[order.status]">{{ order.status }}</span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize" :class="paymentStyles[order.payment_status]">{{ order.payment_status }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-gray-900">{{ formatPrice(order.total_amount) }}</span>
                            <ChevronRight class="h-5 w-5 text-gray-300" />
                        </div>
                    </Link>
                </li>
            </ul>

            <Pagination :links="orders.links" />
        </div>
    </MainLayout>
</template>
