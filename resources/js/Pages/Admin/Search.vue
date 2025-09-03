<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { 
  faSearch, faUsers, faBook, faTags, faComments, faEye, faPencilAlt, faTrash, faArrowLeft, faFaceFrown 
} from '@fortawesome/free-solid-svg-icons'

library.add(faSearch, faUsers, faBook, faTags, faComments, faEye, faPencilAlt, faTrash, faArrowLeft, faFaceFrown)

defineOptions({ layout: AdminLayout })

const props = defineProps({
  query: String,
  results: Object,
  totalResults: Number
})

const results = props.results || {}
const totalResults = props.totalResults || 0
</script>

<template>
  <Head :title="`Search Results for '${query}'`" />

  <div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Hero Header -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
            <font-awesome-icon icon="search" class="w-8 h-8 text-white" />
          </div>
          <div>
            <h1 class="text-3xl font-bold text-slate-800">Search Results</h1>
            <p class="text-slate-600 mt-1">Search query: "{{ query }}"</p>
          </div>
        </div>
        <Link
          :href="route('dashboard')"
          class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-colors"
        >
          <font-awesome-icon icon="arrow-left" class="w-4 h-4" /> Back to Dashboard
        </Link>
      </div>
    </div>

    <!-- Results Summary -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
            <font-awesome-icon icon="search" class="w-5 h-5 text-green-600" />
          </div>
          <div>
            <h3 class="text-slate-800 font-semibold">Search Summary</h3>
            <p class="text-slate-600 text-sm">Query: "{{ query }}"</p>
          </div>
        </div>
        <div class="text-right">
          <p class="text-slate-600 text-sm">Total Results</p>
          <p class="text-2xl font-bold text-slate-800">{{ totalResults }}</p>
        </div>
      </div>
    </div>

    <!-- No Results -->
    <div v-if="totalResults === 0" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
      <div class="flex flex-col items-center gap-4">
        <div class="w-20 h-20 bg-slate-100 rounded-2xl flex items-center justify-center">
          <font-awesome-icon icon="face-frown" class="w-8 h-8 text-slate-400" />
        </div>
        <div>
          <h2 class="text-2xl font-bold text-slate-800 mb-2">No results found</h2>
          <p class="text-slate-600">Try searching with different keywords or check your spelling.</p>
        </div>
      </div>
    </div>

    <!-- Users Results -->
    <div v-if="results.users && results.users.length > 0" class="mb-8">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
              <font-awesome-icon icon="users" class="w-4 h-4 text-blue-600" />
            </div>
            <h2 class="text-lg font-semibold text-slate-800">Users ({{ results.users.length }})</h2>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Name</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Email</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Role</th>
                <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="user in results.users" :key="user.id" class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4 font-medium text-slate-800">{{ user.user_name }}</td>
                <td class="px-6 py-4 text-slate-600">{{ user.email }}</td>
                <td class="px-6 py-4">
                  <span :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                    user.role === 'admin' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'
                  ]">
                    {{ user.role.charAt(0).toUpperCase() + user.role.slice(1) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <Link
                    :href="route('admin.users.show', user.id)"
                    class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white rounded-xl text-xs font-medium hover:bg-blue-600 transition-colors"
                  >
                    <font-awesome-icon icon="eye" class="w-3 h-3 mr-1" /> View
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Books Results -->
    <div v-if="results.books && results.books.length > 0" class="mb-8">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
              <font-awesome-icon icon="book" class="w-4 h-4 text-green-600" />
            </div>
            <h2 class="text-lg font-semibold text-slate-800">Books ({{ results.books.length }})</h2>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Title</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Author</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Category</th>
                <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="book in results.books" :key="book.id" class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4 font-medium text-slate-800">{{ book.title }}</td>
                <td class="px-6 py-4 text-slate-600">{{ book.author }}</td>
                <td class="px-6 py-4">
                  <span v-if="book.category" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                    {{ book.category.name }}
                  </span>
                  <span v-else class="text-slate-400">—</span>
                </td>
                <td class="px-6 py-4 text-right">
                  <Link
                    :href="route('admin.books.show', book.id)"
                    class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white rounded-xl text-xs font-medium hover:bg-blue-600 transition-colors"
                  >
                    <font-awesome-icon icon="eye" class="w-3 h-3 mr-1" /> View
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Categories Results -->
    <div v-if="results.categories && results.categories.length > 0" class="mb-8">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
              <font-awesome-icon icon="tags" class="w-4 h-4 text-orange-600" />
            </div>
            <h2 class="text-lg font-semibold text-slate-800">Categories ({{ results.categories.length }})</h2>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Name</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Description</th>
                <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="category in results.categories" :key="category.id" class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                  <span class="inline-flex items-center px-3 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold text-sm">
                    {{ category.name }}
                  </span>
                </td>
                <td class="px-6 py-4 text-slate-600">{{ category.description || 'No description' }}</td>
                <td class="px-6 py-4 text-right">
                  <Link
                    :href="route('admin.categories.show', category.id)"
                    class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white rounded-xl text-xs font-medium hover:bg-blue-600 transition-colors"
                  >
                    <font-awesome-icon icon="eye" class="w-3 h-3 mr-1" /> View
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Feedback Results -->
    <div v-if="results.feedbacks && results.feedbacks.length > 0" class="mb-8">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
              <font-awesome-icon icon="comments" class="w-4 h-4 text-red-600" />
            </div>
            <h2 class="text-lg font-semibold text-slate-800">Feedback ({{ results.feedbacks.length }})</h2>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">User</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Message</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Status</th>
                <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="feedback in results.feedbacks" :key="feedback.id" class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4 font-medium text-slate-800">{{ feedback.user?.user_name || 'Unknown' }}</td>
                <td class="px-6 py-4 text-slate-600 max-w-xs truncate">{{ feedback.message }}</td>
                <td class="px-6 py-4">
                  <span :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                    feedback.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'
                  ]">
                    {{ feedback.status.charAt(0).toUpperCase() + feedback.status.slice(1) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <Link
                    :href="route('admin.feedback.index')"
                    class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white rounded-xl text-xs font-medium hover:bg-blue-600 transition-colors"
                  >
                    <font-awesome-icon icon="eye" class="w-3 h-3 mr-1" /> View
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
