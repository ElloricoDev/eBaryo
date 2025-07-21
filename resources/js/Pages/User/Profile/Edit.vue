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

defineOptions({ layout: UserLayout });

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

    <div
        class="relative min-h-screen max-w-5xl mx-auto px-2 sm:px-4 py-4 sm:py-6 overflow-x-hidden"
    >
        <div
            class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 via-green-50 to-white animate-gradient-move"
        ></div>
        <div
            class="animate-fade-in bg-white bg-opacity-90 rounded-2xl shadow-xl p-2 sm:p-6"
        >
            <div class="flex items-center mb-6">
                <Link
                    :href="route('user.profile.index')"
                    class="text-green-700 hover:underline focus:outline-none focus:ring-2 focus:ring-green-400 flex items-center gap-2"
                >
                    <font-awesome-icon icon="arrow-left" /> Back
                </Link>
            </div>

            <h1
                class="text-2xl font-bold text-green-700 flex items-center gap-2 mb-6"
            >
                <font-awesome-icon icon="pen-to-square" /> Edit Profile
            </h1>

            <form
                @submit.prevent="submit"
                enctype="multipart/form-data"
                class="space-y-6"
            >
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                    <div class="relative">
                        <input
                            v-model="form.user_name"
                            type="text"
                            id="user_name"
                            class="peer w-full border border-green-300 rounded px-3 py-2 bg-transparent focus:border-green-600 focus:ring-2 focus:ring-green-200 transition placeholder-transparent"
                            placeholder=" "
                        />
                        <label
                            for="user_name"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-green-600 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-5 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.user_name && form.user_name.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="user" /> User Name</label
                        >
                        <div
                            v-if="form.errors.user_name"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.user_name }}
                        </div>
                    </div>

                    <div class="relative">
                        <input
                            v-model="form.first_name"
                            type="text"
                            id="first_name"
                            class="peer w-full border border-green-300 rounded px-3 py-2 bg-transparent focus:border-green-600 focus:ring-2 focus:ring-green-200 transition placeholder-transparent"
                            placeholder=" "
                        />
                        <label
                            for="first_name"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-green-600 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-5 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.first_name &&
                                    form.first_name.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="user" /> First Name</label
                        >
                        <div
                            v-if="form.errors.first_name"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.first_name }}
                        </div>
                    </div>

                    <div class="relative">
                        <input
                            v-model="form.middle_name"
                            type="text"
                            id="middle_name"
                            class="peer w-full border border-green-300 rounded px-3 py-2 bg-transparent focus:border-green-600 focus:ring-2 focus:ring-green-200 transition placeholder-transparent"
                            placeholder=" "
                        />
                        <label
                            for="middle_name"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-green-600 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-5 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.middle_name &&
                                    form.middle_name.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="user" /> Middle Name</label
                        >
                        <div
                            v-if="form.errors.middle_name"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.middle_name }}
                        </div>
                    </div>

                    <div class="relative">
                        <input
                            v-model="form.last_name"
                            type="text"
                            id="last_name"
                            class="peer w-full border border-green-300 rounded px-3 py-2 bg-transparent focus:border-green-600 focus:ring-2 focus:ring-green-200 transition placeholder-transparent"
                            placeholder=" "
                        />
                        <label
                            for="last_name"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-green-600 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-5 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.last_name && form.last_name.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="user" /> Last Name</label
                        >
                        <div
                            v-if="form.errors.last_name"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.last_name }}
                        </div>
                    </div>

                    <div
                        class="col-span-full border-t border-green-100 my-2"
                    ></div>

                    <div class="relative">
                        <input
                            v-model="form.contact_number"
                            type="text"
                            id="contact_number"
                            class="peer w-full border border-green-300 rounded px-3 py-2 bg-transparent focus:border-green-600 focus:ring-2 focus:ring-green-200 transition placeholder-transparent"
                            placeholder=" "
                        />
                        <label
                            for="contact_number"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-green-600 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-5 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.contact_number &&
                                    form.contact_number.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="phone" /> Contact
                            Number</label
                        >
                        <div
                            v-if="form.errors.contact_number"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.contact_number }}
                        </div>
                    </div>

                    <div class="relative">
                        <input
                            v-model="form.address"
                            type="text"
                            id="address"
                            class="peer w-full border border-green-300 rounded px-3 py-2 bg-transparent focus:border-green-600 focus:ring-2 focus:ring-green-200 transition placeholder-transparent"
                            placeholder=" "
                        />
                        <label
                            for="address"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-green-600 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-5 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.address && form.address.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="location-dot" />
                            Address</label
                        >
                        <div
                            v-if="form.errors.address"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.address }}
                        </div>
                    </div>

                    <div class="relative">
                        <input
                            v-model="form.email"
                            type="email"
                            id="email"
                            class="peer w-full border border-green-300 rounded px-3 py-2 bg-transparent focus:border-green-600 focus:ring-2 focus:ring-green-200 transition placeholder-transparent"
                            placeholder=" "
                        />
                        <label
                            for="email"
                            class="absolute left-3 top-2.5 text-green-700 font-medium pointer-events-none transition-all duration-200 peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-base peer-placeholder-shown:text-green-700 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-green-600 peer-focus:font-semibold bg-white px-1"
                            :class="{
                                '-top-5 text-xs text-green-600 font-semibold bg-white px-1':
                                    form.email && form.email.length > 0,
                            }"
                        >
                            <font-awesome-icon icon="envelope" /> Email</label
                        >
                        <div
                            v-if="form.errors.email"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div
                        class="col-span-full border-t border-green-100 my-2"
                    ></div>

                    <div>
                        <label class="text-green-700 font-medium block mb-1"
                            ><font-awesome-icon icon="graduation-cap" />
                            Student</label
                        >
                        <select
                            v-model="form.student"
                            class="w-full border border-green-300 rounded px-3 py-2 bg-transparent focus:border-green-600 focus:ring-2 focus:ring-green-200 transition"
                        >
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        <div
                            v-if="form.errors.student"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.student }}
                        </div>
                    </div>

                    <div
                        class="col-span-full border-t border-green-100 my-2"
                    ></div>

                    <div>
                        <label class="text-green-700 font-medium block mb-1"
                            ><font-awesome-icon icon="image" /> Avatar</label
                        >
                        <input
                            type="file"
                            @change="onAvatarChange"
                            accept="image/*"
                            class="w-full border border-green-300 rounded px-3 py-2 bg-transparent focus:border-green-600 focus:ring-2 focus:ring-green-200 transition"
                        />
                        <div
                            v-if="form.errors.avatar"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.avatar }}
                        </div>

                        <div
                            v-if="avatarPreview"
                            class="mt-3 flex justify-center"
                        >
                            <img
                                :src="avatarPreview"
                                alt="Avatar Preview"
                                class="w-20 h-20 rounded-full border-4 border-green-300 object-cover shadow-lg ring-2 ring-green-200"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 focus:bg-green-800 text-white px-5 py-2 rounded shadow font-semibold flex items-center gap-2 animate-pulse-cta focus:outline-none focus:ring-2 focus:ring-green-400 transition disabled:opacity-60 disabled:cursor-not-allowed"
                        :disabled="form.processing"
                    >
                        <font-awesome-icon icon="save" />
                        <span v-if="form.processing">
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
                            Saving...
                        </span>
                        <span v-else>Update Profile</span>
                    </button>
                    <Link
                        :href="route('user.profile.index')"
                        class="text-green-700 hover:underline focus:outline-none focus:ring-2 focus:ring-green-400 flex items-center gap-2"
                    >
                        <font-awesome-icon icon="xmark" /> Cancel
                    </Link>
                </div>
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
select:focus,
button:focus,
a:focus {
    outline: 2px solid #34d399;
    outline-offset: 2px;
}
</style>
