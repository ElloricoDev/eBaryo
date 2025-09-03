<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import UserLayout from "@/Layouts/UserLayout.vue";
import Swal from "sweetalert2";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faCommentDots, faArrowLeft } from "@fortawesome/free-solid-svg-icons";
library.add(faCommentDots, faArrowLeft);


const form = useForm({
    message: "",
    type: "general",
});

const { props } = usePage();
const user = props.auth?.user || null;
const isVerified = user && user.email_verified_at;

const feedbacks = props.feedbacks || [];
const hasNewResponses = props.hasNewResponses || false;

function submit() {
    form.post(route("feedback.store"), {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Thank you!",
                text: "Your feedback has been submitted.",
                timer: 2000,
                showConfirmButton: false,
            });
            form.reset();
        },
    });
}

function goBack() {
    const lastPage = sessionStorage.getItem("lastPage");
    if (lastPage) {
        window.location.href = lastPage;
        sessionStorage.removeItem("lastPage");
    } else {
        window.history.back();
    }
}

if (hasNewResponses) {
    Swal.fire({
        icon: "info",
        title: "New Response!",
        text: "An admin has responded to your feedback.",
        timer: 2500,
        showConfirmButton: false,
    });
}
</script>

<template>
    <Head title="Feedback" />
    <UserLayout>
        <!-- Back Button for Mobile -->
        <div class="mb-6">
            <button
                @click="goBack"
                class="block sm:hidden text-green-700 font-bold flex items-center gap-2 hover:text-green-800 transition-colors"
            >
                <font-awesome-icon icon="arrow-left" class="text-lg" />
                Back
            </button>
        </div>

        <!-- Hero Header -->
        <div class="mb-12 bg-gradient-to-br from-green-50 via-white to-emerald-50 rounded-3xl p-8 border border-green-100 shadow-sm">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4 flex items-center justify-center gap-3">
                    <font-awesome-icon icon="comment-dots" class="text-green-600" />
                    Share Your Thoughts
                </h1>
                <p class="text-xl text-slate-600 leading-relaxed">
                    We value your feedback! Help us improve your reading experience
                </p>
            </div>
        </div>

        <!-- Feedback Form Section -->
        <div class="max-w-4xl mx-auto mb-16">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 hover:shadow-md transition-shadow duration-300">
                <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                    <font-awesome-icon icon="comment-dots" class="text-green-600" />
                    Submit Feedback
                </h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Feedback Type Selection -->
                    <div class="space-y-3">
                        <label for="type" class="block text-lg font-semibold text-slate-700">Feedback Type</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label 
                                v-for="type in ['general', 'testimonial', 'bug_report', 'feature_request']" 
                                :key="type"
                                class="relative cursor-pointer"
                            >
                                <input
                                    type="radio"
                                    :value="type"
                                    v-model="form.type"
                                    class="sr-only peer"
                                />
                                <div class="p-4 border-2 border-slate-200 rounded-2xl text-center transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-md hover:border-slate-300">
                                    <div class="text-sm font-medium text-slate-700 peer-checked:text-green-700">
                                        {{ type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div v-if="form.errors.type" class="text-sm text-red-600 mt-2 animate-fade-in">
                            {{ form.errors.type }}
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="space-y-3">
                        <label for="message" class="block text-lg font-semibold text-slate-700">
                            Your Message
                        </label>
                        <textarea
                            v-model="form.message"
                            id="message"
                            rows="6"
                            class="w-full px-6 py-4 border-2 border-slate-200 rounded-2xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 resize-none"
                            placeholder="Share your thoughts, suggestions, or issues..."
                        ></textarea>
                        <div v-if="form.errors.message" class="text-sm text-red-600 mt-2 animate-fade-in">
                            {{ form.errors.message }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing || !isVerified"
                            class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white py-4 px-8 rounded-2xl font-semibold text-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-green-200"
                        >
                            <span v-if="!form.processing" class="flex items-center justify-center gap-3">
                                <font-awesome-icon icon="comment-dots" />
                                Submit Feedback
                            </span>
                            <span v-else class="flex items-center justify-center gap-3">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                        
                        <div v-if="!isVerified" class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 text-center">
                            <font-awesome-icon icon="exclamation-triangle" class="mr-2" />
                            Please verify your email to submit feedback.
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div v-if="$page.props.flash?.success" class="mt-6 p-4 bg-green-50 border border-green-200 rounded-2xl text-green-700 animate-fade-in">
                        <font-awesome-icon icon="check-circle" class="mr-2" />
                        {{ $page.props.flash.success }}
                    </div>
                </form>
            </div>
        </div>

        <!-- My Feedback History Section -->
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 hover:shadow-md transition-shadow duration-300">
                <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                    <font-awesome-icon icon="list-check" class="text-green-600" />
                    My Feedback History
                </h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b-2 border-slate-200">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 uppercase tracking-wider">Message</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 uppercase tracking-wider">Admin Response</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 uppercase tracking-wider">Responded At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="fb in feedbacks" :key="fb.id" class="hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-blue-100 text-blue-800': fb.type === 'general',
                                            'bg-green-100 text-green-800': fb.type === 'testimonial',
                                            'bg-red-100 text-red-800': fb.type === 'bug_report',
                                            'bg-purple-100 text-purple-800': fb.type === 'feature_request'
                                        }">
                                        {{ (fb.type || 'general').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-700 max-w-xs">
                                    <div class="truncate" :title="fb.message">{{ fb.message }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                        :class="fb.status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800'">
                                        {{ fb.status.charAt(0).toUpperCase() + fb.status.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-700 max-w-xs">
                                    <div v-if="fb.response" class="truncate" :title="fb.response">{{ fb.response }}</div>
                                    <span v-else class="text-slate-400 italic">No response yet</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">
                                    {{ fb.responded_at ? new Intl.DateTimeFormat('en-PH', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(fb.responded_at)) : '-' }}
                                </td>
                            </tr>

                            <tr v-if="!feedbacks.length">
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-500">
                                        <font-awesome-icon icon="comment-dots" class="text-4xl mb-4 text-slate-300" />
                                        <span class="text-lg font-medium">No feedback found</span>
                                        <span class="text-sm text-slate-400 mt-1">Your submitted feedback will appear here</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </UserLayout>
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
textarea:focus,
button:focus,
a:focus {
    outline: 2px solid #34d399;
    outline-offset: 2px;
}
</style>
