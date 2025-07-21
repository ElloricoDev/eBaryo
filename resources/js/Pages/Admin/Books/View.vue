<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faArrowLeft, faEye, faBook, faUser, faTags, faBuilding, faLanguage, faBarcode, faCalendar, faInfoCircle, faAlignLeft, faImage, faFile, faFileCircleXmark, faDownload, faStar, faTrophy, faCheckCircle
} from '@fortawesome/free-solid-svg-icons'

library.add(faArrowLeft, faEye, faBook, faUser, faTags, faBuilding, faLanguage, faBarcode, faCalendar, faInfoCircle, faAlignLeft, faImage, faFile, faFileCircleXmark, faDownload, faStar, faTrophy, faCheckCircle)

defineOptions({ layout: AdminLayout });

const { props } = usePage();
const book = props.book;

const fileExt = computed(() => {
  if (!book.ebook_file) return '';
  return book.ebook_file.split('.').pop().toLowerCase();
});

const isPdf = computed(() => fileExt.value === 'pdf');
const isEpub = computed(() => fileExt.value === 'epub');
</script>

<template>
  <div class="max-w-6xl mx-auto py-10 px-4">
    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center gap-4">
      <Link :href="route('admin.books.index')"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-700 text-white font-bold rounded-full shadow-lg hover:from-green-600 hover:to-green-800 transition-transform duration-200 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 hover:scale-105"
      >
        <font-awesome-icon icon="arrow-left" class="text-lg" />
        <span class="hidden sm:inline">Back to Books</span>
        <span class="sm:hidden">Back</span>
      </Link>
      <h1 class="text-2xl font-bold text-green-600 flex items-center gap-2">
        <font-awesome-icon icon="eye" /> Book Details
      </h1>
    </div>

    <div class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 shadow-2xl rounded-2xl mb-8 overflow-hidden">
      <div class="grid grid-cols-1 md:grid-cols-4">
        <div class="p-8 flex flex-col items-center justify-center bg-gray-100">
          <img
            v-if="book.cover_image"
            :src="book.cover_image"
            alt="Cover"
            class="w-40 h-60 object-contain border-2 border-green-500 rounded-xl shadow mb-4"
          />
          <div v-else class="text-gray-500 text-center flex flex-col items-center justify-center h-60">
            <font-awesome-icon icon="image" class="text-5xl mb-2" />
            <p>No Cover</p>
          </div>
          <span
            :class="[
              'inline-block px-4 py-1 rounded-full text-xs font-bold',
              book.status === 'active' ? 'bg-green-200 text-green-800' : 'bg-red-100 text-red-600'
            ]"
          >
            <font-awesome-icon :icon="book.status === 'active' ? 'check-circle' : 'file-circle-xmark'" class="mr-1" />
            {{ book.status ? book.status.charAt(0).toUpperCase() + book.status.slice(1) : 'N/A' }}
          </span>
        </div>

        <div class="md:col-span-3 p-8">
          <h3 class="text-2xl font-bold text-green-700 mb-4 flex items-center gap-2">
            <font-awesome-icon icon="book" /> {{ book.title || 'N/A' }}
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 text-base mb-6">
            <div class="flex items-center gap-2"><font-awesome-icon icon="user" class="text-green-400" /> <span class="font-semibold text-green-700">Author:</span> <span class="ml-1">{{ book.author || 'N/A' }}</span></div>
            <div class="flex items-center gap-2"><font-awesome-icon icon="tags" class="text-green-400" /> <span class="font-semibold text-green-700">Category:</span> <span class="ml-1">{{ book.category?.name || 'N/A' }}</span></div>
            <div class="flex items-center gap-2"><font-awesome-icon icon="building" class="text-green-400" /> <span class="font-semibold text-green-700">Publisher:</span> <span class="ml-1">{{ book.publisher || 'N/A' }}</span></div>
            <div class="flex items-center gap-2"><font-awesome-icon icon="language" class="text-green-400" /> <span class="font-semibold text-green-700">Language:</span> <span class="ml-1">{{ book.language || 'N/A' }}</span></div>
            <div class="flex items-center gap-2"><font-awesome-icon icon="barcode" class="text-green-400" /> <span class="font-semibold text-green-700">ISBN:</span> <span class="ml-1">{{ book.isbn || 'N/A' }}</span></div>
            <div class="flex items-center gap-2"><font-awesome-icon icon="calendar" class="text-green-400" /> <span class="font-semibold text-green-700">Published Year:</span> <span class="ml-1">{{ book.published_year || 'N/A' }}</span></div>
            <div class="flex items-center gap-2"><font-awesome-icon icon="eye" class="text-green-400" /> <span class="font-semibold text-green-700">Reads:</span> <span class="ml-1">{{ book.read_count ?? 0 }}</span></div>
            <div class="flex items-center gap-2"><font-awesome-icon icon="star" class="text-yellow-400" /> <span class="font-semibold text-green-700">Reviews:</span> <span class="ml-1">{{ book.reviews_count ?? 0 }}</span></div>
            <div class="flex items-center gap-2"><font-awesome-icon icon="trophy" class="text-yellow-500" /> <span class="font-semibold text-green-700">Rating:</span> <span class="ml-1">
              <span v-if="book.average_rating !== null" class="inline-block px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 font-bold">{{ book.average_rating }}</span>
              <span v-else>—</span>
            </span></div>
          </div>

          <div class="mt-4 text-base">
            <div class="font-semibold text-green-700 mb-1 flex items-center gap-2"><font-awesome-icon icon="align-left" /> Description:</div>
            <p class="text-gray-700 bg-green-50 rounded-lg p-4">{{ book.description || 'N/A' }}</p>
          </div>

          <div class="mt-4 text-base">
            <div class="font-semibold text-green-700 mb-1 flex items-center gap-2"><font-awesome-icon icon="file" /> Ebook File:</div>
            <div v-if="book.ebook_file">
              <a :href="book.ebook_file" target="_blank" class="text-green-700 underline flex items-center gap-1 font-semibold hover:text-green-900 transition">
                <font-awesome-icon icon="download" /> Download Ebook
              </a>
            </div>
            <div v-else class="text-gray-500">N/A</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
