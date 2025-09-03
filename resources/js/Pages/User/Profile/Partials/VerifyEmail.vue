<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from "sweetalert2";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faEnvelopeCircleCheck,
    faKey,
    faPaperPlane,
    faEnvelope,
    faCheckCircle,
    faExclamationTriangle,
    faArrowRotateRight,
} from "@fortawesome/free-solid-svg-icons";
library.add(faEnvelopeCircleCheck, faKey, faPaperPlane, faEnvelope, faCheckCircle, faExclamationTriangle, faArrowRotateRight);

const { props } = usePage();
const user = props.user;
const isGoogleUser = user.google_id !== null;
const isVerified = ref(computed(() => !!user.email_verified_at));

const form = ref({
    password: "",
});

const submitting = ref(false);

const submit = () => {
    submitting.value = true;
    form.value.post(route("user.profile.sendVerificationEmail"), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Verification Email Sent",
                text: "A verification email has been sent to your email address.",
                timer: 2000,
                showConfirmButton: false,
            });
            form.value.reset();
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text:
                    form.value.errors.password ||
                    form.value.errors.message ||
                    "Failed to send verification email.",
            });
        },
        onFinish: () => {
            submitting.value = false;
        },
    });
};

const resendCooldown = ref(0);

const resendVerification = async () => {
    try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        // Start cooldown
        resendCooldown.value = 60;
        const interval = setInterval(() => {
            resendCooldown.value--;
            if (resendCooldown.value <= 0) {
                clearInterval(interval);
            }
        }, 1000);
        
        // Show success message
        // You can integrate with your actual verification resend logic here
    } catch (error) {
        console.error('Failed to resend verification email:', error);
    }
};

const refreshPage = () => {
    window.location.reload();
};

onMounted(() => {
    // Check if user just verified their email
    if (window.location.search.includes('verified=1')) {
        // Clear the URL parameter
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>

<template>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 hover:shadow-md transition-shadow duration-300">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center">
                <font-awesome-icon icon="envelope" class="text-white text-xl" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Email Verification</h3>
                <p class="text-slate-600 text-sm">Verify your email address to unlock all features</p>
            </div>
        </div>

        <!-- Verification Status -->
        <div v-if="user.email_verified_at" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl text-green-800">
            <div class="flex items-center gap-3">
                <font-awesome-icon icon="check-circle" class="text-green-600" />
                <div>
                    <div class="font-semibold">✅ Email Verified</div>
                    <div class="text-sm">Your email has been successfully verified. You have access to all features.</div>
                </div>
            </div>
        </div>

        <!-- Unverified Email -->
        <div v-else class="space-y-6">
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800">
                <div class="flex items-center gap-3">
                    <font-awesome-icon icon="exclamation-triangle" class="text-amber-600" />
                    <div>
                        <div class="font-semibold">⚠️ Email Not Verified</div>
                        <div class="text-sm">Please verify your email address to access all features and ensure account security.</div>
                    </div>
                </div>
            </div>

            <!-- Verification Actions -->
            <div class="space-y-4">
                <div class="text-sm text-slate-600">
                    <p class="mb-2">If you haven't received a verification email:</p>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li>Check your spam/junk folder</li>
                        <li>Make sure the email address is correct</li>
                        <li>Wait a few minutes for the email to arrive</li>
                    </ul>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        @click="resendVerification"
                        :disabled="resendCooldown > 0"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-3 px-6 rounded-2xl font-semibold shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-blue-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="resendCooldown === 0" class="flex items-center justify-center gap-2">
                            <font-awesome-icon icon="paper-plane" />
                            Resend Verification Email
                        </span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            Resend in {{ resendCooldown }}s
                        </span>
                    </button>

                    <button
                        @click="refreshPage"
                        class="px-6 py-3 bg-slate-100 text-slate-700 rounded-2xl hover:bg-slate-200 transition-all duration-200 font-medium hover:shadow-md"
                    >
                        <font-awesome-icon icon="arrow-rotate-right" class="mr-2" />
                        Refresh
                    </button>
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
