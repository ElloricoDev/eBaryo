<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTags, faArrowLeft, faAlignLeft, faList, faPlusCircle } from '@fortawesome/free-solid-svg-icons'

library.add(faTags, faArrowLeft, faAlignLeft, faList, faPlusCircle)

defineOptions({ layout: AdminLayout });

const form = useForm({
    name: "",
    description: "",
});

function submit() {
    form.post(route("admin.categories.store"), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Category created!',
                text: 'The new category has been added successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}
</script>

<template>
    <Head title="Create new category" />

    <div class="max-w-7xl mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-green-600 flex items-center gap-2">
                <font-awesome-icon icon="tags" /> Create New Category
            </h1>
            <Link
                :href="route('admin.categories.index')"
                class="inline-flex items-center bg-green-600 text-white font-semibold px-5 py-2 rounded-full shadow hover:bg-green-700 transition"
            >
                <font-awesome-icon icon="arrow-left" class="mr-2" /> Back to Categories
            </Link>
        </div>

        <div class="flex justify-center">
            <div class="w-full max-w-xl">
                <div class="bg-white border border-green-600 rounded-2xl shadow p-6">
                    <form @submit.prevent="submit">
                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block font-semibold text-green-700 mb-1">
                                <font-awesome-icon icon="align-left" class="mr-1" /> Name
                            </label>
                            <input
                                v-model="form.name"
                                id="name"
                                type="text"
                                class="w-full px-4 py-2 rounded-full border shadow-sm focus:ring-2 focus:ring-green-300 focus:border-green-600"
                                :class="{'border-red-500': form.errors.name, 'border-green-600': !form.errors.name}"
                            />
                            <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block font-semibold text-green-700 mb-1">
                                <font-awesome-icon icon="list" class="mr-1" /> Description
                            </label>
                            <textarea
                                v-model="form.description"
                                id="description"
                                rows="3"
                                class="w-full px-4 py-2 rounded-xl border shadow-sm focus:ring-2 focus:ring-green-300 focus:border-green-600"
                                :class="{'border-red-500': form.errors.description, 'border-green-600': !form.errors.description}"
                            ></textarea>
                            <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded-full shadow transition disabled:opacity-50 inline-flex items-center justify-center gap-2"
                            :disabled="form.processing"
                        >
                            <font-awesome-icon icon="plus-circle" class="mr-1" /> Create Category
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
