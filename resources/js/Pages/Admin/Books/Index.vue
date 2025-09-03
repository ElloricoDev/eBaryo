<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage, router, Link, Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faBook, faPlus, faFunnelDollar, faSearch, faXmarkCircle, faHashtag, faUser, faTags, faCheckCircle, faGear, faEye, faPencilAlt, faPauseCircle, faPlayCircle, faTrash, faStar, faTrophy, faCheckSquare, faSquare
} from '@fortawesome/free-solid-svg-icons'

library.add(faBook, faPlus, faFunnelDollar, faSearch, faXmarkCircle, faHashtag, faUser, faTags, faCheckCircle, faGear, faEye, faPencilAlt, faPauseCircle, faPlayCircle, faTrash, faStar, faTrophy, faCheckSquare, faSquare)

defineOptions({ layout: AdminLayout });

const { props } = usePage();
const books = ref(props.books || { data: [] });
const filters = props.filters || {};
const selectedBooks = ref([]);
const selectAll = ref(false);

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

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedBooks.value = books.value.data?.map(book => book.id) || [];
  } else {
    selectedBooks.value = [];
  }
};

const toggleSelectBook = (bookId) => {
  const index = selectedBooks.value.indexOf(bookId);
  if (index > -1) {
    selectedBooks.value.splice(index, 1);
  } else {
    selectedBooks.value.push(bookId);
  }
  selectAll.value = selectedBooks.value.length === (books.value.data?.length || 0);
};

const confirmBulkDelete = () => {
  if (selectedBooks.value.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "No Selection",
      text: "Please select books to delete.",
      timer: 2000,
      showConfirmButton: false,
    });
    return;
  }

  Swal.fire({
    title: "Are you sure?",
    text: `This will permanently delete ${selectedBooks.value.length} book(s). This action cannot be undone!`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete them!",
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route("admin.books.bulk-delete"), {
        book_ids: selectedBooks.value
      }, {
        preserveScroll: true,
        onSuccess: () => {
          selectedBooks.value = [];
          selectAll.value = false;
          Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "Selected books have been deleted successfully.",
            timer: 2000,
            showConfirmButton: false,
          });
        },
      });
    }
  });
};
</script>

