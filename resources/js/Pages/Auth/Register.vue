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
        <div class="flex flex-col items-center justify-center">
            <div
                class="absolute inset-0 -z-10 bg-gradient-to-br from-green-200 via-green-50 to-white animate-gradient-move"
            ></div>
            <div
                class="w-[350px] bg-white border-2 border-green-600 rounded-2xl shadow-md p-6 mt-2 transition duration-200 hover:shadow-lg hover:border-green-700 animate-slide-up"
            >
                <h1
                    class="text-center text-green-700 text-2xl font-semibold mb-6"
                >
                    <font-awesome-icon icon="user-plus" class="mr-2" /> Register
                </h1>

                <form @submit.prevent="submit" class="space-y-5">
                    <div class="relative">
                        <input
                            id="name"
                            type="text"
                            v-model="form.user_name"
                            class="w-full border rounded-md px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-300 peer bg-transparent placeholder-transparent"
                            :class="{ 'border-red-500': form.errors.user_name }"
                            autofocus
                            placeholder=" "
                            aria-label="Full Name"
                        />
                        <label
                            for="name"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-focus:-top-6 peer-focus:text-xs peer-focus:text-green-600 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-6 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.user_name && form.user_name.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="person" /> Full Name
                        </label>
                        <div
                            v-if="form.errors.user_name"
                            class="text-sm text-red-600 mt-1"
                        >
                            {{ form.errors.user_name }}
                        </div>
                    </div>

                    <div class="relative">
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            class="w-full border rounded-md px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-300 peer bg-transparent placeholder-transparent"
                            :class="{ 'border-red-500': form.errors.email }"
                            placeholder=" "
                            aria-label="Email address"
                        />
                        <label
                            for="email"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-focus:-top-6 peer-focus:text-xs peer-focus:text-green-600 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-6 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.email && form.email.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="envelope" /> Email address
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
                            id="password"
                            type="password"
                            v-model="form.password"
                            class="w-full border rounded-md px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-300 peer bg-transparent placeholder-transparent"
                            :class="{ 'border-red-500': form.errors.password }"
                            placeholder=" "
                            aria-label="Password"
                        />
                        <label
                            for="password"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-focus:-top-6 peer-focus:text-xs peer-focus:text-green-600 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-6 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.password && form.password.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="lock" /> Password
                        </label>
                        <div
                            v-if="form.errors.password"
                            class="text-sm text-red-600 mt-1"
                        >
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div class="relative">
                        <input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            class="w-full border rounded-md px-3 py-3 focus:outline-none focus:ring-2 focus:ring-green-300 peer bg-transparent placeholder-transparent"
                            placeholder=" "
                            aria-label="Confirm Password"
                        />
                        <label
                            for="password_confirmation"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-focus:-top-6 peer-focus:text-xs peer-focus:text-green-600 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-6 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.password_confirmation &&
                                    form.password_confirmation.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="lock" /> Confirm Password
                        </label>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="w-full bg-green-600 text-white py-2 px-4 rounded shadow-sm hover:bg-green-700 transition disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <font-awesome-icon icon="user-plus" class="mr-1" />
                            {{
                                form.processing ? "Registering..." : "Register"
                            }}
                        </button>
                    </div>

                    <div>
                        <a
                            :href="route('google.redirect')"
                            class="w-full inline-flex justify-center items-center border border-green-600 text-green-600 py-2 px-4 rounded hover:bg-green-50 transition"
                        >
                            <font-awesome-icon
                                :icon="['fab', 'google']"
                                class="mr-2 text-red-500"
                            />
                            Sign in with Google
                        </a>
                    </div>
                </form>

                <div class="text-center mt-6">
                    <font-awesome-icon
                        icon="right-to-bracket"
                        class="text-green-700"
                    />
                    <Link
                        :href="route('login')"
                        class="ml-1 text-green-700 hover:underline"
                        >Login</Link
                    >
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
