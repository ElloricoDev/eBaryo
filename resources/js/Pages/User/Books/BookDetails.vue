<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { usePage, Head, Link, router } from '@inertiajs/vue3';
import EpubReader from '@/Components/EpubReader.vue';
import PdfReader from '@/Components/PdfReader.vue';
import { ref, computed, nextTick } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowLeft, faBookOpen, faUpRightFromSquare, faBookmark, faXmark, faFlag, faBook, faFile, faDownload, faUser, faBarcode, faCalendar, faBuilding, faLanguage, faTag, faCircle } from '@fortawesome/free-solid-svg-icons'

library.add(faArrowLeft, faBookOpen, faUpRightFromSquare, faBookmark, faXmark, faFlag, faBook, faFile, faDownload, faUser, faBarcode, faCalendar, faBuilding, faLanguage, faTag, faCircle)

defineOptions({ layout: UserLayout });
const { props } = usePage();
const book = props.book;
const savedBookIds = ref(props.saved_books || []);
const isSaved = computed(() => savedBookIds.value.includes(book.id));
const showReader = ref(false);
const readerSection = ref(null);

const from = new URLSearchParams(window.location.search).get('from') || 'books';
const getBackRoute = () => {
  switch (from) {
    case 'home': return route('home');
    case 'saved': return route('books.saved');
    case 'books':
    case 'details':
    default: return route('books.index');
  }
};

function handleReadNow() {
  showReader.value = !showReader.value;
  if (!showReader.value) return;
  nextTick(() => {
    setTimeout(() => {
      if (readerSection.value) {
        const offset = 100; // px, adjust as needed
        const top = readerSection.value.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    }, 200);
  });
}

function saveBook() {
  if (isSaved.value) return;
  router.post(route('books.save', { id: book.id }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      if (!savedBookIds.value.includes(book.id)) savedBookIds.value.push(book.id);
      alert(`"${book.title}" has been saved to your library!`);
    },
    onError: (errors) => {
      console.error('Error saving book:', errors);
      alert('Failed to save book. Please try again.');
    }
  });
}

function unsaveBook() {
  if (!isSaved.value) return;
  router.post(route('books.unsave', { id: book.id }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      const index = savedBookIds.value.indexOf(book.id);
      if (index > -1) savedBookIds.value.splice(index, 1);
      alert(`"${book.title}" has been removed from your saved books.`);
    },
    onError: (errors) => {
      console.error('Error unsaving book:', errors);
      alert('Failed to unsave book. Please try again.');
    }
  });
}

function reportBook() {
  alert('Book reported! (feature coming soon)');
}

const fileType = computed(() => {
  if (!book.ebook_file) return null;
  return book.ebook_file.split('.').pop()?.toLowerCase();
});

const isPdf = computed(() => fileType.value === 'pdf');
const isEpub = computed(() => fileType.value === 'epub');
</script>

