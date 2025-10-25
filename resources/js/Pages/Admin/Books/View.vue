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
  <div class=" mx-auto px-4">
    <!-- Hero Header -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
            <font-awesome-icon icon="eye" class="w-8 h-8 text-white" />
          </div>
          <div>
            <h1 class="text-3xl font-bold text-slate-800">Book Details</h1>
            <p class="text-slate-600 mt-1">View comprehensive book information</p>
          </div>
        </div>
        <Link :href="route('admin.books.index')"
          class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-colors"
        >
          <font-awesome-icon icon="arrow-left" class="w-4 h-4" /> Back to Books
        </Link>
      </div>
    </div>

    <!-- Book Details Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="grid grid-cols-1 lg:grid-cols-4">
        <!-- Cover Image Section -->
        <div class="lg:col-span-1 bg-slate-50 p-8 flex flex-col items-center justify-center">
          <div class="relative">
            <img
              v-if="book.cover_image"
              :src="book.cover_image"
              alt="Book Cover"
              class="w-48 h-64 object-cover rounded-xl shadow-lg border border-slate-200"
            />
            <div v-else class="w-48 h-64 bg-slate-200 rounded-xl flex flex-col items-center justify-center border border-slate-300">
              <font-awesome-icon icon="image" class="text-4xl text-slate-400 mb-2" />
              <p class="text-slate-500 text-sm">No Cover Image</p>
            </div>
            <div class="mt-4">
              <span
                :class="[
                  'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                  book.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                ]"
              >
                <font-awesome-icon :icon="book.status === 'active' ? 'check-circle' : 'file-circle-xmark'" class="w-4 h-4 mr-1" />
                {{ book.status ? book.status.charAt(0).toUpperCase() + book.status.slice(1) : 'N/A' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Book Information Section -->
        <div class="lg:col-span-3 p-8">
          <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800 mb-2">{{ book.title || 'N/A' }}</h2>
            <p class="text-slate-600 text-lg">by {{ book.author || 'Unknown Author' }}</p>
          </div>

          <!-- Book Details Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="space-y-4">
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Category</label>
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-medium">
                  {{ book.category?.name || 'No Category' }}
                </div>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Publisher</label>
                <p class="text-slate-600">{{ book.publisher || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Language</label>
                <p class="text-slate-600">{{ book.language || 'N/A' }}</p>
              </div>
              
            </div>
            <div class="space-y-4">
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Published Year</label>
                <p class="text-slate-600">{{ book.published_year || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Read Count</label>
                <p class="text-slate-600">{{ book.read_count ?? 0 }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Reviews</label>
                <p class="text-slate-600">{{ book.reviews_count ?? 0 }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Average Rating</label>
                <div v-if="book.average_rating !== null" class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-semibold">
                  <font-awesome-icon icon="star" class="w-4 h-4 mr-1" />
                  {{ book.average_rating }}
                </div>
                <p v-else class="text-slate-400">No ratings yet</p>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div class="mb-6">
            <label class="block text-slate-700 font-semibold mb-2">Description</label>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
              <p class="text-slate-700 leading-relaxed">{{ book.description || 'No description available' }}</p>
            </div>
          </div>

          <!-- Ebook File -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Ebook File</label>
            <div v-if="book.ebook_file" class="inline-flex items-center gap-2">
              <a :href="book.ebook_file" target="_blank" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-colors font-medium">
                <font-awesome-icon icon="download" class="w-4 h-4" />
                Download Ebook
              </a>
              <span class="text-slate-500 text-sm">{{ fileExt.toUpperCase() }} File</span>
            </div>
            <p v-else class="text-slate-400">No ebook file available</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
