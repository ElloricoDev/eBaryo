<script setup>
import UserLayout from '@/Layouts/UserLayout.vue'
import { usePage, Head, router } from '@inertiajs/vue3'
import EpubReader from '@/Components/EpubReader.vue'
import PdfReader from '@/Components/PdfReader.vue'
import { computed, onMounted } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowLeft, faBook, faFile, faDownload } from '@fortawesome/free-solid-svg-icons'

defineOptions({ layout: UserLayout })
const { props } = usePage()
const book = props.book
const lastPercent = props.lastPercent || 0

const from = new URLSearchParams(window.location.search).get('from')

const saveProgress = (percent) => {
  router.post(
    route('books.saveProgress', { id: book.id }),
    { percent },
    { preserveState: true, preserveScroll: true, replace: true }
  )
}

const goBack = () => {
  if (window.history.length > 1) {
    window.history.back()
  } else if (from === 'list') {
    router.visit(route('books.index'))
  } else if (from === 'home') {
    router.visit(route('home'))
  } else {
    router.visit(route('books.view', { id: book.id }))
  }
}

const fileType = computed(() => {
  if (!book.ebook_file) return null
  return book.ebook_file.split('.').pop()?.toLowerCase()
})
const isPdf = computed(() => fileType.value === 'pdf')
const isEpub = computed(() => fileType.value === 'epub')

library.add(faArrowLeft, faBook, faFile, faDownload)

onMounted(() => {
  const el = document.getElementById('book-content');
  if (el) {
    el.scrollIntoView({ behavior: 'auto' });
  }
});
</script>

<template>
  <Head :title="`Read: ${book.title}`" />
  <div class="relative min-h-screen w-full px-2 sm:px-4 py-2 sm:py-4 overflow-x-hidden">
    <!-- Animated Gradient Background -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 via-green-50 to-white animate-gradient-move"></div>
    <div class="animate-fade-in bg-white bg-opacity-90 rounded-2xl shadow-xl p-2 sm:p-4">
      <!-- Header -->
      <div class="flex justify-between items-center mb-4 sticky top-0 bg-white bg-opacity-90 z-20 py-2 sm:static sm:bg-transparent sm:py-0">
        <button @click="goBack" class="text-green-700 border border-green-700 px-3 sm:px-4 py-2 rounded hover:bg-green-100 focus:bg-green-200 transition animate-pulse-cta focus:outline-none focus:ring-2 focus:ring-green-400 flex items-center gap-2 text-base sm:text-base">
          <font-awesome-icon icon="arrow-left" class="h-5 w-5" /> <span class="hidden xs:inline">Back</span>
        </button>
        <h4 class="text-green-700 font-bold flex items-center gap-2 text-base sm:text-lg text-center flex-1 justify-center">
          <font-awesome-icon icon="book" class="h-5 w-5" />
          <span class="truncate max-w-[60vw] sm:max-w-xs">{{ book.title }}</span>
        </h4>
        <span class="w-10"></span>
      </div>

      <!-- Reader Container -->
      <div class="bg-white rounded-2xl border-2 border-green-700 p-2 sm:p-4 min-h-[70vh] sm:min-h-[80vh] shadow-md hover:shadow-xl transition-shadow" id="book-content">
        <!-- PDF Reader -->
        <PdfReader
          v-if="isPdf"
          :url="book.ebook_file"
          @update:percent="saveProgress"
          :startPercent="lastPercent"
        />

        <!-- EPUB Reader -->
        <EpubReader
          v-else-if="isEpub"
          :url="book.ebook_file"
          @update:percent="saveProgress"
          :startPercent="lastPercent"
        />

        <!-- Unsupported Format -->
        <div v-else class="flex flex-col items-center justify-center text-center py-10 text-gray-600 animate-fade-in">
          <font-awesome-icon icon="file" class="mx-auto text-gray-400 text-5xl" />
          <h5 class="mt-3 text-lg sm:text-xl font-semibold">Unsupported File Format</h5>
          <p class="text-gray-500 text-sm sm:text-base">This file format is not supported for in-browser reading.</p>
          <a :href="book.ebook_file" target="_blank" class="inline-flex items-center gap-2 mt-4 px-3 sm:px-4 py-2 border border-green-700 text-green-700 rounded hover:bg-green-100 focus:bg-green-200 transition focus:outline-none focus:ring-2 focus:ring-green-400 font-semibold text-base sm:text-base">
            <font-awesome-icon icon="download" class="h-5 w-5" /> Download File
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes gradient-move {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
.animate-gradient-move {
  background-size: 200% 200%;
  animation: gradient-move 8s ease-in-out infinite;
}
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fade-in {
  animation: fade-in 1s both;
}
@keyframes pulse-cta {
  0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
  50% { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
}
.animate-pulse-cta {
  animation: pulse-cta 2s infinite;
}
input:focus, button:focus, a:focus {
  outline: 2px solid #34d399;
  outline-offset: 2px;
}
</style>