<template>
  <Head :title="book.title" />
  <div class="relative min-h-screen max-w-6xl mx-auto py-6 px-4 overflow-x-hidden">
    <!-- Animated Gradient Background -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 via-green-50 to-white animate-gradient-move"></div>
    <div class="animate-fade-in bg-white bg-opacity-90 rounded-2xl shadow-xl p-6">
      <div class="mb-4">
        <Link :href="getBackRoute()" class="inline-flex items-center gap-2 text-green-700 border border-green-600 hover:bg-green-50 focus:bg-green-100 transition px-4 py-2 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-green-400">
          <font-awesome-icon icon="arrow-left" class="h-5 w-5" /> Back
        </Link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <img
            :src="book.cover_image || '/images/default.svg'"
            :alt="book.title"
            class="w-full rounded-xl border-2 border-green-600 bg-green-50 mb-4 shadow"
          />

          <div class="space-y-2">
            <button v-if="book.ebook_file" @click="handleReadNow" class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 focus:bg-green-800 text-white font-semibold px-4 py-2 rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-green-400 animate-pulse-cta">
              <font-awesome-icon icon="book-open" class="h-5 w-5" />
              {{ showReader ? 'Hide Reader' : 'Read Now' }}
            </button>

            <Link
              v-if="book.ebook_file"
              :href="route('books.read', { id: book.id, from: 'details' })"
              class="w-full flex items-center justify-center gap-2 border border-green-600 text-green-700 hover:bg-green-50 focus:bg-green-100 font-semibold px-4 py-2 rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-green-400"
            >
              <font-awesome-icon icon="up-right-from-square" class="h-5 w-5" />
              Open in Full Screen
            </Link>

            <button
              v-if="!isSaved"
              @click="saveBook"
              class="w-full flex items-center justify-center gap-2 border border-green-600 text-green-700 hover:bg-green-50 focus:bg-green-100 font-semibold px-4 py-2 rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-green-400"
            >
              <font-awesome-icon icon="bookmark" class="h-5 w-5" />
              Save
            </button>

            <button
              v-else
              @click="unsaveBook"
              class="w-full flex items-center justify-center gap-2 border border-yellow-500 text-yellow-600 hover:bg-yellow-50 focus:bg-yellow-100 font-semibold px-4 py-2 rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-yellow-400"
            >
              <font-awesome-icon icon="bookmark" class="h-5 w-5" />
              <font-awesome-icon icon="xmark" class="h-4 w-4 text-yellow-600" />
              Unsave
            </button>

            <button @click="reportBook" class="w-full flex items-center justify-center gap-2 border border-red-500 text-red-600 hover:bg-red-50 focus:bg-red-100 font-semibold px-4 py-2 rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-red-400">
              <font-awesome-icon icon="flag" class="h-5 w-5" />
              Report
            </button>
          </div>
        </div>

        <div class="md:col-span-2">
          <h2 class="text-2xl font-bold text-green-700 mb-3 flex items-center gap-2">
            <font-awesome-icon icon="book" class="h-6 w-6" />
            {{ book.title }}
          </h2>

          <div class="border-t border-green-200 my-4"></div>

          <p><span class="text-green-600 inline-flex items-center gap-1"><font-awesome-icon icon="user" /> Author:</span> {{ book.author }}</p>
          <p><span class="text-green-600 inline-flex items-center gap-1"><font-awesome-icon icon="barcode" /> ISBN:</span> {{ book.isbn || 'N/A' }}</p>
          <p><span class="text-green-600 inline-flex items-center gap-1"><font-awesome-icon icon="calendar" /> Published Year:</span> {{ book.published_year || 'N/A' }}</p>
          <p><span class="text-green-600 inline-flex items-center gap-1"><font-awesome-icon icon="building" /> Publisher:</span> {{ book.publisher || 'N/A' }}</p>
          <p><span class="text-green-600 inline-flex items-center gap-1"><font-awesome-icon icon="language" /> Language:</span> {{ book.language || 'N/A' }}</p>
          <p><span class="text-green-600 inline-flex items-center gap-1"><font-awesome-icon icon="tag" /> Category:</span> {{ book.category?.name || 'N/A' }}</p>
          <p>
            <span class="text-green-600 inline-flex items-center gap-1"><font-awesome-icon icon="circle" :class="book.status === 'active' ? 'text-green-500' : 'text-red-500'" /> Status:</span>
            <span :class="book.status === 'active' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
              {{ book.status }}
            </span>
          </p>

          <div class="border-t border-green-200 my-4"></div>

          <p class="mt-4"><strong class="text-green-600">Description:</strong></p>
          <div class="mb-3 prose max-w-none" v-html="book.description || '<em>No description provided.</em>'"></div>

          <p class="text-sm text-gray-500">Added: {{ new Date(book.created_at).toLocaleString() }}</p>
          <p class="text-sm text-gray-500">Last Updated: {{ new Date(book.updated_at).toLocaleString() }}</p>

          <span v-if="!book.ebook_file" class="text-red-600">No eBook file available.</span>
        </div>
      </div>

      <div v-if="showReader && book.ebook_file" class="mt-8" ref="readerSection">
        <PdfReader v-if="isPdf" :url="book.ebook_file" />
        <EpubReader v-else-if="isEpub" :url="book.ebook_file" />

        <div v-else class="text-center py-10 animate-fade-in">
          <font-awesome-icon icon="file" class="mx-auto text-gray-400 text-5xl" />
          <h5 class="text-lg font-medium text-gray-500 mt-4">Unsupported File Format</h5>
          <p class="text-gray-400">This file format is not supported for reading in the browser.</p>
          <a :href="book.ebook_file" target="_blank" class="inline-flex items-center gap-2 border border-green-600 text-green-700 hover:bg-green-50 focus:bg-green-100 font-semibold px-4 py-2 rounded-lg shadow transition mt-3 focus:outline-none focus:ring-2 focus:ring-green-400">
            <font-awesome-icon icon="download" class="h-5 w-5" />
            Download File
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