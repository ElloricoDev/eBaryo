<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import PdfReader from '@/Components/PdfReader.vue';
import EpubReader from '@/Components/EpubReader.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faArrowLeft, faEye, faBook, faUser, faTags, faBuilding, faLanguage, faBarcode, faCalendar, faInfoCircle, faAlignLeft, faImage, faFile, faFileCircleXmark, faDownload
} from '@fortawesome/free-solid-svg-icons'

library.add(faArrowLeft, faEye, faBook, faUser, faTags, faBuilding, faLanguage, faBarcode, faCalendar, faInfoCircle, faAlignLeft, faImage, faFile, faFileCircleXmark, faDownload)

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
    <div class="flex items-center mb-6">
      <Link :href="route('admin.books.index')" class="btn btn-outline-success me-3 flex items-center gap-2">
        <font-awesome-icon icon="arrow-left" /> Back
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

    <div class="bg-white border-2 border-green-500 shadow-md rounded-2xl overflow-hidden">
      <div class="bg-green-600 text-white font-semibold px-4 py-2 rounded-t-2xl flex items-center gap-2">
        <font-awesome-icon icon="file" /> Read Ebook
      </div>
      <div class="bg-gray-100 px-4 py-6 rounded-b-2xl">
        <PdfReader v-if="isPdf && book.ebook_file" :url="book.ebook_file" />
        <EpubReader v-else-if="isEpub && book.ebook_file" :url="book.ebook_file" />

        <div v-else-if="book.ebook_file" class="text-center py-10">
          <font-awesome-icon icon="file-circle-xmark" class="text-5xl text-gray-400" />
          <h5 class="text-lg text-gray-500 mt-3">Unsupported File Format</h5>
          <p class="text-sm text-gray-500">This file format is not supported for reading in the browser.</p>
          <a :href="book.ebook_file" target="_blank" class="inline-block mt-3 px-4 py-2 border border-green-600 text-green-600 rounded hover:bg-green-100 flex items-center gap-2">
            <font-awesome-icon icon="download" /> Download File
          </a>
        </div>

        <div v-else class="text-center py-10">
          <font-awesome-icon icon="file-circle-xmark" class="text-5xl text-gray-400" />
          <h5 class="text-lg text-gray-500 mt-3">No Ebook File</h5>
          <p class="text-sm text-gray-500">No ebook file has been uploaded for this book.</p>
        </div>
      </div>
    </div>
  </div>
</template>
