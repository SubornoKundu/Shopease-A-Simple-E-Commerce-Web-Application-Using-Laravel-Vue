<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { KeyRound, Mail, ShieldCheck, Store, User } from '@lucide/vue';
import { routes } from '@/routes';

const props = defineProps({
    pendingEmail: { type: String, default: null },
    pendingName: { type: String, default: null },
    demoOtp: { type: String, default: null },
    resendWaitSeconds: { type: Number, default: 0 },
    otpLength: { type: Number, default: 6 },
});

// Derived purely from props (not local state) so a redirect back to this
// same page after POST /otp/send or /otp/verify always reflects the
// server's view of where the user is in the flow, even if Vue reuses
// this component instance instead of remounting it.
const step = computed(() => (props.pendingEmail ? 'otp' : 'email'));

const emailForm = useForm({
    name: props.pendingName ?? '',
    email: props.pendingEmail ?? '',
});

const otpForm = useForm({
    otp: '',
});

function sendOtp() {
    emailForm.post(routes.otpSend());
}

function verifyOtp() {
    otpForm.post(routes.otpVerify(), {
        onError: () => (otpForm.otp = ''),
    });
}

function resend() {
    router.post(routes.otpResend(), {}, { preserveScroll: true });
}

function useAnotherEmail() {
    router.post(routes.otpCancel());
}

const cooldown = ref(props.resendWaitSeconds);
let cooldownTimer = null;

watch(
    () => props.resendWaitSeconds,
    (value) => {
        cooldown.value = value;
    },
);

onMounted(() => {
    cooldownTimer = window.setInterval(() => {
        if (cooldown.value > 0) cooldown.value -= 1;
    }, 1000);
});
onBeforeUnmount(() => window.clearInterval(cooldownTimer));
</script>

<template>
    <Head title="Login" />

    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-10">
        <div class="w-full max-w-sm">
            <div class="mb-6 flex flex-col items-center gap-2">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-600 text-white">
                    <Store class="h-6 w-6" />
                </span>
                <span class="text-xl font-bold text-gray-900">Shop<span class="text-green-600">Ease</span></span>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <!-- Step 1: name + email -->
                <template v-if="step === 'email'">
                    <h1 class="text-lg font-bold text-gray-900">Login or create an account</h1>
                    <p class="mt-1 text-sm text-gray-500">We'll email you a one-time code -- no password needed.</p>

                    <form class="mt-5 space-y-4" @submit.prevent="sendOtp">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Full Name</label>
                            <div class="relative">
                                <User class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                                <input
                                    v-model="emailForm.name"
                                    type="text"
                                    autocomplete="name"
                                    placeholder="Suborno Kundu"
                                    class="w-full rounded-lg border border-gray-200 py-2.5 pr-3 pl-9 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/20 focus:outline-none"
                                >
                            </div>
                            <p v-if="emailForm.errors.name" class="mt-1 text-xs text-rose-600">{{ emailForm.errors.name }}</p>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Email Address</label>
                            <div class="relative">
                                <Mail class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                                <input
                                    v-model="emailForm.email"
                                    type="email"
                                    autocomplete="email"
                                    placeholder="you@example.com"
                                    class="w-full rounded-lg border border-gray-200 py-2.5 pr-3 pl-9 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/20 focus:outline-none"
                                >
                            </div>
                            <p v-if="emailForm.errors.email" class="mt-1 text-xs text-rose-600">{{ emailForm.errors.email }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="emailForm.processing"
                            class="w-full rounded-full bg-green-600 py-2.5 text-sm font-semibold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ emailForm.processing ? 'Sending code...' : 'Send Verification Code' }}
                        </button>
                    </form>
                </template>

                <!-- Step 2: enter OTP -->
                <template v-else>
                    <h1 class="text-lg font-bold text-gray-900">Enter verification code</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        We sent a {{ otpLength }}-digit code to <span class="font-medium text-gray-700">{{ pendingEmail }}</span>.
                    </p>

                    <div v-if="demoOtp" class="mt-4 flex items-center gap-2 rounded-xl border border-dashed border-amber-300 bg-amber-50 px-3 py-2">
                        <ShieldCheck class="h-4 w-4 shrink-0 text-amber-600" />
                        <p class="text-xs text-amber-800">
                            Demo mode: your code is
                            <span class="font-mono text-sm font-bold tracking-widest">{{ demoOtp }}</span>
                        </p>
                    </div>

                    <form class="mt-5 space-y-4" @submit.prevent="verifyOtp">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ otpLength }}-Digit Code</label>
                            <div class="relative">
                                <KeyRound class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                                <input
                                    v-model="otpForm.otp"
                                    type="text"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    :maxlength="otpLength"
                                    autofocus
                                    placeholder="000000"
                                    class="w-full rounded-lg border border-gray-200 py-2.5 pr-3 pl-9 text-center text-lg font-semibold tracking-[0.4em] focus:border-green-500 focus:ring-2 focus:ring-green-500/20 focus:outline-none"
                                >
                            </div>
                            <p v-if="otpForm.errors.otp" class="mt-1 text-xs text-rose-600">{{ otpForm.errors.otp }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="otpForm.processing"
                            class="w-full rounded-full bg-green-600 py-2.5 text-sm font-semibold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ otpForm.processing ? 'Verifying...' : 'Verify & Continue' }}
                        </button>
                    </form>

                    <div class="mt-4 flex items-center justify-between text-xs">
                        <button
                            type="button"
                            class="font-medium text-green-700 hover:underline disabled:cursor-not-allowed disabled:text-gray-400 disabled:no-underline"
                            :disabled="cooldown > 0"
                            @click="resend"
                        >
                            {{ cooldown > 0 ? `Resend code in ${cooldown}s` : 'Resend code' }}
                        </button>
                        <button type="button" class="text-gray-400 hover:text-gray-600" @click="useAnotherEmail">
                            Use a different email
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
