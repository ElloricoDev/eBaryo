<script setup>
import { useForm, Link, Head } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faEnvelope,
    faPaperPlane,
    faRightToBracket,
    faUserPlus,
} from "@fortawesome/free-solid-svg-icons";

library.add(faEnvelope, faPaperPlane, faRightToBracket, faUserPlus);

const form = useForm({
    email: "",
});

defineOptions({
    layout: GuestLayout,
});
</script>

<template>
    <Head title="Forgot Password" />
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-50 via-white to-green-50 relative overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <img 
                    src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                    alt="Library books background" 
                    class="w-full h-full object-cover opacity-40"
                />
            </div>
            
            <div class="max-w-md w-full space-y-8 relative z-10">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                    <div class="text-center mb-8">
                        <img src="/favicon1.png" alt="eBaryo logo" class="w-12 h-12 mx-auto mb-4" />
                        <h1 class="text-3xl font-bold text-slate-800">
                            Forgot your password?
                        </h1>
                        <p class="text-slate-600 mt-2">
                            No worries, we'll send you reset instructions
                        </p>
                    </div>

                    <form
                        @submit.prevent="form.post(route('password.email'))"
                        class="space-y-6"
                    >
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                                <font-awesome-icon icon="envelope" class="mr-2 text-slate-400" />
                                Email address
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                id="email"
                                required
                                autofocus
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.email }"
                                placeholder="Enter your email"
                                aria-label="Email"
                            />
                            <div
                                v-if="form.errors.email"
                                class="text-sm text-red-600 mt-1"
                            >
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <div>
                            <button
                                type="submit"
                                class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing" class="inline-flex items-center justify-center">
                                    <font-awesome-icon icon="paper-plane" class="mr-2" />
                                    Send reset link
                                </span>
                                <span v-else class="flex items-center justify-center">
                                    <svg
                                        class="animate-spin h-5 w-5 mr-2"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        ></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                        ></path>
                                    </svg>
                                    Sending...
                                </span>
                            </button>
                        </div>

                        <div
                            v-if="$page.props.status"
                            class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg"
                        >
                            {{ $page.props.status }}
                        </div>
                    </form>

                    <div class="text-center mt-6 space-y-3">
                        <Link
                            :href="route('login')"
                            class="text-green-600 hover:text-green-700 font-medium hover:underline block"
                        >
                            Back to sign in
                        </Link>
                        <p class="text-slate-600">
                            Don't have an account?
                            <Link
                                :href="route('register')"
                                class="text-green-600 hover:text-green-700 font-medium hover:underline"
                            >
                                Sign up
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
</template>

<style scoped>
@keyframes gradient-move {
    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}

.animate-gradient-move {
    background-size: 200% 200%;
    animation: gradient-move 8s ease-in-out infinite;
}

@keyframes slide-up {
    from {
        transform: translateY(30px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-slide-up {
    animation: slide-up 0.7s cubic-bezier(0.4, 0, 0.2, 1) both;
}

input:focus,
button:focus,
a:focus {
    outline: 2px solid #34d399;
    outline-offset: 2px;
}
label {
    transition: all 0.2s;
}
</style>
