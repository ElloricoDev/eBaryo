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
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Hero Header -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center">
                        <font-awesome-icon icon="tags" class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800">Edit Category</h1>
                        <p class="text-slate-600 mt-1">Update category information</p>
                    </div>
                </div>
                <Link
                    :href="route('admin.categories.index')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-colors"
                >
                    <font-awesome-icon icon="arrow-left" class="w-4 h-4" /> Back to Categories
                </Link>
            </div>
        </div>

        <!-- Form -->
        <div class="flex justify-center">
            <div class="w-full max-w-2xl">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name Field -->
                        <div>
                            <label for="name" class="block text-slate-700 font-semibold mb-2">
                                Category Name
                            </label>
                            <input
                                v-model="form.name"
                                id="name"
                                type="text"
                                placeholder="Enter category name"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-colors"
                                :class="{'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.name}"
                            />
                            <p v-if="form.errors.name" class="text-red-500 text-sm mt-2">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Description Field -->
                        <div>
                            <label for="description" class="block text-slate-700 font-semibold mb-2">
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                id="description"
                                rows="4"
                                placeholder="Enter category description (optional)"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-colors resize-none"
                                :class="{'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.description}"
                            ></textarea>
                            <p v-if="form.errors.description" class="text-red-500 text-sm mt-2">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-700 hover:to-yellow-800 text-white font-semibold py-3 rounded-2xl transition-all duration-200 inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="form.processing"
                        >
                            <font-awesome-icon icon="save" class="w-4 h-4" />
                            {{ form.processing ? 'Updating...' : 'Update Category' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
