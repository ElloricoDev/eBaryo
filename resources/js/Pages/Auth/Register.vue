<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { useForm, Link, Head } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faUserPlus,
    faPerson,
    faEnvelope,
    faLock,
    faRightToBracket,
} from "@fortawesome/free-solid-svg-icons";
import { faGoogle } from "@fortawesome/free-brands-svg-icons";

library.add(
    faUserPlus,
    faPerson,
    faEnvelope,
    faLock,
    faRightToBracket,
    faGoogle
);

const form = useForm({
    user_name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: (errors) => {
            Object.keys(errors).forEach((field) => {
                form[field] = "";
            });
        },
    });
};
</script>

<template>
    <Head title="Register" />
    <GuestLayout>
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
                            Create your account
                        </h1>
                        <p class="text-slate-600 mt-2">
                            Join our community today
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                                Full Name
                            </label>
                            <input
                                id="name"
                                type="text"
                                v-model="form.user_name"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.user_name }"
                                autofocus
                                placeholder="Enter your full name"
                                aria-label="Full Name"
                            />
                            <div
                                v-if="form.errors.user_name"
                                class="text-sm text-red-600 mt-1"
                            >
                                {{ form.errors.user_name }}
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                                Email address
                            </label>
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.email }"
                                placeholder="Enter your email"
                                aria-label="Email address"
                            />
                            <div
                                v-if="form.errors.email"
                                class="text-sm text-red-600 mt-1"
                            >
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                                Password
                            </label>
                            <input
                                id="password"
                                type="password"
                                v-model="form.password"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.password }"
                                placeholder="Create a password"
                                aria-label="Password"
                            />
                            <div
                                v-if="form.errors.password"
                                class="text-sm text-red-600 mt-1"
                            >
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                                Confirm Password
                            </label>
                            <input
                                id="password_confirmation"
                                type="password"
                                v-model="form.password_confirmation"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                placeholder="Confirm your password"
                                aria-label="Confirm Password"
                            />
                        </div>

                        <div>
                            <button
                                type="submit"
                                class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing">
                                    Create account
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
                                    Creating account...
                                </span>
                            </button>
                        </div>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-300" />
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-slate-500">Or continue with</span>
                            </div>
                        </div>

                        <div>
                            <a
                                :href="route('google.redirect')"
                                class="w-full inline-flex justify-center items-center border border-slate-300 text-slate-700 py-3 px-4 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                            >
                                <font-awesome-icon
                                    :icon="['fab', 'google']"
                                    class="mr-2 text-red-500"
                                />
                                Google
                            </a>
                        </div>
                    </form>

                    <div class="text-center mt-6">
                        <p class="text-slate-600">
                            Already have an account?
                            <Link
                                :href="route('login')"
                                class="text-green-600 hover:text-green-700 font-medium hover:underline"
                            >
                                Sign in
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
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
input::placeholder {
    color: transparent;
}
label {
    transition: all 0.2s;
}
</style>
