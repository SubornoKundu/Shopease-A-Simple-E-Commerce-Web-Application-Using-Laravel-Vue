<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { SlidersHorizontal, X } from '@lucide/vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import Pagination from '@/Components/Pagination.vue';
import { routes } from '@/routes';

const props = defineProps({
    products: { type: Object, required: true },
    brands: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const activeCategory = computed(() => props.categories.find((c) => c.slug === props.filters.category));
const activeBrand = computed(() => props.brands.find((b) => b.slug === props.filters.brand));

const hasActiveFilters = computed(
    () => Boolean(props.filters.brand || props.filters.category || props.filters.q),
);

function applyFilter(patch) {
    router.get(routes.productsIndex({ ...props.filters, ...patch }), {}, { preserveState: true, preserveScroll: true });
}

function toggleCategory(slug) {
    applyFilter({ category: props.filters.category === slug ? undefined : slug });
}

function toggleBrand(slug) {
    applyFilter({ brand: props.filters.brand === slug ? undefined : slug });
}

function setSort(event) {
    applyFilter({ sort: event.target.value });
}

const pageTitle = computed(() => {
    if (props.filters.q) return `Search: "${props.filters.q}"`;
    if (activeCategory.value) return activeCategory.value.name;
    if (activeBrand.value) return activeBrand.value.name;
    return 'All Products';
});
</script>

<template>
    <Head :title="pageTitle" />

    <MainLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ pageTitle }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ products.total }} product{{ products.total === 1 ? '' : 's' }} found</p>
                </div>
                <select
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-green-500 focus:outline-none"
                    :value="filters.sort || 'latest'"
                    @change="setSort"
                >
                    <option value="latest">Newest first</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="name">Name: A-Z</option>
                </select>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[240px_1fr]">
                <!-- Filters -->
                <aside class="space-y-6">
                    <div v-if="hasActiveFilters" class="flex flex-wrap gap-2">
                        <Link
                            :href="routes.productsIndex()"
                            class="inline-flex items-center gap-1 rounded-full bg-gray-900 px-3 py-1 text-xs font-medium text-white"
                        >
                            Clear all <X class="h-3 w-3" />
                        </Link>
                    </div>

                    <div>
                        <h3 class="mb-3 flex items-center gap-2 text-sm font-bold text-gray-900">
                            <SlidersHorizontal class="h-4 w-4" /> Categories
                        </h3>
                        <ul class="space-y-1">
                            <li v-for="category in categories" :key="category.id">
                                <button
                                    type="button"
                                    class="w-full rounded-lg px-3 py-1.5 text-left text-sm transition"
                                    :class="filters.category === category.slug
                                        ? 'bg-green-50 font-semibold text-green-700'
                                        : 'text-gray-600 hover:bg-gray-100'"
                                    @click="toggleCategory(category.slug)"
                                >
                                    {{ category.name }}
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="mb-3 text-sm font-bold text-gray-900">Brands</h3>
                        <ul class="space-y-1">
                            <li v-for="brand in brands" :key="brand.id">
                                <button
                                    type="button"
                                    class="w-full rounded-lg px-3 py-1.5 text-left text-sm transition"
                                    :class="filters.brand === brand.slug
                                        ? 'bg-green-50 font-semibold text-green-700'
                                        : 'text-gray-600 hover:bg-gray-100'"
                                    @click="toggleBrand(brand.slug)"
                                >
                                    {{ brand.name }}
                                </button>
                            </li>
                        </ul>
                    </div>
                </aside>

                <!-- Grid -->
                <div>
                    <div v-if="products.data.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
                        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                    </div>
                    <div v-else class="rounded-2xl border border-dashed border-gray-300 py-20 text-center">
                        <p class="text-gray-500">No products match these filters.</p>
                        <Link :href="routes.productsIndex()" class="mt-2 inline-block text-sm font-medium text-green-700 hover:underline">
                            Clear filters
                        </Link>
                    </div>

                    <Pagination :links="products.links" />
                </div>
            </div>
        </div>
    </MainLayout>
</template>
