<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTags, faArrowLeft, faAlignLeft, faList, faSave } from '@fortawesome/free-solid-svg-icons'

library.add(faTags, faArrowLeft, faAlignLeft, faList, faSave)

defineOptions({ layout: AdminLayout });

const props = defineProps({
    category: Object
});

const form = useForm({
    name: props.category.name,
    description: props.category.description || ''
});

function submit() {
    form.put(route('admin.categories.update', props.category.id), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Category updated!',
                text: 'The category has been updated successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}
</script>

<template>
    <Head title="Edit category" />
    <div class="max-w-7xl mx-auto py-10 px-4">
        <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
                <font-awesome-icon icon="tags" /> Edit Category
            </h1>
            <Link
                :href="route('admin.categories.index')"
                class="inline-flex items-center bg-green-600 text-white font-semibold px-6 py-2 rounded-full shadow-lg hover:bg-green-700 transition-transform duration-200 hover:scale-105 text-base"
            >
                <font-awesome-icon icon="arrow-left" class="mr-2" /> Back to Categories
            </Link>
        </div>

        <div class="flex justify-center">
            <div class="w-full max-w-xl">
                <div class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 rounded-2xl shadow-2xl p-8">
                    <form @submit.prevent="submit">
                        <!-- Name Field -->
                        <div class="mb-8 relative">
                            <label for="name" class="block font-semibold text-green-700 mb-1">
                                <font-awesome-icon icon="align-left" class="mr-1" /> Name
                            </label>
                            <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="align-left" /></span>
                            <input
                                v-model="form.name"
                                id="name"
                                type="text"
                                class="w-full px-10 py-2 rounded-full border border-green-500 shadow-sm focus:ring-2 focus:ring-green-300 focus:border-green-600 transition-all duration-150"
                                :class="{'border-red-500': form.errors.name, 'border-green-600': !form.errors.name}"
                            />
                            <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Description Field -->
                        <div class="mb-8 relative">
                            <label for="description" class="block font-semibold text-green-700 mb-1">
                                <font-awesome-icon icon="list" class="mr-1" /> Description
                            </label>
                            <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="list" /></span>
                            <textarea
                                v-model="form.description"
                                id="description"
                                rows="3"
                                class="w-full px-10 py-2 rounded-xl border border-green-500 shadow-sm focus:ring-2 focus:ring-green-300 focus:border-green-600 transition-all duration-150"
                                :class="{'border-red-500': form.errors.description, 'border-green-600': !form.errors.description}"
                            ></textarea>
                            <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-bold py-3 rounded-full shadow-xl transition-transform duration-200 inline-flex items-center justify-center gap-2 text-lg hover:scale-105 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <font-awesome-icon icon="save" class="mr-1" /> Update Category
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
