<script setup>
import { ref, computed } from 'vue';
import BookCard from '@/Components/BookCard.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    required: true
  },
  books: {
    type: Array,
    default: () => []
  },
  sectionType: {
    type: String,
    required: true
  },
  savedBookIds: {
    type: Array,
    default: () => []
  },
  auth: {
    type: Object,
    required: true
  },
  emptyMessage: {
    type: String,
    default: 'No books available at the moment.'
  },
  emptyIcon: {
    type: String,
    default: 'book'
  },
  badgeText: {
    type: String,
    default: ''
  },
  badgeColor: {
    type: String,
    default: 'bg-green-400'
  },
  borderColor: {
    type: String,
    default: 'border-green-400'
  },
  bgColor: {
    type: String,
    default: 'bg-green-50'
  },
  iconColor: {
    type: String,
    default: 'text-green-600'
  },
  titleColor: {
    type: String,
    default: 'text-green-700'
  }
});

const emit = defineEmits(['save', 'unsave']);

// Pagination state
const currentPage = ref(0);
const booksPerPage = 8;

// Paginated books
const paginatedBooks = computed(() => {
  const startIndex = currentPage.value * booksPerPage;
  const endIndex = startIndex + booksPerPage;
  return props.books.slice(startIndex, endIndex);
});

// Total pages
const totalPages = computed(() => {
  return Math.ceil(props.books.length / booksPerPage);
});

// Navigation state
const hasNextPage = computed(() => {
  return currentPage.value < totalPages.value - 1;
});

const hasPreviousPage = computed(() => {
  return currentPage.value > 0;
});

// Pagination methods
const nextPage = () => {
  if (hasNextPage.value) {
    currentPage.value++;
  }
};

const previousPage = () => {
  if (hasPreviousPage.value) {
    currentPage.value--;
  }
};

const saveBook = (book) => {
  emit('save', book);
};

const unsaveBook = (book) => {
  emit('unsave', book);
};
</script>

