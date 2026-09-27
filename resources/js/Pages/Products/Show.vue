<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Heart, Minus, Plus, ShoppingCart, Store, Tag } from '@lucide/vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { formatPrice } from '@/currency';
import { routes } from '@/routes';

const props = defineProps({
    product: { type: Object, required: true },
    relatedProducts: { type: Array, default: () => [] },
});

const page = usePage();
const isAuthed = computed(() => Boolean(page.props.auth?.user));

const hasDiscount = computed(
    () => props.product.discount_price && Number(props.product.discount_price) < Number(props.product.price),
);
const outOfStock = computed(() => Number(props.product.stock) <= 0);
const maxQty = computed(() => Math.min(50, Number(props.product.stock) || 0));

const quantity = ref(1);

function inc() {
    if (quantity.value < maxQty.value) quantity.value += 1;
}
function dec() {
    if (quantity.value > 1) quantity.value -= 1;
}

function addToCart() {
    if (!isAuthed.value) {
        router.visit(routes.login());
        return;
    }
    router.post(
        routes.cartStore(),
        { product_id: props.product.id, quantity: quantity.value },
        { preserveScroll: true },
    );
}

function addToWishlist() {
    if (!isAuthed.value) {
        router.visit(routes.login());
        return;
    }
    router.post(routes.wishlistStore(), { product_id: props.product.id }, { preserveScroll: true });
}
</script>

<template>
    <Head :title="product.name" />

    <MainLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <nav class="mb-6 flex flex-wrap items-center gap-1 text-sm text-gray-500">
                <Link :href="routes.home()" class="hover:text-green-700">Home</Link>
                <span>/</span>
                <Link :href="routes.productsIndex({ category: product.category?.slug })" class="hover:text-green-700">
                    {{ product.category?.name }}
                </Link>
                <span>/</span>
                <span class="text-gray-700">{{ product.name }}</span>
            </nav>

            <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
                <div class="overflow-hidden rounded-2xl bg-gray-100">
                    <img :src="product.image" :alt="product.name" class="aspect-square w-full object-cover">
                </div>

                <div>
                    <Link
                        v-if="product.brand"
                        :href="routes.productsIndex({ brand: product.brand.slug })"
                        class="inline-flex items-center gap-1 text-xs font-semibold tracking-wide text-green-700 uppercase hover:underline"
                    >
                        <Store class="h-3.5 w-3.5" /> {{ product.brand.name }}
                    </Link>

                    <h1 class="mt-2 text-2xl font-bold text-gray-900 sm:text-3xl">{{ product.name }}</h1>

                    <div class="mt-4 flex items-baseline gap-3">
                        <span class="text-3xl font-bold text-gray-900">
                            {{ formatPrice(hasDiscount ? product.discount_price : product.price) }}
                        </span>
                        <span v-if="hasDiscount" class="text-lg text-gray-400 line-through">
                            {{ formatPrice(product.price) }}
                        </span>
                        <span v-if="hasDiscount" class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-700">
                            <Tag class="mr-0.5 inline h-3 w-3" />Sale
                        </span>
                    </div>

                    <p class="mt-2 text-sm font-medium" :class="outOfStock ? 'text-rose-600' : 'text-green-700'">
                        {{ outOfStock ? 'Out of stock' : `In stock (${product.stock} available)` }}
                    </p>

                    <p class="mt-6 leading-relaxed text-gray-600">{{ product.description }}</p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <div v-if="!outOfStock" class="flex items-center rounded-full border border-gray-200">
                            <button type="button" class="flex h-11 w-11 items-center justify-center text-gray-600 disabled:opacity-30" :disabled="quantity <= 1" @click="dec">
                                <Minus class="h-4 w-4" />
                            </button>
                            <span class="w-8 text-center font-semibold">{{ quantity }}</span>
                            <button type="button" class="flex h-11 w-11 items-center justify-center text-gray-600 disabled:opacity-30" :disabled="quantity >= maxQty" @click="inc">
                                <Plus class="h-4 w-4" />
                            </button>
                        </div>

                        <button
                            type="button"
                            :disabled="outOfStock"
                            class="flex flex-1 items-center justify-center gap-2 rounded-full bg-green-600 px-6 py-3 font-semibold text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-gray-300 sm:flex-none"
                            @click="addToCart"
                        >
                            <ShoppingCart class="h-4 w-4" /> Add to Cart
                        </button>

                        <button
                            type="button"
                            class="flex h-12 w-12 items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:border-rose-300 hover:text-rose-600"
                            title="Add to wishlist"
                            @click="addToWishlist"
                        >
                            <Heart class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>

            <section v-if="relatedProducts.length" class="mt-16">
                <h2 class="mb-4 text-lg font-bold text-gray-900">You might also like</h2>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <ProductCard v-for="related in relatedProducts" :key="related.id" :product="related" />
                </div>
            </section>
        </div>
    </MainLayout>
</template>
