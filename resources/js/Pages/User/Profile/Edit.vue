<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faArrowLeft,
    faPenToSquare,
    faUser,
    faPhone,
    faLocationDot,
    faEnvelope,
    faGraduationCap,
    faImage,
    faSave,
    faXmark,
} from "@fortawesome/free-solid-svg-icons";
library.add(
    faArrowLeft,
    faPenToSquare,
    faUser,
    faPhone,
    faLocationDot,
    faEnvelope,
    faGraduationCap,
    faImage,
    faSave,
    faXmark
);


const { props } = usePage();
const user = props.user;

const form = useForm({
    user_name: user.user_name || "",
    first_name: user.first_name || "",
    middle_name: user.middle_name || "",
    last_name: user.last_name || "",
    contact_number: user.contact_number || "",
    address: user.address || "",
    email: user.email || "",
    avatar: null,
    student: user.student || "no",
});

const avatarPreview = ref(user.avatar || null);

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: "PUT",
    }));

    form.post(route("user.profile.update"), {
        forceFormData: true,
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Profile Updated",
                text: "Your profile has been successfully updated.",
                timer: 2000,
                showConfirmButton: false,
            });
        },
    });
};

function onAvatarChange(e) {
    const file = e.target.files[0];
    form.avatar = file;
    avatarPreview.value = file
        ? URL.createObjectURL(file)
        : user.avatar || null;
}
</script>

