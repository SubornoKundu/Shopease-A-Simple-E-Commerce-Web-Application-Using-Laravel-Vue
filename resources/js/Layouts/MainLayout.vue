<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Heart, LogOut, Menu, Package, Search, ShoppingCart, Store, User, X,
} from '@lucide/vue';
import { routes } from '@/routes';
import FlashMessages from '@/Components/FlashMessages.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const cartCount = computed(() => page.props.cartCount ?? 0);
const wishlistCount = computed(() => page.props.wishlistCount ?? 0);

const mobileMenuOpen = ref(false);
const search = ref(new URLSearchParams(window.location.search).get('q') ?? '');

function submitSearch() {
    router.get(routes.productsIndex({ q: search.value || undefined }));
    mobileMenuOpen.value = false;
}

function logout() {
    router.post(routes.logout());
}

const year = new Date().getFullYear();
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gray-50">
        <FlashMessages />

        <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/95 backdrop-blur">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between gap-4">
                    <Link :href="routes.home()" class="flex shrink-0 items-center gap-2 text-green-700">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-green-600 text-white">
                            <Store class="h-5 w-5" />
                        </span>
                        <span class="text-lg font-bold tracking-tight text-gray-900">Shop<span class="text-green-600">Ease</span></span>
                    </Link>

                    <form class="hidden max-w-md flex-1 md:flex" @submit.prevent="submitSearch">
                        <div class="relative w-full">
                            <Search class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="search"
                                type="search"
                                placeholder="Search products..."
                                class="w-full rounded-full border border-gray-200 bg-gray-50 py-2 pr-4 pl-9 text-sm focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 focus:outline-none"
                            >
                        </div>
                    </form>

                    <nav class="hidden items-center gap-6 text-sm font-medium text-gray-600 md:flex">
                        <Link :href="routes.home()" class="hover:text-green-700">Home</Link>
                        <Link :href="routes.productsIndex()" class="hover:text-green-700">Shop</Link>
                    </nav>

                    <div class="flex items-center gap-1 sm:gap-2">
                        <Link :href="routes.wishlistIndex()" class="relative flex h-10 w-10 items-center justify-center rounded-full text-gray-600 hover:bg-gray-100" title="Wishlist">
                            <Heart class="h-5 w-5" />
                            <span v-if="wishlistCount > 0" class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-600 text-[10px] font-bold text-white">
                                {{ wishlistCount }}
                            </span>
                        </Link>

                        <Link :href="routes.cartIndex()" class="relative flex h-10 w-10 items-center justify-center rounded-full text-gray-600 hover:bg-gray-100" title="Cart">
                            <ShoppingCart class="h-5 w-5" />
                            <span v-if="cartCount > 0" class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-green-600 text-[10px] font-bold text-white">
                                {{ cartCount }}
                            </span>
                        </Link>

                        <template v-if="user">
                            <Link :href="routes.ordersIndex()" class="hidden h-10 w-10 items-center justify-center rounded-full text-gray-600 hover:bg-gray-100 sm:flex" title="My Orders">
                                <Package class="h-5 w-5" />
                            </Link>
                            <Link :href="routes.profileEdit()" class="hidden h-10 w-10 items-center justify-center rounded-full text-gray-600 hover:bg-gray-100 sm:flex" title="Profile">
                                <User class="h-5 w-5" />
                            </Link>
                            <button type="button" class="hidden h-10 w-10 items-center justify-center rounded-full text-gray-600 hover:bg-gray-100 sm:flex" title="Logout" @click="logout">
                                <LogOut class="h-5 w-5" />
                            </button>
                        </template>
                        <Link v-else :href="routes.login()" class="hidden shrink-0 rounded-full bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 sm:block">
                            Login
                        </Link>

                        <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-gray-600 hover:bg-gray-100 md:hidden" @click="mobileMenuOpen = !mobileMenuOpen">
                            <component :is="mobileMenuOpen ? X : Menu" class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <div v-if="mobileMenuOpen" class="space-y-4 border-t border-gray-100 py-4 md:hidden">
                    <form @submit.prevent="submitSearch">
                        <div class="relative">
                            <Search class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="search"
                                type="search"
                                placeholder="Search products..."
                                class="w-full rounded-full border border-gray-200 bg-gray-50 py-2 pr-4 pl-9 text-sm focus:outline-none"
                            >
                        </div>
                    </form>
                    <nav class="flex flex-col gap-1 text-sm font-medium text-gray-700">
                        <Link :href="routes.home()" class="rounded-lg px-3 py-2 hover:bg-gray-100">Home</Link>
                        <Link :href="routes.productsIndex()" class="rounded-lg px-3 py-2 hover:bg-gray-100">Shop</Link>
                        <template v-if="user">
                            <Link :href="routes.ordersIndex()" class="rounded-lg px-3 py-2 hover:bg-gray-100">My Orders</Link>
                            <Link :href="routes.profileEdit()" class="rounded-lg px-3 py-2 hover:bg-gray-100">Profile</Link>
                            <button type="button" class="rounded-lg px-3 py-2 text-left hover:bg-gray-100" @click="logout">Logout</button>
                        </template>
                        <Link v-else :href="routes.login()" class="rounded-lg bg-green-600 px-3 py-2 text-center font-semibold text-white">Login</Link>
                    </nav>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="flex items-center gap-2 text-gray-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-600 text-white">
                            <Store class="h-4 w-4" />
                        </span>
                        <span class="font-bold">Shop<span class="text-green-600">Ease</span></span>
                    </div>
                    <p class="text-center text-sm text-gray-500">
                        A Laravel + Vue e-commerce demo -- DIU Batch-12, Module-22 assignment.
                    </p>
                    <p class="text-sm text-gray-400">&copy; {{ year }} ShopEase</p>
                </div>
            </div>
        </footer>
    </div>
</template>
