<script setup>
import UserLayout from '@/Layouts/UserLayout.vue'
import { usePage, router, Head } from '@inertiajs/vue3'
import BookCard from '@/Components/BookCard.vue'
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowLeft } from '@fortawesome/free-solid-svg-icons'

library.add(faArrowLeft)

defineOptions({ layout: UserLayout })
const { props } = usePage()
const books = props.books || []
const savedBookIds = ref(Array.isArray(props.saved_books) ? props.saved_books : [])
const search = ref('')

// Ensure valid array
const safeSavedBookIds = computed(() => {
  return Array.isArray(savedBookIds.value) ? savedBookIds.value : []
})

const filteredBooks = computed(() => {
  if (!search.value) return books
  return books.filter(book =>
    (book.title && book.title.toLowerCase().includes(search.value.toLowerCase())) ||
    (book.author && book.author.toLowerCase().includes(search.value.toLowerCase()))
  )
})

const handleSearch = (e) => {
  search.value = e.detail
}

onMounted(() => {
  window.addEventListener('user-search', handleSearch)
})
onUnmounted(() => {
  window.removeEventListener('user-search', handleSearch)
})

const saveBook = (book) => {
  router.post(route('books.save', { id: book.id }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      if (!savedBookIds.value.includes(book.id)) {
        savedBookIds.value.push(book.id)
      }
    },
    onError: (errors) => {
      console.error('Error saving book:', errors)
    }
  })
}

const unsaveBook = (book) => {
  router.post(route('books.unsave', { id: book.id }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      const index = savedBookIds.value.indexOf(book.id)
      if (index > -1) {
        savedBookIds.value.splice(index, 1)
      }
    },
    onError: (errors) => {
      console.error('Error unsaving book:', errors)
    }
  })
}

function goBack() { window.history.back(); }
</script>

<template>
  <Head title="All Books" />
  <div>
    <button @click="goBack" class="block sm:hidden mb-4 text-green-700 font-bold flex items-center gap-2">
      <font-awesome-icon icon="arrow-left" class="text-lg" />
      Back
    </button>
    <h1 class="text-green-700 font-bold text-2xl flex items-center gap-2 mb-6">
      <i class="bi bi-journals"></i> All Books
    </h1>

    <div class="grid grid-cols-2 gap-3 px-1 pb-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 sm:gap-6 sm:px-0">
      <template v-if="filteredBooks.length > 0">
        <BookCard
          v-for="book in filteredBooks"
          :key="book.id"
          :book="{ ...book, from: 'books' }"
          :isSaved="safeSavedBookIds.includes(book.id)"
          :auth="$page.props.auth"
          @save="saveBook"
          @unsave="unsaveBook"
          class="w-full max-w-[180px] mx-auto sm:max-w-[220px] sm:min-w-[200px]"
        >
          
        </BookCard>
      </template>
      <template v-else>
        <div class="col-span-full text-center text-gray-500 py-10">
          <i class="bi bi-search text-green-600 text-3xl mb-2 block"></i>
          <p>No results found.</p>
        </div>
      </template>
    </div>
  </div>
</template>