<template>
    <Head title="Edit Profile" />
    <UserLayout>
        <!-- Hero Header -->
        <div class="mb-12 bg-gradient-to-br from-blue-50 via-white to-indigo-50 rounded-3xl p-8 border border-blue-100 shadow-sm">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4 flex items-center justify-center gap-3">
                    <font-awesome-icon icon="pen-to-square" class="text-blue-600" />
                    Edit Your Profile
                </h1>
                <p class="text-xl text-slate-600 leading-relaxed">
                    Update your personal information and preferences
                </p>
            </div>
        </div>

        <!-- Edit Form Container -->
        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 hover:shadow-md transition-shadow duration-300">
                <!-- Back Navigation -->
                <div class="mb-8">
                    <Link
                        :href="route('user.profile.index')"
                        class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium hover:underline transition-colors duration-200"
                    >
                        <font-awesome-icon icon="arrow-left" />
                        Back to Profile
                    </Link>
                </div>

                <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-8">
                    <!-- Personal Information Section -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                            <font-awesome-icon icon="user" class="text-blue-600" />
                            Personal Information
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- User Name -->
                            <div class="relative">
                                <input
                                    v-model="form.user_name"
                                    type="text"
                                    id="user_name"
                                    class="peer w-full border-2 border-slate-200 rounded-2xl px-4 py-4 bg-transparent focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-transparent"
                                    placeholder=" "
                                />
                                <label
                                    for="user_name"
                                    class="absolute left-4 top-4 text-slate-600 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-2 peer-focus:text-sm peer-focus:text-blue-600 peer-focus:font-semibold bg-white px-2"
                                >
                                    <font-awesome-icon icon="user" class="mr-2" />
                                    User Name
                                </label>
                                <div v-if="form.errors.user_name" class="text-red-500 text-sm mt-2">
                                    {{ form.errors.user_name }}
                                </div>
                            </div>

                            <!-- First Name -->
                            <div class="relative">
                                <input
                                    v-model="form.first_name"
                                    type="text"
                                    id="first_name"
                                    class="peer w-full border-2 border-slate-200 rounded-2xl px-4 py-4 bg-transparent focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-transparent"
                                    placeholder=" "
                                />
                                <label
                                    for="first_name"
                                    class="absolute left-4 top-4 text-slate-600 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-2 peer-focus:text-sm peer-focus:text-blue-600 peer-focus:font-semibold bg-white px-2"
                                >
                                    <font-awesome-icon icon="user" class="mr-2" />
                                    First Name
                                </label>
                                <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-2">
                                    {{ form.errors.first_name }}
                                </div>
                            </div>

                            <!-- Middle Name -->
                            <div class="relative">
                                <input
                                    v-model="form.middle_name"
                                    type="text"
                                    id="middle_name"
                                    class="peer w-full border-2 border-slate-200 rounded-2xl px-4 py-4 bg-transparent focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-transparent"
                                    placeholder=" "
                                />
                                <label
                                    for="middle_name"
                                    class="absolute left-4 top-4 text-slate-600 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-2 peer-focus:text-sm peer-focus:text-blue-600 peer-focus:font-semibold bg-white px-2"
                                >
                                    <font-awesome-icon icon="user" class="mr-2" />
                                    Middle Name
                                </label>
                                <div v-if="form.errors.middle_name" class="text-red-500 text-sm mt-2">
                                    {{ form.errors.middle_name }}
                                </div>
                            </div>

                            <!-- Last Name -->
                            <div class="relative">
                                <input
                                    v-model="form.last_name"
                                    type="text"
                                    id="last_name"
                                    class="peer w-full border-2 border-slate-200 rounded-2xl px-4 py-4 bg-transparent focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-transparent"
                                    placeholder=" "
                                />
                                <label
                                    for="last_name"
                                    class="absolute left-4 top-4 text-slate-600 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-2 peer-focus:text-sm peer-focus:text-blue-600 peer-focus:font-semibold bg-white px-2"
                                >
                                    <font-awesome-icon icon="user" class="mr-2" />
                                    Last Name
                                </label>
                                <div v-if="form.errors.last_name" class="text-red-500 text-sm mt-2">
                                    {{ form.errors.last_name }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="space-y-6 pt-8 border-t border-slate-200">
                        <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                            <font-awesome-icon icon="envelope" class="text-blue-600" />
                            Contact Information
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Contact Number -->
                            <div class="relative">
                                <input
                                    v-model="form.contact_number"
                                    type="text"
                                    id="contact_number"
                                    class="peer w-full border-2 border-slate-200 rounded-2xl px-4 py-4 bg-transparent focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-transparent"
                                    placeholder=" "
                                />
                                <label
                                    for="contact_number"
                                    class="absolute left-4 top-4 text-slate-600 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-2 peer-focus:text-sm peer-focus:text-blue-600 peer-focus:font-semibold bg-white px-2"
                                >
                                    <font-awesome-icon icon="phone" class="mr-2" />
                                    Contact Number
                                </label>
                                <div v-if="form.errors.contact_number" class="text-red-500 text-sm mt-2">
                                    {{ form.errors.contact_number }}
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="relative">
                                <input
                                    v-model="form.address"
                                    type="text"
                                    id="address"
                                    class="peer w-full border-2 border-slate-200 rounded-2xl px-4 py-4 bg-transparent focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-transparent"
                                    placeholder=" "
                                />
                                <label
                                    for="address"
                                    class="absolute left-4 top-4 text-slate-600 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-2 peer-focus:text-sm peer-focus:text-blue-600 peer-focus:font-semibold bg-white px-2"
                                >
                                    <font-awesome-icon icon="location-dot" class="mr-2" />
                                    Address
                                </label>
                                <div v-if="form.errors.address" class="text-red-500 text-sm mt-2">
                                    {{ form.errors.address }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="relative">
                                <input
                                    v-model="form.email"
                                    type="email"
                                    id="email"
                                    class="peer w-full border-2 border-slate-200 rounded-2xl px-4 py-4 bg-transparent focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 placeholder-transparent"
                                    placeholder=" "
                                />
                                <label
                                    for="email"
                                    class="absolute left-4 top-4 text-slate-600 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-2 peer-focus:text-sm peer-focus:text-blue-600 peer-focus:font-semibold bg-white px-2"
                                >
                                    <font-awesome-icon icon="envelope" class="mr-2" />
                                    Email
                                </label>
                                <div v-if="form.errors.email" class="text-red-500 text-sm mt-2">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Student Status -->
                            <div class="relative">
                                <label class="text-slate-700 font-medium block mb-3">
                                    <font-awesome-icon icon="graduation-cap" class="mr-2 text-blue-600" />
                                    Student Status
                                </label>
                                <select
                                    v-model="form.student"
                                    class="w-full border-2 border-slate-200 rounded-2xl px-4 py-4 bg-transparent focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                >
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                                <div v-if="form.errors.student" class="text-red-500 text-sm mt-2">
                                    {{ form.errors.student }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Avatar Section -->
                    <div class="space-y-6 pt-8 border-t border-slate-200">
                        <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                            <font-awesome-icon icon="image" class="text-blue-600" />
                            Profile Picture
                        </h2>
                        
                        <div class="flex flex-col md:flex-row items-start gap-6">
                            <!-- Avatar Upload -->
                            <div class="flex-1">
                                <label class="text-slate-700 font-medium block mb-3">
                                    <font-awesome-icon icon="image" class="mr-2" />
                                    Upload New Avatar
                                </label>
                                <input
                                    type="file"
                                    @change="onAvatarChange"
                                    accept="image/*"
                                    class="w-full border-2 border-slate-200 rounded-2xl px-4 py-4 bg-transparent focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                <div v-if="form.errors.avatar" class="text-red-500 text-sm mt-2">
                                    {{ form.errors.avatar }}
                                </div>
                            </div>

                            <!-- Avatar Preview -->
                            <div v-if="avatarPreview" class="text-center">
                                <label class="text-slate-700 font-medium block mb-3">Preview</label>
                                <img
                                    :src="avatarPreview"
                                    alt="Avatar Preview"
                                    class="w-32 h-32 rounded-full border-4 border-blue-300 object-cover shadow-xl ring-4 ring-blue-100"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8 border-t border-slate-200">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-2xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-blue-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="!form.processing" class="flex items-center justify-center gap-3">
                                <font-awesome-icon icon="save" />
                                Update Profile
                            </span>
                            <span v-else class="flex items-center justify-center gap-3">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                        
                        <Link
                            :href="route('user.profile.index')"
                            class="w-full sm:w-auto text-slate-600 hover:text-slate-800 font-medium hover:underline transition-colors duration-200 flex items-center justify-center gap-2 px-8 py-4 border-2 border-slate-200 rounded-2xl hover:border-slate-300"
                        >
                            <font-awesome-icon icon="xmark" />
                            Cancel
                        </Link>
                    </div>
                </form>
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
select:focus,
button:focus,
a:focus {
    outline: 2px solid #34d399;
    outline-offset: 2px;
}
</style>