<template>
  <Head title="Books" />
  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
              <font-awesome-icon icon="book" class="w-8 h-8 text-white" />
            </div>
            <div>
              <h1 class="text-3xl font-bold text-slate-800">Books Management</h1>
              <p class="text-slate-600 mt-1">Manage your digital library collection</p>
            </div>
          </div>
          <div class="flex gap-3">
            <button v-if="selectedBooks.length > 0" @click="confirmBulkDelete"
              class="bg-red-500 text-white px-6 py-3 rounded-2xl shadow-sm hover:bg-red-600 flex items-center gap-2 text-base font-semibold transition-all duration-200">
              <font-awesome-icon icon="trash" /> Delete Selected ({{ selectedBooks.length }})
            </button>
            <Link :href="route('admin.books.create')" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-2xl shadow-sm hover:from-green-700 hover:to-emerald-700 flex items-center gap-2 text-base font-semibold transition-all duration-200">
              <font-awesome-icon icon="plus" /> Add Book
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-8 p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-gradient-to-br from-slate-500 to-slate-600 rounded-xl flex items-center justify-center">
          <font-awesome-icon icon="funnel-dollar" class="w-5 h-5 text-white" />
        </div>
        <h3 class="text-lg font-semibold text-slate-800">Filters</h3>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="relative">
          <label class="block text-slate-700 font-medium mb-2">Status Filter</label>
          <select v-model="statusFilter" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all duration-200">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="filterBooks" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white w-full py-3 rounded-2xl hover:from-green-700 hover:to-emerald-700 flex items-center justify-center gap-2 font-semibold shadow-sm transition-all duration-200">
            <font-awesome-icon icon="search" /> Filter
          </button>
        </div>
        <div class="flex items-end">
          <button @click="clearFilters" class="border border-slate-300 text-slate-700 w-full py-3 rounded-2xl hover:bg-slate-50 flex items-center justify-center gap-2 font-semibold transition-all duration-200">
            <font-awesome-icon icon="xmark-circle" /> Clear Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Book Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-3 py-4 text-left">
                <button @click="toggleSelectAll" class="flex items-center gap-2 text-slate-600 hover:text-slate-800 font-medium">
                  <font-awesome-icon :icon="selectAll ? 'check-square' : 'square'" class="w-4 h-4" />
                  <span class="hidden sm:inline">Select All</span>
                </button>
              </th>
              <th class="px-3 py-4 text-left text-sm font-semibold text-slate-700 hidden lg:table-cell">ID</th>
              <th class="px-3 py-4 text-left text-sm font-semibold text-slate-700 min-w-[200px]">Title</th>
              <th class="px-3 py-4 text-left text-sm font-semibold text-slate-700 hidden md:table-cell">Author</th>
              <th class="px-3 py-4 text-left text-sm font-semibold text-slate-700 hidden lg:table-cell">Category</th>
              <th class="px-3 py-4 text-left text-sm font-semibold text-slate-700">Status</th>
              <th class="px-3 py-4 text-left text-sm font-semibold text-slate-700 hidden xl:table-cell">Stats</th>
              <th class="px-3 py-4 text-right text-sm font-semibold text-slate-700 min-w-[200px]">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="(book, index) in (books?.data || [])" :key="book.id" class="hover:bg-slate-50 transition-colors">
              <td class="px-3 py-4">
                <button @click="toggleSelectBook(book.id)" class="flex items-center gap-2 text-slate-600 hover:text-slate-800">
                  <font-awesome-icon :icon="selectedBooks.includes(book.id) ? 'check-square' : 'square'" class="w-4 h-4" />
                </button>
              </td>
              <td class="px-3 py-4 font-semibold text-slate-800 hidden lg:table-cell">#{{ book.id }}</td>
              <td class="px-3 py-4">
                <div class="font-medium text-slate-800 truncate max-w-[180px]" :title="book.title">{{ book.title }}</div>
                <div class="text-xs text-slate-500 md:hidden">{{ book.author }}</div>
                <div class="text-xs text-slate-500 lg:hidden" v-if="book.category">{{ book.category.name }}</div>
              </td>
              <td class="px-3 py-4 text-slate-600 hidden md:table-cell">{{ book.author }}</td>
              <td class="px-3 py-4 hidden lg:table-cell">
                <span v-if="book.category" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                  {{ book.category.name }}
                </span>
                <span v-else class="text-slate-400">—</span>
              </td>
              <td class="px-3 py-4">
                <span :class="[
                  'inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold',
                  book.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                ]">
                  <font-awesome-icon :icon="book.status === 'active' ? 'check-circle' : 'xmark-circle'" class="w-3 h-3 mr-1" />
                  <span class="hidden sm:inline">{{ book.status.charAt(0).toUpperCase() + book.status.slice(1) }}</span>
                </span>
              </td>
              <td class="px-3 py-4 text-slate-600 hidden xl:table-cell">
                <div class="text-xs space-y-1">
                  <div class="flex items-center gap-1">
                    <font-awesome-icon icon="eye" class="w-3 h-3 text-slate-400" />
                    <span>{{ book.read_count || 0 }}</span>
                  </div>
                  <div class="flex items-center gap-1">
                    <font-awesome-icon icon="star" class="w-3 h-3 text-slate-400" />
                    <span>{{ book.reviews_count || 0 }}</span>
                  </div>
                  <div v-if="book.average_rating !== null" class="flex items-center gap-1">
                    <font-awesome-icon icon="star" class="w-3 h-3 text-yellow-500" />
                    <span>{{ book.average_rating }}</span>
                  </div>
                </div>
              </td>
              <td class="px-3 py-4 text-right">
                <div class="flex items-center gap-1 justify-end flex-wrap">
                  <Link :href="route('admin.books.show', book.id)" class="inline-flex items-center px-2 py-1 bg-blue-500 text-white rounded-lg text-xs font-medium hover:bg-blue-600 transition-colors" title="View">
                    <font-awesome-icon icon="eye" class="w-3 h-3" />
                    <span class="hidden sm:inline ml-1">View</span>
                  </Link>
                  <Link :href="route('admin.books.edit', book.id)" class="inline-flex items-center px-2 py-1 bg-yellow-500 text-white rounded-lg text-xs font-medium hover:bg-yellow-600 transition-colors" title="Edit">
                    <font-awesome-icon icon="pencil-alt" class="w-3 h-3" />
                    <span class="hidden sm:inline ml-1">Edit</span>
                  </Link>
                  <button @click="toggleStatus(book.id)"
                    :class="[
                      'inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium transition-colors',
                      book.status === 'active' ? 'bg-slate-500 text-white hover:bg-slate-600' : 'bg-green-500 text-white hover:bg-green-600',
                    ]"
                    :title="book.status === 'active' ? 'Deactivate' : 'Activate'">
                    <font-awesome-icon :icon="book.status === 'active' ? 'pause-circle' : 'play-circle'" class="w-3 h-3" />
                    <span class="hidden sm:inline ml-1">{{ book.status === 'active' ? 'Deactivate' : 'Activate' }}</span>
                  </button>
                  <button @click="handleDelete(book.id)" class="inline-flex items-center px-2 py-1 bg-red-500 text-white rounded-lg text-xs font-medium hover:bg-red-600 transition-colors" title="Delete">
                    <font-awesome-icon icon="trash" class="w-3 h-3" />
                    <span class="hidden sm:inline ml-1">Delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!books?.data || books.data.length === 0">
              <td colspan="8" class="text-center text-slate-500 py-12">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
                    <font-awesome-icon icon="book" class="w-6 h-6 text-slate-400" />
                  </div>
                  <p class="text-slate-600 font-medium">No books found</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="books && books.links && books.links.length > 3" class="mt-8 flex items-center justify-between">
      <div class="text-sm text-slate-600">
        Showing {{ books.from || 0 }} to {{ books.to || 0 }} of {{ books.total || 0 }} results
      </div>
      <div class="flex space-x-2">
        <template v-for="link in books.links" :key="link.label">
          <Link
            v-if="link.url"
            :href="link.url"
            v-html="link.label"
            :class="[
              'px-4 py-2 text-sm border rounded-xl transition-all duration-200 font-medium',
              link.active
                ? 'text-white bg-gradient-to-r from-green-600 to-emerald-600 border-green-600 shadow-sm'
                : 'text-slate-700 bg-white border-slate-200 hover:bg-slate-50 hover:border-slate-300'
            ]"
          />
          <span
            v-else
            v-html="link.label"
            class="px-4 py-2 text-sm border rounded-xl text-slate-400 bg-slate-100 border-slate-200 cursor-not-allowed"
          />
        </template>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>