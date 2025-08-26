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

  <div class="max-w-7xl mx-auto px-4">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex items-center gap-3">
      <Link
        :href="route('dashboard')"
        class="inline-flex items-center gap-2 px-5 py-2 bg-green-600 text-white font-semibold rounded-full shadow hover:bg-green-700"
      >
        <font-awesome-icon icon="arrow-left" /> Back to Dashboard
      </Link>
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="search" /> Search Results
      </h1>
      <span class="text-green-600 font-medium">"{{ query }}"</span>
    </div>

    <!-- Results Summary -->
    <div class="mb-6 text-center">
      <p class="text-gray-600">
        Found <span class="font-bold text-green-600">{{ totalResults }}</span> result{{ totalResults !== 1 ? 's' : '' }} for "{{ query }}"
      </p>
    </div>

    <!-- No Results -->
    <div v-if="totalResults === 0" class="text-center py-12">
      <font-awesome-icon icon="face-frown" class="text-6xl text-gray-400 mb-4" />
      <h2 class="text-2xl font-bold text-gray-600 mb-2">No results found</h2>
      <p class="text-gray-500">Try searching with different keywords or check your spelling.</p>
    </div>

    <!-- Users Results -->
    <div v-if="results.users && results.users.length > 0" class="mb-8">
      <div class="bg-white border-2 border-green-400 rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-green-100 px-6 py-4 border-b border-green-200">
          <h2 class="text-xl font-bold text-green-700 flex items-center gap-2">
            <font-awesome-icon icon="users" /> Users ({{ results.users.length }})
          </h2>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-green-50">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-green-700">Name</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-green-700">Email</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-green-700">Role</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-green-700">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in results.users" :key="user.id" class="border-b border-gray-100 hover:bg-green-50">
                <td class="px-4 py-3">{{ user.user_name }}</td>
                <td class="px-4 py-3">{{ user.email }}</td>
                <td class="px-4 py-3">
                  <span :class="[
                    'inline-block px-2 py-1 rounded-full text-xs font-bold',
                    user.role === 'admin' ? 'bg-green-200 text-green-800' : 'bg-blue-100 text-blue-700'
                  ]">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right space-x-1">
                  <Link
                    :href="route('admin.users.show', user.id)"
                    class="inline-flex items-center px-3 py-1 text-sm bg-blue-500 text-white rounded-full hover:bg-blue-600"
                  >
                    <font-awesome-icon icon="eye" /> View
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
      <div class="bg-white border-2 border-green-400 rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-green-100 px-6 py-4 border-b border-green-200">
          <h2 class="text-xl font-bold text-green-700 flex items-center gap-2">
            <font-awesome-icon icon="book" /> Books ({{ results.books.length }})
          </h2>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-green-50">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-green-700">Title</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-green-700">Author</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-green-700">Category</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-green-700">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="book in results.books" :key="book.id" class="border-b border-gray-100 hover:bg-green-50">
                <td class="px-4 py-3">{{ book.title }}</td>
                <td class="px-4 py-3">{{ book.author }}</td>
                <td class="px-4 py-3">{{ book.category?.name || '—' }}</td>
                <td class="px-4 py-3 text-right space-x-1">
                  <Link
                    :href="route('admin.books.show', book.id)"
                    class="inline-flex items-center px-3 py-1 text-sm bg-blue-500 text-white rounded-full hover:bg-blue-600"
                  >
                    <font-awesome-icon icon="eye" /> View
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
      <div class="bg-white border-2 border-green-400 rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-green-100 px-6 py-4 border-b border-green-200">
          <h2 class="text-xl font-bold text-green-700 flex items-center gap-2">
            <font-awesome-icon icon="tags" /> Categories ({{ results.categories.length }})
          </h2>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-green-50">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-green-700">Name</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-green-700">Description</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-green-700">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="category in results.categories" :key="category.id" class="border-b border-gray-100 hover:bg-green-50">
                <td class="px-4 py-3">
                  <span class="inline-block px-3 py-1 rounded-full bg-green-200 text-green-800 font-semibold text-sm">
                    {{ category.name }}
                  </span>
                </td>
                <td class="px-4 py-3">{{ category.description || 'No description' }}</td>
                <td class="px-4 py-3 text-right space-x-1">
                  <Link
                    :href="route('admin.categories.show', category.id)"
                    class="inline-flex items-center px-3 py-1 text-sm bg-blue-500 text-white rounded-full hover:bg-blue-600"
                  >
                    <font-awesome-icon icon="eye" /> View
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
      <div class="bg-white border-2 border-green-400 rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-green-100 px-6 py-4 border-b border-green-200">
          <h2 class="text-xl font-bold text-green-700 flex items-center gap-2">
            <font-awesome-icon icon="comments" /> Feedback ({{ results.feedbacks.length }})
          </h2>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-green-50">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-green-700">User</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-green-700">Message</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-green-700">Status</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-green-700">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="feedback in results.feedbacks" :key="feedback.id" class="border-b border-gray-100 hover:bg-green-50">
                <td class="px-4 py-3">{{ feedback.user?.user_name || 'Unknown' }}</td>
                <td class="px-4 py-3 max-w-xs truncate">{{ feedback.message }}</td>
                <td class="px-4 py-3">
                  <span :class="[
                    'inline-block px-2 py-1 rounded-full text-xs font-bold',
                    feedback.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-200 text-green-800'
                  ]">
                    {{ feedback.status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right">
                  <Link
                    :href="route('admin.feedback.index')"
                    class="inline-flex items-center px-3 py-1 text-sm bg-blue-500 text-white rounded-full hover:bg-blue-600"
                  >
                    <font-awesome-icon icon="eye" /> View
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
