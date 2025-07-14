<script setup>
import { ref, computed } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faEnvelopeCircleCheck,
    faKey,
    faPaperPlane,
} from "@fortawesome/free-solid-svg-icons";
library.add(faEnvelopeCircleCheck, faKey, faPaperPlane);

const { props } = usePage();
const user = props.user;
const isGoogleUser = user.google_id !== null;
const isVerified = computed(() => !!user.email_verified_at);

const form = useForm({
    password: "",
});

const submitting = ref(false);

const submit = () => {
    submitting.value = true;
    form.post(route("user.profile.sendVerificationEmail"), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Verification Email Sent",
                text: "A verification email has been sent to your email address.",
                timer: 2000,
                showConfirmButton: false,
            });
            form.reset();
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text:
                    form.errors.password ||
                    form.errors.message ||
                    "Failed to send verification email.",
            });
        },
        onFinish: () => {
            submitting.value = false;
        },
    });
};
</script>

<template>
    <div
        class="relative animate-fade-in bg-white bg-opacity-90 border-2 border-green-600 rounded-2xl shadow-xl mb-6 overflow-hidden"
    >
        <div
            class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 via-green-50 to-white animate-gradient-move"
        ></div>
        <div
            class="bg-green-600 text-white px-4 py-3 rounded-t-2xl flex items-center gap-2 font-medium"
        >
            <font-awesome-icon icon="envelope-circle-check" /> Verify Email
        </div>
        <div class="border-t border-green-100"></div>
        <div class="p-6">
            <div
                v-if="isVerified"
                class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded mb-4"
            >
                Your email is already verified.
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div v-if="!isGoogleUser && !isVerified" class="relative">
                    <input
                        v-model="form.password"
                        type="password"
                        id="verify_password"
                        class="peer w-full border border-green-300 rounded px-3 py-2 bg-transparent focus:border-green-600 focus:ring-2 focus:ring-green-200 transition placeholder-transparent"
                        placeholder="Password"
                        :disabled="isVerified"
                    />
                    <label
                        for="verify_password"
                        class="absolute left-3 top-2 text-green-700 font-medium pointer-events-none transition-all peer-placeholder-shown:top-2 peer-placeholder-shown:text-base peer-focus:-top-4 peer-focus:text-xs peer-focus:text-green-600 bg-white px-1"
                    >
                        <font-awesome-icon icon="key" /> Password
                    </label>
                    <div
                        v-if="form.errors.password"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.password }}
                    </div>
                </div>

                <button
                    type="submit"
                    class="bg-green-600 hover:bg-green-700 focus:bg-green-800 text-white w-full py-2 px-4 rounded shadow-sm font-semibold flex items-center justify-center gap-2 animate-pulse-cta focus:outline-none focus:ring-2 focus:ring-green-400 transition disabled:opacity-60 disabled:cursor-not-allowed"
                    :disabled="submitting || form.processing || isVerified"
                >
                    <font-awesome-icon icon="paper-plane" />
                    <span v-if="submitting || form.processing">
                        <svg
                            class="inline w-4 h-4 animate-spin mr-1"
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
                                d="M4 12a8 8 0 018-8v8z"
                            ></path>
                        </svg>
                        Sending...
                    </span>
                    <span v-else>Send Verification Email</span>
                </button>
            </form>
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
