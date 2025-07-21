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

  <div class="max-w-7xl mx-auto py-10 px-4">
    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="book" />
        Books in {{ category.name }}
      </h1>
      <Link
        :href="route('admin.categories.index')"
        class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-green-600 text-white hover:bg-green-700 shadow-lg font-semibold transition-transform duration-200 hover:scale-105 text-base"
      >
        <font-awesome-icon icon="arrow-left" />
        Back to Categories
      </Link>
    </div>

    <div class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 rounded-2xl shadow-2xl overflow-hidden">
      <div class="p-0">
        <table v-if="books.length" class="min-w-full text-sm text-left">
          <thead class="bg-green-100 text-green-700 text-sm uppercase sticky top-0 z-10">
            <tr>
              <th class="px-4 py-3"><font-awesome-icon icon="hashtag" /> ID</th>
              <th class="px-4 py-3"><font-awesome-icon icon="book" /> Title</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(book, index) in books"
              :key="book.id"
              class="even:bg-green-50 hover:bg-green-100 transition-colors duration-150 hover:scale-[1.01] border-t"
            >
              <td class="px-4 py-2 font-semibold">{{ index + 1 }}</td>
              <td class="px-4 py-2">{{ book.title }}</td>
            </tr>
          </tbody>
        </table>
        <p v-else class="text-center text-gray-500 py-6">
          <font-awesome-icon icon="face-frown" /> No books found for this category.
        </p>
      </div>
    </div>
  </div>
</template>
