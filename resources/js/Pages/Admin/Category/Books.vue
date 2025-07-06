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

  <div class="max-w-7xl mx-auto py-6 px-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="book" />
        Books in {{ category.name }}
      </h1>
      <Link
        :href="route('admin.categories.index')"
        class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-green-600 text-white hover:bg-green-700 shadow-sm font-semibold transition"
      >
        <font-awesome-icon icon="arrow-left" />
        Back to Categories
      </Link>
    </div>

    <div class="bg-white rounded-2xl shadow-md border border-green-500 overflow-hidden">
      <div class="p-0">
        <table v-if="books.length" class="min-w-full text-sm text-left">
          <thead class="bg-green-100 text-green-700 text-sm uppercase">
            <tr>
              <th class="px-4 py-3"><font-awesome-icon icon="hashtag" /> ID</th>
              <th class="px-4 py-3"><font-awesome-icon icon="book" /> Title</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="book in books"
              :key="book.id"
              class="hover:bg-gray-50 transition border-t"
            >
              <td class="px-4 py-2 font-semibold">{{ book.id }}</td>
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
