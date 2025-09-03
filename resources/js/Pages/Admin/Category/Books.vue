<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBook, faArrowLeft, faHashtag, faFaceFrown } from '@fortawesome/free-solid-svg-icons'

library.add(faBook, faArrowLeft, faHashtag, faFaceFrown)

defineOptions({ layout: AdminLayout })

const props = defineProps({
  category: Object,
  books: Array
})

const books = props.books || []
const category = props.category
</script>

<template>
  <Head :title="`Books in ${category.name}`" />

  <div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Hero Header -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center">
            <font-awesome-icon icon="book" class="w-8 h-8 text-white" />
          </div>
          <div>
            <h1 class="text-3xl font-bold text-slate-800">Books in {{ category.name }}</h1>
            <p class="text-slate-600 mt-1">View all books in this category</p>
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

    <!-- Books Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <div v-if="books.length" class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">#</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Book Title</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Author</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Status</th>
              <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(book, index) in books"
              :key="book.id"
              class="hover:bg-slate-50 transition-colors"
            >
              <td class="px-6 py-4 font-semibold text-slate-800">{{ index + 1 }}</td>
              <td class="px-6 py-4">
                <div class="font-medium text-slate-800">{{ book.title }}</div>
              </td>
              <td class="px-6 py-4 text-slate-600">{{ book.author || 'Unknown' }}</td>
              <td class="px-6 py-4">
                <span :class="[
                  'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                  book.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                ]">
                  <font-awesome-icon :icon="book.status === 'active' ? 'check-circle' : 'xmark-circle'" class="w-3 h-3 mr-1" />
                  {{ book.status ? book.status.charAt(0).toUpperCase() + book.status.slice(1) : 'N/A' }}
                </span>
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
      <div v-else class="text-center text-slate-500 py-12">
        <div class="flex flex-col items-center gap-3">
          <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
            <font-awesome-icon icon="book" class="w-6 h-6 text-slate-400" />
          </div>
          <p class="text-slate-600 font-medium">No books found in this category</p>
          <p class="text-slate-500 text-sm">Books will appear here once they are added to this category</p>
        </div>
      </div>
    </div>

    <!-- Summary Card -->
    <div v-if="books.length" class="mt-6 bg-slate-50 rounded-2xl p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
            <font-awesome-icon icon="book" class="w-5 h-5 text-orange-600" />
          </div>
          <div>
            <h3 class="text-slate-800 font-semibold">Category Summary</h3>
            <p class="text-slate-600 text-sm">{{ books.length }} book{{ books.length !== 1 ? 's' : '' }} in {{ category.name }}</p>
          </div>
        </div>
        <div class="text-right">
          <p class="text-slate-600 text-sm">Active Books</p>
          <p class="text-2xl font-bold text-slate-800">{{ books.filter(book => book.status === 'active').length }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
