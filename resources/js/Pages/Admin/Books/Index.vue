<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage, router, Link, Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faBook, faPlus, faFunnelDollar, faSearch, faXmarkCircle, faHashtag, faUser, faTags, faCheckCircle, faGear, faEye, faPencilAlt, faPauseCircle, faPlayCircle, faTrash
} from '@fortawesome/free-solid-svg-icons'

library.add(faBook, faPlus, faFunnelDollar, faSearch, faXmarkCircle, faHashtag, faUser, faTags, faCheckCircle, faGear, faEye, faPencilAlt, faPauseCircle, faPlayCircle, faTrash)

defineOptions({ layout: AdminLayout });

const { props } = usePage();
const books = props.books || { data: [] };
const filters = props.filters || {};

const statusFilter = ref(filters.status || '');

const handleDelete = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will permanently delete the book.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.books.destroy', id));
    }
  });
};

const filterBooks = () => {
  router.get(route('admin.books.index'), { status: statusFilter.value }, {
    preserveState: false,
    preserveScroll: false,
  });
};

const clearFilters = () => {
  statusFilter.value = '';
  router.get(route('admin.books.index'), {}, {
    preserveState: false,
    preserveScroll: false,
  });
};

const toggleStatus = (id) => {
  router.patch(route('admin.books.toggle-status', id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Status Updated',
        text: 'Book status has been updated successfully.',
        timer: 2000,
        showConfirmButton: false,
      });
    },
  });
};
</script>

<template>
  <Head title="Books" />
  <div class="max-w-7xl mx-auto py-8 px-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="book" /> Books
      </h1>
      <Link :href="route('admin.books.create')" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition flex items-center gap-2">
        <font-awesome-icon icon="plus" /> Add Book
      </Link>
    </div>

    <!-- Filter Section -->
    <div class="bg-white border border-green-500 rounded-xl shadow mb-6 p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-green-700 font-medium mb-1"><font-awesome-icon icon="funnel-dollar" /> Status Filter</label>
          <select v-model="statusFilter" class="w-full border border-gray-300 rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="filterBooks" class="bg-green-600 text-white w-full py-2 rounded hover:bg-green-700 transition flex items-center gap-2">
            <font-awesome-icon icon="search" /> Filter
          </button>
        </div>
        <div class="flex items-end">
          <button @click="clearFilters" class="border border-gray-400 text-gray-700 w-full py-2 rounded hover:bg-gray-100 transition flex items-center gap-2">
            <font-awesome-icon icon="xmark-circle" /> Clear Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Book Table -->
    <div class="bg-white border border-green-500 rounded-xl shadow overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-green-100 text-green-800">
          <tr>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="hashtag" /> ID</th>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="book" /> Title</th>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="user" /> Author</th>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="tags" /> Category</th>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="check-circle" /> Status</th>
            <th class="px-4 py-3 text-right"><font-awesome-icon icon="gear" /> Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="book in books.data" :key="book.id" class="border-t">
            <td class="px-4 py-2">{{ book.id }}</td>
            <td class="px-4 py-2">{{ book.title }}</td>
            <td class="px-4 py-2">{{ book.author }}</td>
            <td class="px-4 py-2">{{ book.category ? book.category.name : '—' }}</td>
            <td class="px-4 py-2">
              <span :class="book.status === 'active' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
                <font-awesome-icon :icon="book.status === 'active' ? 'check-circle' : 'xmark-circle'" class="mr-1" />
                {{ book.status.charAt(0).toUpperCase() + book.status.slice(1) }}
              </span>
            </td>
            <td class="px-4 py-2 text-right whitespace-nowrap">
              <Link :href="route('admin.books.show', book.id)" class="text-white bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-sm mr-1 flex items-center gap-1">
                <font-awesome-icon icon="eye" /> View
              </Link>
              <Link :href="route('admin.books.edit', book.id)" class="text-white bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded text-sm mr-1 flex items-center gap-1">
                <font-awesome-icon icon="pencil-alt" /> Edit
              </Link>
              <button @click="toggleStatus(book.id)"
                :class="book.status === 'active' ? 'bg-gray-500 hover:bg-gray-600' : 'bg-green-600 hover:bg-green-700'"
                class="text-white px-3 py-1 rounded text-sm mr-1 flex items-center gap-1">
                <font-awesome-icon :icon="book.status === 'active' ? 'pause-circle' : 'play-circle'" />
                {{ book.status === 'active' ? 'Deactivate' : 'Activate' }}
              </button>
              <button @click="handleDelete(book.id)" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm flex items-center gap-1">
                <font-awesome-icon icon="trash" /> Delete
              </button>
            </td>
          </tr>
          <tr v-if="books.data.length === 0">
            <td colspan="6" class="text-center text-gray-500 py-6">No books found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="books.links && books.links.length > 3" class="mt-6 flex justify-center">
      <nav class="flex items-center space-x-2">
        <!-- Previous Page -->
        <Link 
          v-if="books.prev_page_url"
          :href="books.prev_page_url"
          class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
        >
          Previous
        </Link>
        <span 
          v-else
          class="px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-200 rounded-md cursor-not-allowed"
        >
          Previous
        </span>

        <!-- Page Numbers -->
        <template v-for="(link, index) in books.links" :key="index">
          <Link 
            v-if="link.url && !link.active && link.label !== '&laquo; Previous' && link.label !== 'Next &raquo;'"
            :href="link.url"
            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            {{ link.label }}
          </Link>
          <span 
            v-else-if="link.active"
            class="px-3 py-2 text-sm font-medium text-white bg-green-600 border border-green-600 rounded-md"
          >
            {{ link.label }}
          </span>
        </template>

        <!-- Next Page -->
        <Link 
          v-if="books.next_page_url"
          :href="books.next_page_url"
          class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
        >
          Next
        </Link>
        <span 
          v-else
          class="px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-200 rounded-md cursor-not-allowed"
        >
          Next
        </span>
      </nav>
    </div>

    <!-- Pagination Info -->
    <div v-if="books.total" class="mt-4 text-center text-sm text-gray-600">
      Showing {{ books.from }} to {{ books.to }} of {{ books.total }} results
    </div>
  </div>
</template>

<style scoped>
</style>