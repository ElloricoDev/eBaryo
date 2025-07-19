<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { useForm, Link, Head } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faRightToBracket,
    faPerson,
    faLock,
    faEnvelope,
    faUserPlus,
    faEye,
    faEyeSlash,
} from "@fortawesome/free-solid-svg-icons";
import { faGoogle } from "@fortawesome/free-brands-svg-icons";
import { ref } from "vue";

library.add(
    faRightToBracket,
    faPerson,
    faLock,
    faEnvelope,
    faUserPlus,
    faGoogle,
    faEye,
    faEyeSlash
);

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route("login"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Login" />
    <GuestLayout>
        <div class="flex flex-col items-center justify-center">
            <div
                class="w-[350px] bg-white border-2 border-green-600 rounded-2xl shadow-md p-6 mt-2 transition duration-200 hover:shadow-lg hover:border-green-700 animate-slide-up"
            >
                <h1
                    class="text-center text-green-700 text-2xl font-semibold mb-6"
                >
                    <font-awesome-icon icon="right-to-bracket" class="mr-2" />
                    Login
                </h1>
                <form @submit.prevent="submit" class="space-y-5">
                    <div class="relative">
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            class="w-full border rounded-md px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-300 peer bg-transparent placeholder-transparent"
                            :class="{ 'border-red-500': form.errors.email }"
                            autofocus
                            placeholder=" "
                            aria-label="Email"
                        />
                        <label
                            for="email"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-focus:-top-3 peer-focus:text-xs peer-focus:text-green-600 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-6 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.email && form.email.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="person" /> Email
                        </label>
                        <div
                            v-if="form.errors.email"
                            class="text-sm text-red-600 mt-1"
                        >
                            {{ form.errors.email }}
                        </div>
                    </div>
                    <div class="relative">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            v-model="form.password"
                            class="w-full border rounded-md px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-300 peer bg-transparent placeholder-transparent"
                            :class="{ 'border-red-500': form.errors.password }"
                            placeholder=" "
                            aria-label="Password"
                        />
                        <label
                            for="password"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-focus:-top-3 peer-focus:text-xs peer-focus:text-green-600 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-6 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.password && form.password.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="lock" /> Password
                        </label>
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            tabindex="-1"
                            class="absolute right-3 top-3 text-green-400 hover:text-green-600 focus:outline-none"
                            :aria-label="
                                showPassword ? 'Hide password' : 'Show password'
                            "
                        >
                            <font-awesome-icon
                                :icon="showPassword ? 'eye-slash' : 'eye'"
                            />
                        </button>
                        <div
                            v-if="form.errors.password"
                            class="text-sm text-red-600 mt-1"
                        >
                            {{ form.errors.password }}
                        </div>
                        <div class="text-right mt-2">
                            <Link
                                :href="route('password.request')"
                                class="text-green-700 text-sm hover:underline"
                            >
                                <font-awesome-icon icon="envelope" /> Forgot
                                password?
                            </Link>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input
                            id="remember"
                            type="checkbox"
                            v-model="form.remember"
                            class="border-green-500 text-green-600 focus:ring-green-500 rounded"
                        />
                        <label for="remember" class="text-green-700 text-sm"
                            >Remember Me</label
                        >
                    </div>
                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-green-600 text-white py-2 px-4 rounded shadow-sm hover:bg-green-700 focus:bg-green-800 transition disabled:opacity-50 animate-pulse-cta focus:outline-none focus:ring-2 focus:ring-green-400 flex items-center justify-center gap-2"
                            aria-label="Login"
                        >
                            <span v-if="!form.processing">
                                <font-awesome-icon icon="right-to-bracket" />
                                Login
                            </span>
                            <span v-else>
                                <svg
                                    class="animate-spin h-5 w-5 mr-2 inline-block text-white"
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
                                Logging in...
                            </span>
                        </button>
                    </div>
                    <div class="flex items-center my-2">
                        <div class="flex-grow border-t border-green-200"></div>
                        <span class="mx-2 text-gray-400 text-xs">or</span>
                        <div class="flex-grow border-t border-green-200"></div>
                    </div>
                    <div>
                        <a
                            :href="route('google.redirect')"
                            class="w-full inline-flex justify-center items-center border border-green-600 text-green-600 py-2 px-4 rounded hover:bg-green-50 focus:bg-green-100 transition focus:outline-none focus:ring-2 focus:ring-green-400"
                            aria-label="Login with Google"
                        >
                            <font-awesome-icon
                                :icon="['fab', 'google']"
                                class="mr-2 text-red-500"
                            />
                            Login with Google
                        </a>
                    </div>
                </form>
                <div class="text-center mt-6">
                    <p class="text-gray-600">Don't have an account?</p>
                    <Link
                        :href="route('register')"
                        class="text-green-700 hover:underline mt-2 inline-block"
                    >
                        <font-awesome-icon icon="user-plus" /> Register
                    </Link>
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

@keyframes fade-in {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.animate-fade-in {
    animation: fade-in 1s both;
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

@keyframes pulse-cta {
    0%,
    100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
    }

    50% {
        box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
    }
}

.animate-pulse-cta {
    animation: pulse-cta 2s infinite;
}

input:focus,
button:focus,
a:focus {
    outline: 2px solid #34d399;
    outline-offset: 2px;
}
</style>
