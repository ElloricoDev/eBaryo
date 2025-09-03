<script setup>
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faUserXmark, faKey, faTrash } from "@fortawesome/free-solid-svg-icons";
library.add(faUserXmark, faKey, faTrash);

const form = useForm({
    password: "",
});

const submit = async () => {
    const result = await Swal.fire({
        title: "Are you sure?",
        text: "This action is permanent. Your account will be deleted.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    });

    if (result.isConfirmed) {
        form.delete(route("user.profile.destroy"), {
            preserveScroll: true,
            onError: () => {
                // Optional: handle error
            },
        });
    }
};
</script>

<template>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 hover:shadow-md transition-shadow duration-300">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-500 rounded-2xl flex items-center justify-center">
                <font-awesome-icon icon="user-xmark" class="text-white text-xl" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Delete Account</h3>
                <p class="text-slate-600 text-sm">This action is permanent and cannot be undone</p>
            </div>
        </div>

        <!-- Warning Message -->
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-800">
            <div class="flex items-center gap-3">
                <font-awesome-icon icon="exclamation-triangle" class="text-red-600" />
                <div>
                    <div class="font-semibold">⚠️ Irreversible Action</div>
                    <div class="text-sm">Deleting your account will permanently remove all your data, including reading history, saved books, and feedback.</div>
                </div>
            </div>
        </div>

        <!-- Delete Form -->
        <form @submit.prevent="submit" class="space-y-6">
            <!-- Password Confirmation -->
            <div class="space-y-2">
                <label for="delete_password" class="block text-sm font-semibold text-slate-700">
                    <font-awesome-icon icon="key" class="mr-2 text-red-600" />
                    Confirm with Password
                </label>
                <input
                    v-model="form.password"
                    type="password"
                    id="delete_password"
                    class="w-full border-2 border-slate-200 rounded-2xl px-4 py-3 bg-transparent focus:border-red-500 focus:ring-4 focus:ring-red-100 transition-all duration-200 placeholder-slate-400"
                    placeholder="Enter your password to confirm"
                />
                <div v-if="form.errors.password" class="text-red-500 text-sm">
                    {{ form.errors.password }}
                </div>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :disabled="form.processing"
                class="w-full bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white py-4 px-6 rounded-2xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-red-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span v-if="!form.processing" class="flex items-center justify-center gap-3">
                    <font-awesome-icon icon="trash" />
                    Delete My Account
                </span>
                <span v-else class="flex items-center justify-center gap-3">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    Deleting...
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
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
    }
}
.animate-pulse-cta {
    animation: pulse-cta 2s infinite;
}
input:focus,
button:focus,
a:focus {
    outline: 2px solid #ef4444;
    outline-offset: 2px;
}
</style>
