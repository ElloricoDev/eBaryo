<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage, router, Link, Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faBook, faPlus, faFunnelDollar, faSearch, faXmarkCircle, faHashtag, faUser, faTags, faCheckCircle, faGear, faEye, faPencilAlt, faPauseCircle, faPlayCircle, faTrash, faStar, faTrophy
} from '@fortawesome/free-solid-svg-icons'

library.add(faBook, faPlus, faFunnelDollar, faSearch, faXmarkCircle, faHashtag, faUser, faTags, faCheckCircle, faGear, faEye, faPencilAlt, faPauseCircle, faPlayCircle, faTrash, faStar, faTrophy)

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

// Add goToPage for SPA pagination
const goToPage = (url) => {
  if (!url) return;
  const queryString = url.split('?')[1] || '';
  const params = {};
  queryString.split('&').forEach(pair => {
    const [key, value] = pair.split('=');
    if (key) params[key] = value;
  });
  router.get(route('admin.books.index'), params, {
    preserveState: false,
    preserveScroll: false,
  });
};
</script>

<template>
  <Head title="Books" />
  <div class="max-w-7xl mx-auto px-4">
    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="book" /> Books
      </h1>
      <Link :href="route('admin.books.create')" class="bg-green-600 text-white px-6 py-2 rounded-full shadow-lg hover:bg-green-700 flex items-center gap-2 text-base font-semibold">
        <font-awesome-icon icon="plus" /> Add Book
      </Link>
    </div>

    <!-- Filter Section -->
    <div class="bg-gradient-to-br from-white via-green-50 to-green-100 border border-green-400 rounded-2xl shadow mb-8 p-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="relative">
          <label class="block text-green-700 font-medium mb-1"><font-awesome-icon icon="funnel-dollar" /> Status Filter</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="funnel-dollar" /></span>
          <select v-model="statusFilter" class="w-full border border-gray-300 rounded-full pl-10 pr-4 py-2 focus:ring-2 focus:ring-green-300 focus:outline-none">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="filterBooks" class="bg-green-600 text-white w-full py-2 rounded-full hover:bg-green-700 flex items-center gap-2 font-semibold shadow">
            <font-awesome-icon icon="search" /> Filter
          </button>
        </div>
        <div class="flex items-end">
          <button @click="clearFilters" class="border border-gray-400 text-gray-700 w-full py-2 rounded-full hover:bg-gray-100 flex items-center gap-2 font-semibold shadow">
            <font-awesome-icon icon="xmark-circle" /> Clear Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Book Table -->
    <div class="bg-white border-2 border-green-400 rounded-2xl shadow-lg overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-green-100 text-green-800 sticky top-0 z-10">
          <tr>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="hashtag" /> ID</th>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="book" /> Title</th>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="user" /> Author</th>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="tags" /> Category</th>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="check-circle" /> Status</th>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="eye" /> Reads</th>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="star" /> Reviews</th>
            <th class="px-4 py-3 text-left"><font-awesome-icon icon="trophy" /> Rating</th>
            <th class="px-4 py-3 text-right"><font-awesome-icon icon="gear" /> Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="book in books.data" :key="book.id" class="even:bg-green-50 hover:bg-green-100 border-t">
            <td class="px-4 py-2 font-semibold">{{ book.id }}</td>
            <td class="px-4 py-2">{{ book.title }}</td>
            <td class="px-4 py-2">{{ book.author }}</td>
            <td class="px-4 py-2">{{ book.category ? book.category.name : '—' }}</td>
            <td class="px-4 py-2">
              <span :class="[
                'inline-block px-3 py-1 rounded-full text-xs font-bold',
                book.status === 'active' ? 'bg-green-200 text-green-800' : 'bg-red-100 text-red-600'
              ]">
                <font-awesome-icon :icon="book.status === 'active' ? 'check-circle' : 'xmark-circle'" class="mr-1" />
                {{ book.status.charAt(0).toUpperCase() + book.status.slice(1) }}
              </span>
            </td>
            <td class="px-4 py-2">{{ book.read_count }}</td>
            <td class="px-4 py-2">{{ book.reviews_count }}</td>
            <td class="px-4 py-2">{{ book.average_rating !== null ? book.average_rating : '—' }}</td>
            <td class="px-4 py-2 text-right whitespace-nowrap">
              <Link :href="route('admin.books.show', book.id)" class="text-white bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded-full text-sm mr-1 flex items-center gap-1 shadow">
                <font-awesome-icon icon="eye" /> View
              </Link>
              <Link :href="route('admin.books.edit', book.id)" class="text-white bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded-full text-sm mr-1 flex items-center gap-1 shadow">
                <font-awesome-icon icon="pencil-alt" /> Edit
              </Link>
              <button @click="toggleStatus(book.id)"
                :class="[
                  'text-white px-3 py-1 rounded-full text-sm mr-1 flex items-center gap-1 shadow',
                  book.status === 'active' ? 'bg-gray-500 hover:bg-gray-600' : 'bg-green-600 hover:bg-green-700',
                ]">
                <font-awesome-icon :icon="book.status === 'active' ? 'pause-circle' : 'play-circle'" />
                {{ book.status === 'active' ? 'Deactivate' : 'Activate' }}
              </button>
              <button @click="handleDelete(book.id)" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded-full text-sm flex items-center gap-1 shadow">
                <font-awesome-icon icon="trash" /> Delete
              </button>
            </td>
          </tr>
          <tr v-if="books.data.length === 0">
            <td colspan="9" class="text-center text-gray-500 py-6">No books found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="books.links && books.links.length > 3" class="mt-8 flex justify-center">
      <nav class="flex items-center space-x-2 bg-white rounded-xl shadow px-4 py-2">
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
          <button
            v-if="link.url && !link.active && !link.label.includes('Previous') && !link.label.includes('Next') && !link.label.includes('&laquo;') && !link.label.includes('&raquo;')"
            @click="goToPage(link.url)"
            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-green-100"
          >
            {{ link.label }}
          </button>
          <span
            v-else-if="link.active && !link.label.includes('Previous') && !link.label.includes('Next') && !link.label.includes('&laquo;') && !link.label.includes('&raquo;')"
            class="px-3 py-2 text-sm font-medium text-white bg-green-600 border border-green-600 rounded-md shadow"
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