<template>
  <div :class="borderColor ? `mb-8 border-2 ${borderColor} ${bgColor} rounded-xl shadow-lg p-4` : 'mb-8'">
    <!-- Section Header -->
    <div class="flex items-center mb-3">
      <font-awesome-icon
        :icon="icon"
        class="text-xl mr-2"
        :class="iconColor"
      />
      <h3 class="text-lg sm:text-xl font-bold mr-2" :class="titleColor">
        {{ title }}
      </h3>
      <span
        v-if="badgeText"
        class="text-white text-xs font-bold px-2 py-1 rounded-full ml-2"
        :class="badgeColor"
      >
        {{ badgeText }}
      </span>
    </div>

    <!-- Books Section -->
    <div v-if="books.length > 0" class="relative">
      <!-- Books Grid -->
      <div class="section-scroll-row hide-scrollbar">
        <BookCard
          v-for="book in paginatedBooks"
          :key="book.id"
          :book="{ ...book, from: sectionType }"
          :isSaved="savedBookIds.includes(book.id)"
          :auth="auth"
          @save="saveBook"
          @unsave="unsaveBook"
          class="mx-2 min-w-[180px] max-w-[220px] sm:min-w-[260px] sm:max-w-[260px] flex-shrink-0"
        >
          <!-- Custom footer for most read books -->
          <template v-if="sectionType === 'mostread'" #footer>
            <div class="flex items-center justify-between w-full">
              <div
                class="flex items-center gap-1 text-sm font-bold text-blue-800"
                title="Total times this book has been read by all users"
              >
                <font-awesome-icon
                  icon="eye"
                  class="text-blue-500"
                />
                <span>{{ book.read_count }}</span>
                <span class="text-xs font-normal text-blue-600"
                  >read{{
                    book.read_count === 1 ? "" : "s"
                  }}</span
                >
              </div>
            </div>
          </template>
        </BookCard>
      </div>
      
      <!-- Pagination Controls -->
      <div v-if="totalPages > 1" class="flex items-center justify-center mt-4 gap-4">
        <!-- Previous Button -->
        <button
          @click="previousPage"
          :disabled="!hasPreviousPage"
          class="flex items-center gap-2 px-4 py-2 text-white rounded-lg shadow hover:focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          :class="[
            sectionType === 'recommended' ? 'bg-yellow-600 hover:bg-yellow-700 focus:bg-yellow-800 focus:ring-yellow-400' :
            sectionType === 'new' ? 'bg-green-600 hover:bg-green-700 focus:bg-green-800 focus:ring-green-400' :
            sectionType === 'hot' ? 'bg-red-600 hover:bg-red-700 focus:bg-red-800 focus:ring-red-400' :
            sectionType === 'mostread' ? 'bg-blue-600 hover:bg-blue-700 focus:bg-blue-800 focus:ring-blue-400' :
            sectionType === 'highestrated' ? 'bg-yellow-600 hover:bg-yellow-700 focus:bg-yellow-800 focus:ring-yellow-400' :
            'bg-green-600 hover:bg-green-700 focus:bg-green-800 focus:ring-green-400'
          ]"
        >
          <font-awesome-icon icon="chevron-left" class="text-sm" />
          Previous
        </button>
        
        <!-- Page Indicator -->
        <div class="flex items-center gap-2 font-semibold" :class="[
          sectionType === 'recommended' ? 'text-yellow-700' :
          sectionType === 'new' ? 'text-green-700' :
          sectionType === 'hot' ? 'text-red-700' :
          sectionType === 'mostread' ? 'text-blue-700' :
          sectionType === 'highestrated' ? 'text-yellow-700' :
          'text-green-700'
        ]">
          <span>Page {{ currentPage + 1 }} of {{ totalPages }}</span>
          <span class="text-gray-500 text-sm">({{ books.length }} total books)</span>
        </div>
        
        <!-- Next Button -->
        <button
          @click="nextPage"
          :disabled="!hasNextPage"
          class="flex items-center gap-2 px-4 py-2 text-white rounded-lg shadow hover:focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          :class="[
            sectionType === 'recommended' ? 'bg-yellow-600 hover:bg-yellow-700 focus:bg-yellow-800 focus:ring-yellow-400' :
            sectionType === 'new' ? 'bg-green-600 hover:bg-green-700 focus:bg-green-800 focus:ring-green-400' :
            sectionType === 'hot' ? 'bg-red-600 hover:bg-red-700 focus:bg-red-800 focus:ring-red-400' :
            sectionType === 'mostread' ? 'bg-blue-600 hover:bg-blue-700 focus:bg-blue-800 focus:ring-blue-400' :
            sectionType === 'highestrated' ? 'bg-yellow-600 hover:bg-yellow-700 focus:bg-yellow-800 focus:ring-yellow-400' :
            'bg-green-600 hover:bg-green-700 focus:bg-green-800 focus:ring-green-400'
          ]"
        >
          Next
          <font-awesome-icon icon="chevron-right" class="text-sm" />
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center text-gray-500 py-8">
      <font-awesome-icon
        :icon="emptyIcon"
        class="text-3xl mb-2"
        :class="iconColor.replace('-600', '-400')"
      />
      <div>{{ emptyMessage }}</div>
    </div>
  </div>
</template>

<style scoped>
.hide-scrollbar {
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE 10+ */
}
.hide-scrollbar::-webkit-scrollbar {
  display: none; /* Chrome/Safari/Webkit */
}
.section-scroll-row {
  display: flex;
  overflow-x: auto;
  gap: 0.5rem; /* gap-2 */
  padding-left: 0.75rem; /* px-3 */
  padding-right: 0.75rem;
  padding-bottom: 0.5rem;
  scroll-behavior: smooth;
}
@media (min-width: 640px) {
  .section-scroll-row {
    gap: 1rem; /* gap-4 */
    padding-left: 1rem; /* px-4 */
    padding-right: 1rem;
  }
}
</style> 