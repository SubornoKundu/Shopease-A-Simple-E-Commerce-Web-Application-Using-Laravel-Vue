<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { BadgeCheck, Mail, User } from '@lucide/vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    user: { type: Object, required: true },
});

const form = useForm({
    name: props.user.name,
    phone: props.user.phone ?? '',
});

function submit() {
    form.put('/profile', { preserveScroll: true });
}

function formatDate(value) {
    return new Date(value).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
}
</script>

<template>
    <Head title="Your Profile" />

    <MainLayout>
        <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-2xl font-bold text-gray-900">Your Profile</h1>

            <div class="mb-6 flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-6">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-xl font-bold text-green-700">
                    {{ user.name.charAt(0).toUpperCase() }}
                </span>
                <div>
                    <p class="flex items-center gap-1.5 font-bold text-gray-900">
                        {{ user.name }}
                        <BadgeCheck class="h-4 w-4 text-green-600" title="Verified" />
                    </p>
                    <p class="flex items-center gap-1 text-sm text-gray-500">
                        <Mail class="h-3.5 w-3.5" /> {{ user.email }}
                    </p>
                    <p class="mt-0.5 text-xs text-gray-400">Member since {{ formatDate(user.created_at) }}</p>
                </div>
            </div>

            <form class="space-y-4 rounded-2xl border border-gray-200 bg-white p-6" @submit.prevent="submit">
                <h2 class="flex items-center gap-2 font-bold text-gray-900">
                    <User class="h-4 w-4" /> Edit Details
                </h2>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Full Name</label>
                    <input v-model="form.name" type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/20 focus:outline-none">
                    <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Phone Number</label>
                    <input v-model="form.phone" type="text" placeholder="01XXXXXXXXX" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/20 focus:outline-none">
                    <p v-if="form.errors.phone" class="mt-1 text-xs text-rose-600">{{ form.errors.phone }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Email Address</label>
                    <input :value="user.email" type="email" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                    <p class="mt-1 text-xs text-gray-400">Your email is used to sign in and can't be changed here.</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-full bg-green-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </button>
            </form>
        </div>
    </MainLayout>
</template>
