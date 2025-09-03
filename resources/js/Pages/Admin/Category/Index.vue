<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTags, faPlus, faHashtag, faTag, faAlignLeft, faPencilAlt, faEye, faTrash, faBook, faFaceFrown } from '@fortawesome/free-solid-svg-icons'

library.add(faTags, faPlus, faHashtag, faTag, faAlignLeft, faPencilAlt, faEye, faTrash, faBook, faFaceFrown)

defineOptions({ layout: AdminLayout });

const props = defineProps({
    categories: Array
});

const categories = props.categories || [];

function deleteCategory(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.categories.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                }
            });
        }
    });
}
</script>

<template>
    <Head title="Categories" />
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <font-awesome-icon icon="tags" class="w-8 h-8 text-white" />
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-slate-800">Categories Management</h1>
                            <p class="text-slate-600 mt-1">Organize books by categories and genres</p>
                        </div>
                    </div>
                    <Link
                        :href="route('admin.categories.create')"
                        class="bg-gradient-to-r from-orange-600 to-orange-700 text-white font-semibold px-6 py-3 rounded-2xl shadow-sm hover:from-orange-700 hover:to-orange-800 flex items-center gap-2 text-base transition-all duration-200"
                    >
                        <font-awesome-icon icon="plus" /> Add Category
                    </Link>
                </div>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">ID</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Name</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Description</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr
                        v-for="category in categories"
                        :key="category.id"
                        class="hover:bg-slate-50 transition-colors"
                    >
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ category.id }}</td>
                        <td class="px-6 py-4">
                          <span class="inline-flex items-center px-3 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold text-sm">{{ category.name }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ category.description || 'No description'}}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2 justify-end">
                                <Link
                                    :href="route('admin.categories.edit', category.id)"
                                    class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white rounded-xl text-xs font-medium hover:bg-yellow-600 transition-colors"
                                >
                                    <font-awesome-icon icon="pencil-alt" class="w-3 h-3 mr-1" /> Edit
                                </Link>
                                <Link
                                    :href="route('admin.categories.show', category.id)"
                                    class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white rounded-xl text-xs font-medium hover:bg-green-600 transition-colors"
                                >
                                    <font-awesome-icon icon="eye" class="w-3 h-3 mr-1" /> View
                                </Link>
                                <button
                                    @click="deleteCategory(category.id)"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white rounded-xl text-xs font-medium hover:bg-red-600 transition-colors"
                                >
                                    <font-awesome-icon icon="trash" class="w-3 h-3 mr-1" /> Delete
                                </button>
                                <Link
                                    :href="route('admin.categories.books', category.id)"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white rounded-xl text-xs font-medium hover:bg-blue-600 transition-colors"
                                >
                                    <font-awesome-icon icon="book" class="w-3 h-3 mr-1" /> View Books
                                </Link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="categories.length === 0">
                        <td colspan="4" class="text-center text-slate-500 py-12">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
                                    <font-awesome-icon icon="tags" class="w-6 h-6 text-slate-400" />
                                </div>
                                <p class="text-slate-600 font-medium">No categories found</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
