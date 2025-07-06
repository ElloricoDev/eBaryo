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
    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-green-600 flex items-center gap-2">
                <font-awesome-icon icon="tags" /> Categories
            </h1>
            <Link
                :href="route('admin.categories.create')"
                class="inline-flex items-center bg-green-600 text-white font-semibold px-5 py-2 rounded-full shadow hover:bg-green-700 transition"
            >
                <font-awesome-icon icon="plus" class="mr-2" /> Add Category
            </Link>
        </div>

        <!-- Table Card -->
        <div class="bg-white border border-green-600 rounded-2xl shadow overflow-x-auto">
            <table class="min-w-full table-auto text-left">
                <thead class="bg-green-100 text-green-700">
                    <tr>
                        <th class="px-4 py-3 font-semibold"><font-awesome-icon icon="hashtag" /> ID</th>
                        <th class="px-4 py-3 font-semibold"><font-awesome-icon icon="tag" /> Name</th>
                        <th class="px-4 py-3 font-semibold"><font-awesome-icon icon="align-left" /> Description</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="category in categories"
                        :key="category.id"
                        class="border-t hover:bg-gray-50 transition"
                    >
                        <td class="px-4 py-3 font-medium">{{ category.id }}</td>
                        <td class="px-4 py-3">{{ category.name }}</td>
                        <td class="px-4 py-3">{{ category.description }}</td>
                        <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                            <Link
                                :href="route('admin.categories.edit', category.id)"
                                class="inline-flex items-center px-3 py-1 text-sm bg-yellow-400 text-white rounded-full hover:bg-yellow-500 transition"
                            >
                                <font-awesome-icon icon="pencil-alt" class="mr-1" /> Edit
                            </Link>
                            <Link
                                :href="route('admin.categories.show', category.id)"
                                class="inline-flex items-center px-3 py-1 text-sm bg-green-600 text-white rounded-full hover:bg-green-700 transition"
                            >
                                <font-awesome-icon icon="eye" class="mr-1" /> View
                            </Link>
                            <button
                                @click="deleteCategory(category.id)"
                                class="inline-flex items-center px-3 py-1 text-sm bg-red-600 text-white rounded-full hover:bg-red-700 transition"
                            >
                                <font-awesome-icon icon="trash" class="mr-1" /> Delete
                            </button>
                            <Link
                                :href="route('admin.categories.books', category.id)"
                                class="inline-flex items-center px-3 py-1 text-sm bg-blue-500 text-white rounded-full hover:bg-blue-600 transition"
                            >
                                <font-awesome-icon icon="book" class="mr-1" /> View Books
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="categories.length === 0">
                        <td colspan="4" class="text-center text-gray-500 py-6">
                            <font-awesome-icon icon="face-frown" /> No categories found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
