<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { usePage, router, Head, Link } from '@inertiajs/vue3';
import BookCard from '@/Components/BookCard.vue';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faPlayCircle, faSearch, faHome } from '@fortawesome/free-solid-svg-icons';
library.add(faPlayCircle, faSearch, faHome);

const { props } = usePage();
const books = props.books || [];
const savedBookIds = ref(Array.isArray(props.saved_books) ? props.saved_books : []);
const continueReading = props.continueReading || null;
const search = ref('');

const filteredBooks = computed(() => {
  if (!search.value) return books;
  return books.filter(book =>
    (book.title && book.title.toLowerCase().includes(search.value.toLowerCase())) ||
    (book.author && book.author.toLowerCase().includes(search.value.toLowerCase()))
  );
});

const handleSearch = (e) => {
  search.value = e.detail;
};

const saveBook = (book) => {
  router.post(route('books.save', { id: book.id }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      if (!savedBookIds.value.includes(book.id)) {
        savedBookIds.value.push(book.id);
      }
    },
    onError: (errors) => {
      console.error('Error saving book:', errors);
    }
  });
};

const unsaveBook = (book) => {
  router.post(route('books.unsave', { id: book.id }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      const index = savedBookIds.value.indexOf(book.id);
      if (index > -1) {
        savedBookIds.value.splice(index, 1);
      }
    },
    onError: (errors) => {
      console.error('Error unsaving book:', errors);
    }
  });
};

onMounted(() => {
  window.addEventListener('user-search', handleSearch);
});
onUnmounted(() => {
  window.removeEventListener('user-search', handleSearch);
});
</script>

<template>
  <Head title="Home" />
  <UserLayout>
    <!-- Animated Gradient Background -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 via-green-50 to-white animate-gradient-move"></div>
    <!-- Continue Reading -->
    <div
      v-if="continueReading && continueReading.book"
      class="bg-green-100 border-2 border-green-600 rounded-xl flex justify-between items-center p-4 mb-6 shadow-lg animate-slide-down"
    >
      <div>
        <strong class="text-green-700">Continue Reading:</strong>
        {{ continueReading.book.title }}
        <span v-if="continueReading.last_percent" class="text-green-800 ml-1">
          ({{ Math.round(continueReading.last_percent * 100) }}% read)
        </span>
      </div>
      <Link
        :href="route('books.read', { id: continueReading.book.id })"
        class="inline-flex items-center bg-green-600 hover:bg-green-700 focus:bg-green-800 text-white text-sm font-medium px-3 py-1.5 rounded-md shadow-md animate-pulse-cta transition focus:outline-none focus:ring-2 focus:ring-green-400"
        aria-label="Resume reading"
      >
        <font-awesome-icon icon="play-circle" class="mr-2" /> Resume
      </Link>
    </div>

    <!-- Book Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in">
      <template v-if="filteredBooks.length > 0">
        <BookCard
          v-for="book in filteredBooks"
          :key="book.id"
          :book="{...book, from: 'home'}"
          :isSaved="savedBookIds.includes(book.id)"
          @save="saveBook"
          @unsave="unsaveBook"
          class="rounded-2xl shadow-md hover:shadow-xl transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-400"
        />
      </template>
      <template v-else>
        <div class="col-span-full text-center text-gray-500 py-12 animate-fade-in">
          <font-awesome-icon icon="search" class="text-green-600 text-3xl mb-2" />
          <div>No results found.</div>
        </div>
      </template>
    </div>
  </UserLayout>
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
@keyframes slide-down {
  from { transform: translateY(-30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.animate-slide-down {
  animation: slide-down 0.7s cubic-bezier(0.4,0,0.2,1) both;
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
a:focus, button:focus, .focus\:outline-none:focus {
  outline: 2px solid #34d399;
  outline-offset: 2px;
}
</style>
