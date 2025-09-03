<script setup>
import { usePage, router } from "@inertiajs/vue3";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import DeleteAccountForm from "./Partials/DeleteAccountForm.vue";
import VerifyEmail from "./Partials/VerifyEmail.vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faUserCircle,
    faUser,
    faPhone,
    faLocationDot,
    faEnvelope,
    faCircleCheck,
    faGraduationCap,
    faPenToSquare,
    faArrowLeft,
    faTag,
} from "@fortawesome/free-solid-svg-icons";
library.add(
    faUserCircle,
    faUser,
    faPhone,
    faLocationDot,
    faEnvelope,
    faCircleCheck,
    faGraduationCap,
    faPenToSquare,
    faArrowLeft,
    faTag
);
import { onMounted } from "vue";



const { props } = usePage();
const user = props.user;
const genres = props.genres || [];

onMounted(() => {
    if (window.location.search.includes("verified=1")) {
        window.location.replace(window.location.pathname);
    }
});

function goBack() {
    router.visit(route("home"));
}
</script>

<template>
    <Head title="My Profile" />
    <UserLayout>
        <!-- Back Button for Mobile -->
        <div class="mb-6">
            <button
                @click="goBack"
                class="block sm:hidden text-green-700 font-bold flex items-center gap-2 hover:text-green-800 transition-colors"
            >
                <font-awesome-icon icon="arrow-left" class="text-lg" />
                Back to Home
            </button>
        </div>

        <!-- Hero Header -->
        <div class="mb-12 bg-gradient-to-br from-green-50 via-white to-emerald-50 rounded-3xl p-8 border border-green-100 shadow-sm">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4 flex items-center justify-center gap-3">
                    <font-awesome-icon icon="user-circle" class="text-green-600" />
                    My Profile
                </h1>
                <p class="text-xl text-slate-600 leading-relaxed">
                    Manage your personal information and account settings
                </p>
            </div>
        </div>

        <!-- Profile Information Card -->
        <div class="max-w-4xl mx-auto mb-12">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 hover:shadow-md transition-shadow duration-300">
                <!-- Profile Avatar Section -->
                <div class="text-center mb-8">
                    <div class="relative inline-block">
                        <img
                            v-if="user.avatar"
                            :src="user.avatar"
                            alt="Avatar"
                            class="w-32 h-32 rounded-full border-4 border-green-300 object-cover shadow-xl ring-4 ring-green-100"
                        />
                        <img
                            v-else
                            src="https://ui-avatars.com/api/?name=N%2FA&background=ddd&color=555"
                            alt="No Avatar"
                            class="w-32 h-32 rounded-full border-4 border-green-300 object-cover shadow-xl ring-4 ring-green-100"
                        />
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center shadow-lg">
                            <font-awesome-icon icon="user" class="text-white text-sm" />
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 mt-4">{{ user.user_name || "User" }}</h2>
                    <p class="text-slate-600">{{ user.email }}</p>
                </div>

                <!-- Profile Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                            <font-awesome-icon icon="user" class="text-green-600" />
                            Personal Information
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <span class="text-green-600 font-semibold min-w-[100px]">User Name:</span>
                                <span class="text-slate-700">{{ user.user_name || "N/A" }}</span>
                            </div>
                            
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <span class="text-green-600 font-semibold min-w-[100px]">First Name:</span>
                                <span class="text-slate-700">{{ user.first_name || "N/A" }}</span>
                            </div>
                            
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <span class="text-green-600 font-semibold min-w-[100px]">Middle Name:</span>
                                <span class="text-slate-700">{{ user.middle_name || "N/A" }}</span>
                            </div>
                            
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <span class="text-green-600 font-semibold min-w-[100px]">Last Name:</span>
                                <span class="text-slate-700">{{ user.last_name || "N/A" }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Additional Info -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                            <font-awesome-icon icon="envelope" class="text-green-600" />
                            Contact & Details
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <span class="text-green-600 font-semibold min-w-[100px]">Contact:</span>
                                <span class="text-slate-700">{{ user.contact_number || "N/A" }}</span>
                            </div>
                            
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <span class="text-green-600 font-semibold min-w-[100px]">Address:</span>
                                <span class="text-slate-700">{{ user.address || "N/A" }}</span>
                            </div>
                            
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <span class="text-green-600 font-semibold min-w-[100px]">Email:</span>
                                <span class="text-slate-700">{{ user.email || "N/A" }}</span>
                            </div>
                            
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <span class="text-green-600 font-semibold min-w-[100px]">Student:</span>
                                <span class="text-slate-700">{{ user.student === "yes" ? "Yes" : "No" }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Verification Status -->
                <div class="mt-8 p-4 rounded-2xl" :class="user.email_verified_at ? 'bg-green-50 border border-green-200' : 'bg-amber-50 border border-amber-200'">
                    <div class="flex items-center gap-3">
                        <font-awesome-icon icon="circle-check" :class="user.email_verified_at ? 'text-green-600' : 'text-amber-600'" />
                        <span class="font-semibold" :class="user.email_verified_at ? 'text-green-800' : 'text-amber-800'">
                            Email Status: {{ user.email_verified_at ? "Verified" : "Not Verified" }}
                        </span>
                    </div>
                </div>

                <!-- Interests Section -->
                <div class="mt-8 p-6 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl border border-purple-200">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                        <font-awesome-icon icon="tag" class="text-purple-600" />
                        Reading Interests
                    </h3>
                    <div class="flex items-center justify-between">
                        <div class="flex flex-wrap gap-2">
                            <span v-if="genres.length" class="text-slate-700">{{ genres.join(", ") }}</span>
                            <span v-else class="text-slate-500 italic">No interests selected yet.</span>
                        </div>
                        <Link
                            :href="route('user.profile.genres.edit')"
                            class="bg-purple-600 text-white px-4 py-2 rounded-xl hover:bg-purple-700 transition-all duration-200 hover:shadow-md"
                        >
                            Edit Interests
                        </Link>
                    </div>
                </div>

                <!-- Edit Profile Button -->
                <div class="mt-8 text-center">
                    <Link
                        :href="route('user.profile.edit')"
                        class="inline-flex items-center justify-center gap-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-8 py-4 rounded-2xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-green-200"
                    >
                        <font-awesome-icon icon="pen-to-square" />
                        Edit Profile
                    </Link>
                </div>
            </div>
        </div>

        <!-- Additional Components -->
        <div class="max-w-4xl mx-auto space-y-6">
            <VerifyEmail />
            <UpdatePasswordForm />
            <DeleteAccountForm />
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
button:focus,
a:focus {
    outline: 2px solid #34d399;
    outline-offset: 2px;
}
</style>
