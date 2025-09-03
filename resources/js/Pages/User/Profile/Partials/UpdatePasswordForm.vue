<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faShieldHalved,
    faKey,
    faArrowRotateRight,
} from "@fortawesome/free-solid-svg-icons";
library.add(faShieldHalved, faKey, faArrowRotateRight);

const { props } = usePage();
const user = props.user;
const isGoogleUser = user.google_id !== null;

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.put(route("password.update"), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Password Updated",
                text: "Your password has been successfully updated.",
                timer: 2000,
                showConfirmButton: false,
            });
            form.reset();
        },
    });
};
</script>

<template>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 hover:shadow-md transition-shadow duration-300">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center">
                <font-awesome-icon icon="shield-halved" class="text-white text-xl" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Update Password</h3>
                <p class="text-slate-600 text-sm">Keep your account secure with a strong password</p>
            </div>
        </div>

        <!-- Google User Notice -->
        <div v-if="isGoogleUser" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-2xl text-blue-800">
            <div class="flex items-center gap-3">
                <font-awesome-icon icon="info-circle" class="text-blue-600" />
                <div>
                    <div class="font-semibold">Google Account</div>
                    <div class="text-sm">You cannot change your password because you signed in using Google.</div>
                </div>
            </div>
        </div>

        <!-- Password Form -->
        <form v-else @submit.prevent="submit" class="space-y-6">
            <!-- Current Password -->
            <div class="space-y-2">
                <label for="current_password" class="block text-sm font-semibold text-slate-700">
                    <font-awesome-icon icon="key" class="mr-2 text-green-600" />
                    Current Password
                </label>
                <input
                    v-model="form.current_password"
                    type="password"
                    id="current_password"
                    class="w-full border-2 border-slate-200 rounded-2xl px-4 py-3 bg-transparent focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 placeholder-slate-400"
                    placeholder="Enter your current password"
                />
                <div v-if="form.errors.current_password" class="text-red-500 text-sm">
                    {{ form.errors.current_password }}
                </div>
            </div>

            <!-- New Password -->
            <div class="space-y-2">
                <label for="new_password" class="block text-sm font-semibold text-slate-700">
                    <font-awesome-icon icon="key" class="mr-2 text-green-600" />
                    New Password
                </label>
                <input
                    v-model="form.password"
                    type="password"
                    id="new_password"
                    class="w-full border-2 border-slate-200 rounded-2xl px-4 py-3 bg-transparent focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 placeholder-slate-400"
                    placeholder="Enter your new password"
                />
                <div v-if="form.errors.password" class="text-red-500 text-sm">
                    {{ form.errors.password }}
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">
                    <font-awesome-icon icon="key" class="mr-2 text-green-600" />
                    Confirm New Password
                </label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    id="password_confirmation"
                    class="w-full border-2 border-slate-200 rounded-2xl px-4 py-3 bg-transparent focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 placeholder-slate-400"
                    placeholder="Confirm your new password"
                />
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :disabled="form.processing"
                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white py-4 px-6 rounded-2xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-green-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span v-if="!form.processing" class="flex items-center justify-center gap-3">
                    <font-awesome-icon icon="arrow-rotate-right" />
                    Update Password
                </span>
                <span v-else class="flex items-center justify-center gap-3">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    Updating...
                </span>
            </button>
        </form>
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
