<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { routes } from '@/routes';

const props = defineProps({
    sliders: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    latestProducts: { type: Array, default: () => [] },
    featuredProducts: { type: Array, default: () => [] },
});

const activeSlide = ref(0);
let timer = null;

function goTo(index) {
    activeSlide.value = (index + props.sliders.length) % props.sliders.length;
}

function next() {
    goTo(activeSlide.value + 1);
}

function prev() {
    goTo(activeSlide.value - 1);
}

function resetTimer() {
    window.clearInterval(timer);
    if (props.sliders.length > 1) {
        timer = window.setInterval(next, 5000);
    }
}

onMounted(resetTimer);
onBeforeUnmount(() => window.clearInterval(timer));

function onNavClick(fn) {
    fn();
    resetTimer();
}

const hasSliders = computed(() => props.sliders.length > 0);
</script>

<template>
    <Head title="Home" />

    <MainLayout>
        <!-- Hero slider -->
        <section v-if="hasSliders" class="relative overflow-hidden bg-gray-100">
            <div class="relative aspect-[16/6] w-full min-h-[220px] sm:min-h-[280px]">
                <Link
                    v-for="(slide, index) in sliders"
                    :key="slide.id"
                    :href="slide.link || routes.productsIndex()"
                    class="absolute inset-0 transition-opacity duration-500"
                    :class="index === activeSlide ? 'opacity-100' : 'pointer-events-none opacity-0'"
                >
                    <img :src="slide.image" :alt="slide.title" class="h-full w-full object-cover">
                </Link>

                <template v-if="sliders.length > 1">
                    <button
                        type="button"
                        class="absolute top-1/2 left-3 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-gray-800 shadow hover:bg-white"
                        @click="onNavClick(prev)"
                    >
                        <ChevronLeft class="h-5 w-5" />
                    </button>
                    <button
                        type="button"
                        class="absolute top-1/2 right-3 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-gray-800 shadow hover:bg-white"
                        @click="onNavClick(next)"
                    >
                        <ChevronRight class="h-5 w-5" />
                    </button>
                    <div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2">
                        <button
                            v-for="(slide, index) in sliders"
                            :key="slide.id"
                            type="button"
                            class="h-2 rounded-full transition-all"
                            :class="index === activeSlide ? 'w-6 bg-white' : 'w-2 bg-white/60'"
                            @click="onNavClick(() => goTo(index))"
                        />
                    </div>
                </template>
            </div>
        </section>

        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- Brands -->
            <section v-if="brands.length" class="mb-12">
                <h2 class="mb-4 text-lg font-bold text-gray-900">Shop by Brand</h2>
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-5">
                    <Link
                        v-for="brand in brands"
                        :key="brand.id"
                        :href="routes.productsIndex({ brand: brand.slug })"
                        class="flex flex-col items-center gap-2 rounded-xl border border-gray-200 bg-white p-4 transition hover:border-green-300 hover:shadow-md"
                    >
                        <img :src="brand.logo" :alt="brand.name" class="h-10 w-10 rounded-lg object-cover">
                        <span class="text-xs font-medium text-gray-600">{{ brand.name }}</span>
                    </Link>
                </div>
            </section>

            <!-- Categories -->
            <section v-if="categories.length" class="mb-12">
                <h2 class="mb-4 text-lg font-bold text-gray-900">Shop by Category</h2>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    <Link
                        v-for="category in categories"
                        :key="category.id"
                        :href="routes.productsIndex({ category: category.slug })"
                        class="group relative aspect-[4/3] overflow-hidden rounded-xl bg-gray-100"
                    >
                        <img :src="category.image" :alt="category.name" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        <span class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0" />
                        <span class="absolute bottom-2 left-3 text-sm font-semibold text-white">{{ category.name }}</span>
                    </Link>
                </div>
            </section>

            <!-- Featured -->
            <section v-if="featuredProducts.length" class="mb-12">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Featured Products</h2>
                    <Link :href="routes.productsIndex()" class="text-sm font-medium text-green-700 hover:underline">View all</Link>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" />
                </div>
            </section>

            <!-- Latest -->
            <section v-if="latestProducts.length">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Latest Products</h2>
                    <Link :href="routes.productsIndex()" class="text-sm font-medium text-green-700 hover:underline">View all</Link>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <ProductCard v-for="product in latestProducts" :key="product.id" :product="product" />
                </div>
            </section>
        </div>
    </MainLayout>
</template>
