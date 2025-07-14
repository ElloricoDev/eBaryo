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
    <div class="flex flex-col items-center justify-center">
        <div
            class="w-[350px] bg-white border-2 border-green-600 rounded-2xl shadow-md p-6 mt-2 transition duration-200 hover:shadow-lg hover:border-green-700 animate-slide-up"
        >
            <h1 class="text-center text-green-700 text-2xl font-semibold mb-6">
                <font-awesome-icon icon="envelope" class="mr-2" /> Forgot
                Password
            </h1>

            <form
                @submit.prevent="form.post(route('password.email'))"
                class="space-y-5"
            >
                <div class="relative">
                    <input
                        v-model="form.email"
                        type="email"
                        id="email"
                        required
                        autofocus
                        class="w-full border rounded-md px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-300 peer bg-transparent placeholder-transparent"
                        :class="{ 'border-red-500': form.errors.email }"
                        placeholder="Email"
                        aria-label="Email"
                    />
                    <label
                        for="email"
                        class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-focus:-top-4 peer-focus:text-xs peer-focus:text-green-600 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:font-semibold bg-white px-1"
                        :class="{
                            '-top-4 text-xs text-green-600 font-semibold':
                                form.email,
                        }"
                    >
                        <font-awesome-icon icon="envelope" /> Email
                    </label>
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
                        class="w-full bg-green-600 text-white py-2 px-4 rounded shadow-sm hover:bg-green-700 transition disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        <font-awesome-icon icon="paper-plane" class="mr-1" />
                        {{
                            form.processing
                                ? "Sending..."
                                : "Send Password Reset Link"
                        }}
                    </button>
                </div>

                <div
                    v-if="$page.props.status"
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded"
                >
                    {{ $page.props.status }}
                </div>
            </form>

            <div class="text-center mt-6 space-y-2">
                <Link
                    :href="route('login')"
                    class="text-green-700 hover:underline block"
                >
                    <font-awesome-icon icon="right-to-bracket" /> Back to Login
                </Link>
                <Link
                    :href="route('register')"
                    class="text-green-700 hover:underline block"
                >
                    <font-awesome-icon icon="user-plus" /> Don't have an
                    account? Register
                </Link>
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

input::placeholder {
    color: transparent;
}

label {
    transition: all 0.2s;
}
</style>
