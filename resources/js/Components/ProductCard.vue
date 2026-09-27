<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Heart, ShoppingCart } from '@lucide/vue';
import { formatPrice } from '@/currency';
import { routes } from '@/routes';

const props = defineProps({
    product: { type: Object, required: true },
});

const page = usePage();
const isAuthed = computed(() => Boolean(page.props.auth?.user));

const hasDiscount = computed(
    () => props.product.discount_price && Number(props.product.discount_price) < Number(props.product.price),
);

const outOfStock = computed(() => Number(props.product.stock) <= 0);

function addToCart() {
    if (!isAuthed.value) {
        router.visit(routes.login());
        return;
    }

    router.post(
        routes.cartStore(),
        { product_id: props.product.id, quantity: 1 },
        { preserveScroll: true, preserveState: true },
    );
}

function addToWishlist() {
    if (!isAuthed.value) {
        router.visit(routes.login());
        return;
    }

    router.post(
        routes.wishlistStore(),
        { product_id: props.product.id },
        { preserveScroll: true, preserveState: true },
    );
}
</script>

<template>
    <div class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:shadow-lg">
        <Link :href="`/products/${product.slug}`" class="relative block aspect-square overflow-hidden bg-gray-100">
            <img
                :src="product.image"
                :alt="product.name"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                loading="lazy"
            >
            <span
                v-if="hasDiscount"
                class="absolute top-3 left-3 rounded-full bg-rose-600 px-2.5 py-1 text-xs font-semibold text-white shadow"
            >
                Sale
            </span>
            <span
                v-if="outOfStock"
                class="absolute inset-0 flex items-center justify-center bg-white/70 text-sm font-semibold text-gray-700 backdrop-blur-[1px]"
            >
                Out of stock
            </span>
        </Link>

        <button
            type="button"
            class="absolute top-3 right-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-gray-600 shadow transition hover:bg-white hover:text-rose-600"
            title="Add to wishlist"
            @click="addToWishlist"
        >
            <Heart class="h-4.5 w-4.5" />
        </button>

        <div class="flex flex-1 flex-col gap-1 p-4">
            <p v-if="product.brand" class="text-xs font-medium tracking-wide text-gray-400 uppercase">
                {{ product.brand.name }}
            </p>
            <Link :href="`/products/${product.slug}`" class="line-clamp-2 text-sm font-semibold text-gray-900 hover:text-green-700">
                {{ product.name }}
            </Link>

            <div class="mt-auto flex items-center justify-between pt-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-base font-bold text-gray-900">
                        {{ formatPrice(hasDiscount ? product.discount_price : product.price) }}
                    </span>
                    <span v-if="hasDiscount" class="text-xs text-gray-400 line-through">
                        {{ formatPrice(product.price) }}
                    </span>
                </div>

                <button
                    type="button"
                    :disabled="outOfStock"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-green-600 text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-gray-300"
                    title="Add to cart"
                    @click="addToCart"
                >
                    <ShoppingCart class="h-4 w-4" />
                </button>
            </div>
        </div>
    </div>
</template>
