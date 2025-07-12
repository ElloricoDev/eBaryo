<script setup>
import { Link } from '@inertiajs/vue3';
import BookCard from '@/Components/BookCard.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = defineProps({
  continueReadingList: {
    type: Array,
    default: () => []
  },
  savedBookIds: {
    type: Array,
    default: () => []
  },
  auth: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['save', 'unsave']);

const saveBook = (book) => {
  emit('save', book);
};

const unsaveBook = (book) => {
  emit('unsave', book);
};
</script>

<template>
  <div class="mb-8 border-2 border-green-400 bg-green-50 rounded-xl shadow-lg p-4">
    <div class="flex items-center mb-3">
      <font-awesome-icon
        icon="play-circle"
        class="text-green-600 text-xl mr-2"
      />
      <h3 class="text-lg sm:text-xl font-bold text-green-700 mr-2">
        Continue Reading
      </h3>
      <span
        class="bg-green-400 text-white text-xs font-bold px-2 py-1 rounded-full ml-2"
      >
        In Progress
      </span>
    </div>

    <div v-if="continueReadingList.length" class="section-scroll-row hide-scrollbar">
      <BookCard
        v-for="log in continueReadingList"
        :key="log.id"
        :book="{
          ...log.book,
          from: 'continue',
          highlightProgress: true,
          progress: log.last_percent,
        }"
        :isSaved="savedBookIds.includes(log.book.id)"
        :auth="auth"
        @save="saveBook"
        @unsave="unsaveBook"
        class="mx-2 min-w-[180px] max-w-[220px] sm:min-w-[260px] sm:max-w-[260px] flex-shrink-0"
      >
        <template #footer>
          <div class="flex items-center justify-between w-full">
            <div class="text-xs text-green-700 font-semibold">
              {{ Math.round((log.last_percent || 0) * 100) }}%
              read
            </div>
            <Link
              :href="route('books.read', { id: log.book.id })"
              class="ml-2 text-green-600 hover:underline font-bold"
            >
              Continue Read
            </Link>
          </div>
        </template>
      </BookCard>
    </div>

    <div v-else class="text-center text-gray-500 py-8">
      <font-awesome-icon
        icon="play-circle"
        class="text-green-400 text-3xl mb-2"
      />
      <div>No books in progress. Start reading to see your progress here!</div>
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