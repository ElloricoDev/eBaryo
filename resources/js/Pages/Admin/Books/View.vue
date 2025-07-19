<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faArrowLeft, faEye, faBook, faUser, faTags, faBuilding, faLanguage, faBarcode, faCalendar, faInfoCircle, faAlignLeft, faImage, faFile, faFileCircleXmark, faDownload, faStar, faTrophy
} from '@fortawesome/free-solid-svg-icons'

library.add(faArrowLeft, faEye, faBook, faUser, faTags, faBuilding, faLanguage, faBarcode, faCalendar, faInfoCircle, faAlignLeft, faImage, faFile, faFileCircleXmark, faDownload, faStar, faTrophy)

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
  <div class="max-w-7xl mx-auto py-6 px-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center mb-6 gap-4">
      <Link :href="route('admin.books.index')"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-700 text-white font-bold rounded-full shadow-lg hover:from-green-600 hover:to-green-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2"
      >
        <font-awesome-icon icon="arrow-left" class="text-lg" />
        <span class="hidden sm:inline">Back to Books</span>
        <span class="sm:hidden">Back</span>
      </Link>
      <h1 class="text-2xl font-bold text-green-600 flex items-center gap-2">
        <font-awesome-icon icon="eye" /> Book Details
      </h1>
    </div>

    <div class="bg-white border-2 border-green-500 shadow-md rounded-2xl mb-6 overflow-hidden">
      <div class="grid grid-cols-1 md:grid-cols-4">
        <div class="p-4 flex items-center justify-center bg-gray-100">
          <img
            v-if="book.cover_image"
            :src="book.cover_image"
            alt="Cover"
            class="w-32 h-48 object-contain border border-green-500 rounded shadow"
          />
          <div v-else class="text-gray-500 text-center">
            <font-awesome-icon icon="image" class="text-4xl" />
            <p>No Cover</p>
          </div>
        </div>

        <div class="md:col-span-3 p-6">
          <h3 class="text-xl font-semibold text-green-600 mb-2 flex items-center gap-2">
            <font-awesome-icon icon="book" /> {{ book.title || 'N/A' }}
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
            <div><strong><font-awesome-icon icon="user" /> Author:</strong> {{ book.author || 'N/A' }}</div>
            <div><strong><font-awesome-icon icon="tags" /> Category:</strong> {{ book.category?.name || 'N/A' }}</div>
            <div><strong><font-awesome-icon icon="building" /> Publisher:</strong> {{ book.publisher || 'N/A' }}</div>
            <div><strong><font-awesome-icon icon="language" /> Language:</strong> {{ book.language || 'N/A' }}</div>
            <div><strong><font-awesome-icon icon="barcode" /> ISBN:</strong> {{ book.isbn || 'N/A' }}</div>
            <div><strong><font-awesome-icon icon="calendar" /> Published Year:</strong> {{ book.published_year || 'N/A' }}</div>
            <div>
              <strong><font-awesome-icon icon="info-circle" /> Status:</strong>
              <span :class="book.status === 'active' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
                {{ book.status ? book.status.charAt(0).toUpperCase() + book.status.slice(1) : 'N/A' }}
              </span>
            </div>
            <div><strong><font-awesome-icon icon="eye" /> Reads:</strong> {{ book.read_count ?? 0 }}</div>
            <div><strong><font-awesome-icon icon="star" /> Reviews:</strong> {{ book.reviews_count ?? 0 }}</div>
            <div><strong><font-awesome-icon icon="trophy" /> Rating:</strong> {{ book.average_rating !== null ? book.average_rating : '—' }}</div>
          </div>

          <div class="mt-3 text-sm">
            <strong><font-awesome-icon icon="align-left" /> Description:</strong>
            <p class="text-gray-600">{{ book.description || 'N/A' }}</p>
          </div>

          <div class="mt-3 text-sm">
            <strong><font-awesome-icon icon="file" /> Ebook File:</strong>
            <div v-if="book.ebook_file">
              <a :href="book.ebook_file" target="_blank" class="text-green-600 underline flex items-center gap-1">
                <font-awesome-icon icon="file" /> {{ book.ebook_file }}
              </a>
            </div>
            <div v-else class="text-gray-500">N/A</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
