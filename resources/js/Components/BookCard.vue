<script setup>
import { router, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Swal from 'sweetalert2';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBook, faEye, faBookmark, faFlag, faXmarkCircle, faHourglassHalf, faXmark, faUser, faTag, faBuilding, faStar } from '@fortawesome/free-solid-svg-icons'

library.add(faBook, faEye, faBookmark, faFlag, faXmarkCircle, faHourglassHalf, faXmark, faUser, faTag, faBuilding, faStar)

const props = defineProps({
  book: Object,
  isSaved: Boolean,
  auth: Object
});

const emit = defineEmits(['save', 'unsave', 'report']);

const showReportModal = ref(false);
const reportReason = ref('');
const reportDescription = ref('');
const isSubmittingReport = ref(false);

const isBookSaved = computed(() => props.isSaved);
const avatarUrl = computed(() => props.book.cover_image && props.book.cover_image !== '' ? props.book.cover_image : '/images/default.svg');
const user = props.auth?.user || null;
const isVerified = computed(() => user && user.email_verified_at);

const goToRead = () => {
  if (!isVerified.value) {
    Swal.fire({ icon: 'warning', title: 'Verify your email', text: 'Please verify your email to read this book.' });
    return;
  }
  const params = { id: props.book.id };
  if (props.book.from) params.from = props.book.from;
  router.visit(route('books.read', params), { preserveScroll: false });
};

const saveScrollAndGoTo = (type) => {
  if (type === 'view') {
    sessionStorage.setItem('scrollPosition', window.scrollY);
    sessionStorage.setItem('scrollBookId', props.book.id);
    // Let the link proceed as normal
  }
};

const handleSave = () => {
  if (isBookSaved.value) return;
  emit('save', props.book);
  Swal.fire({ icon: 'success', title: 'Book saved!', text: `"${props.book.title}" has been added to your saved books.`, timer: 1500, showConfirmButton: false });
};

const handleUnsave = () => {
  if (!isBookSaved.value) return;
  Swal.fire({
    title: 'Unsave this book?',
    text: `Are you sure you want to remove "${props.book.title}" from your saved books?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Unsave',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      emit('unsave', props.book);
      Swal.fire({ icon: 'success', title: 'Book unsaved!', text: `"${props.book.title}" has been removed.`, timer: 1500, showConfirmButton: false });
    }
  });
};

const handleReport = () => {
  if (!isVerified.value) {
    Swal.fire({ icon: 'warning', title: 'Verify your email', text: 'Please verify your email to report a book.' });
    return;
  }
  showReportModal.value = true;
};

const submitReport = () => {
  if (!reportReason.value.trim()) {
    Swal.fire({ icon: 'error', title: 'Reason Required', text: 'Please provide a reason.' });
    return;
  }

  isSubmittingReport.value = true;

  router.post(route('books.report', { bookId: props.book.id }), {
    reason: reportReason.value,
    description: reportDescription.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showReportModal.value = false;
      reportReason.value = '';
      reportDescription.value = '';
      Swal.fire({ icon: 'success', title: 'Report Submitted', text: 'Thank you for your report.', timer: 2000, showConfirmButton: false });
    },
    onError: () => {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to submit. Try again.' });
    },
    onFinish: () => {
      isSubmittingReport.value = false;
    },
  });
};

const closeReportModal = () => {
  showReportModal.value = false;
  reportReason.value = '';
  reportDescription.value = '';
};

defineOptions({ inheritAttrs: false })
</script>

<template>
  <div
    v-bind="$attrs"
    :data-book-id="book.id"
    class="relative animate-fade-in bg-white bg-opacity-90 border border-green-200 border-[1px] rounded-lg shadow-md hover:shadow-2xl hover:scale-[1.025] transition-all duration-300 w-full flex flex-col overflow-hidden group m-0"
    tabindex="0"
  >
    <!-- Animated Gradient Background -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-50 via-white to-green-100 animate-gradient-move"></div>
    <div class="bg-gradient-to-b from-green-50 to-white p-1 pb-0 flex flex-col items-center">
      <img
        :src="avatarUrl"
        class="w-[120px] h-[170px] object-contain rounded-xl shadow group-hover:scale-105 transition-transform duration-300 border border-green-100 bg-white m-0"
        alt="Book Cover"
        @error="event.target.src = '/images/default.svg'"
      />
    </div>
    <div class="flex-1 flex flex-col px-1 pt-1 pb-1">
      <h5 class="text-green-800 font-extrabold text-xl mb-1 truncate text-center">{{ book.title }}</h5>
      <!-- Show reading percent if available -->
      <div v-if="typeof book.progress === 'number' && book.progress > 0" class="text-xs text-green-700 text-center font-semibold mb-1">
        {{ Math.round(book.progress * 100) }}% read
      </div>
      <!-- Ratings and Reviews -->
      <div class="flex items-center justify-center gap-1 mb-1">
        <template v-if="book.average_rating && book.reviews_count > 0">
          <span :class="['flex items-center', book.highlightRating ? 'text-yellow-400' : 'text-yellow-500']">
            <template v-for="i in 5">
              <svg
                v-if="i <= Math.round(book.average_rating)"
                :key="'star-full-' + i"
                :class="book.highlightRating ? 'w-5 h-5' : 'w-4 h-4'"
                class="fill-current"
                viewBox="0 0 20 20"
              >
                <polygon points="10,1 12.59,6.99 19,7.64 14,12.26 15.18,18.51 10,15.27 4.82,18.51 6,12.26 1,7.64 7.41,6.99" />
              </svg>
              <svg
                v-else
                :key="'star-empty-' + i"
                :class="book.highlightRating ? 'w-5 h-5' : 'w-4 h-4'"
                class="text-gray-300"
                viewBox="0 0 20 20"
              >
                <polygon fill="none" stroke="currentColor" stroke-width="1" points="10,1 12.59,6.99 19,7.64 14,12.26 15.18,18.51 10,15.27 4.82,18.51 6,12.26 1,7.64 7.41,6.99" />
              </svg>
            </template>
          </span>
          <span class="text-sm text-gray-700 font-semibold ml-1">{{ book.average_rating }}</span>
          <span class="text-xs text-gray-500 ml-1">({{ book.reviews_count }} review{{ book.reviews_count > 1 ? 's' : '' }})</span>
        </template>
        <template v-else>
          <span class="text-xs text-gray-400">No ratings yet</span>
        </template>
      </div>
      <!-- Total Ratings and Total Reads Row -->
      <div class="flex items-center justify-center gap-4 mb-1">
        <div class="flex items-center gap-1 text-xs text-yellow-700" v-if="book.reviews_count > 0">
          <font-awesome-icon icon="star" class="text-yellow-400" />
          <span>{{ book.reviews_count }}</span>
          <span class="text-xs text-gray-500">rating{{ book.reviews_count === 1 ? '' : 's' }}</span>
        </div>
        <div class="flex items-center gap-1 text-xs text-blue-800" v-if="book.read_count > 0">
          <font-awesome-icon icon="eye" class="text-blue-500" />
          <span>{{ book.read_count }}</span>
          <span class="text-xs text-blue-600">read{{ book.read_count === 1 ? '' : 's' }}</span>
        </div>
      </div>
      <p class="text-xs text-center mb-1">
        <span class="text-green-600 font-medium"><font-awesome-icon icon="user" class="mr-1" />Author:</span>
        <span class="text-gray-500">{{ book.author || 'Unknown' }}</span>
      </p>
      <p class="text-xs text-center mb-1">
        <span class="text-green-600 font-medium"><font-awesome-icon icon="tag" class="mr-1" />Genre:</span>
        <span class="text-gray-500">{{ book.category?.name || book.genre || 'Unknown' }}</span>
      </p>
      <p class="text-xs text-center mb-2">
        <span class="text-green-600 font-medium"><font-awesome-icon icon="building" class="mr-1" />Publisher:</span>
        <span class="text-gray-500">{{ book.publisher || 'N/A' }}</span>
      </p>
      <div class="border-t border-green-100 my-2"></div>
      <div class="mt-auto flex flex-col gap-1">
        <div class="flex gap-1 justify-center">
          <button
            class="flex-1 flex items-center justify-center gap-1 px-2 py-1 text-xs sm:px-3 sm:py-1 sm:text-sm bg-green-600 text-white rounded-lg font-semibold shadow hover:bg-green-700 focus:bg-green-800 animate-pulse-cta focus:outline-none focus:ring-2 focus:ring-green-400 transition-colors duration-200"
            @click="goToRead"
          >
            <font-awesome-icon icon="book" class="text-white text-lg" />
            <span>
              <template v-if="typeof book.progress === 'number' && book.progress > 0 && book.progress < 1">Continue Read</template>
              <template v-else>Read</template>
            </span>
          </button>
          <Link
            :href="route('books.view', { id: book.id, from: book.from || 'books', returnTo: book.from || 'books' })"
            class="flex-1 flex items-center justify-center gap-1 px-2 py-1 text-xs sm:px-3 sm:py-1 sm:text-sm border border-green-600 text-green-700 rounded-lg font-semibold shadow hover:bg-green-50 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-400 transition-colors duration-200"
            @click="() => saveScrollAndGoTo('view')"
          >
            <font-awesome-icon icon="eye" class="text-green-700 text-lg" />
            <span>View</span>
          </Link>
        </div>
        <div class="flex gap-1 justify-center">
          <button
            v-if="!isBookSaved"
            class="flex-1 flex items-center justify-center gap-1 px-2 py-1 text-xs sm:px-3 sm:py-1 sm:text-sm border border-green-600 text-green-700 rounded-lg font-semibold shadow hover:bg-green-50 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-400 transition-colors duration-200"
            @click="handleSave"
          >
            <font-awesome-icon icon="bookmark" class="text-green-700 text-lg" />
            <span>Save</span>
          </button>
          <button
            v-else
            class="flex-1 flex items-center justify-center gap-1 px-2 py-1 text-xs sm:px-3 sm:py-1 sm:text-sm border border-yellow-500 text-yellow-600 rounded-lg font-semibold shadow hover:bg-yellow-50 focus:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-colors duration-200"
            @click="handleUnsave"
          >
            <font-awesome-icon icon="xmark" class="text-yellow-600 text-lg" />
            <span>Unsave</span>
          </button>
          <button
            class="flex-1 flex items-center justify-center gap-1 px-2 py-1 text-xs sm:px-3 sm:py-1 sm:text-sm border border-red-500 text-red-600 rounded-lg font-semibold shadow hover:bg-red-50 focus:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-400 transition-colors duration-200"
            @click="handleReport"
          >
            <font-awesome-icon icon="flag" class="text-red-600 text-lg" />
            <span>Report</span>
          </button>
        </div>
      </div>
    </div>
    <!-- Move the footer slot here, always visible, with border for clarity -->
    <div v-if="$slots.footer" class="border-t border-green-100 pt-2 mt-2 bg-green-50/60">
      <slot name="footer" />
    </div>
  </div>

  <!-- Modal -->
  <teleport to="body">
    <div v-if="showReportModal" class="fixed inset-0 z-[9999] flex items-center justify-center">
      <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm transition-all duration-300 pointer-events-auto"></div>
      <div class="relative animate-fade-in bg-white bg-opacity-95 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden z-10 pointer-events-auto">
        <!-- Animated Gradient Background -->
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-50 via-white to-green-100 animate-gradient-move"></div>
        <div class="bg-green-600 text-white px-4 py-3 rounded-t-2xl flex justify-between items-center">
          <h5 class="font-bold"><font-awesome-icon icon="flag" /> Report Book</h5>
          <button class="text-white focus:outline-none focus:ring-2 focus:ring-white rounded" @click="closeReportModal">&times;</button>
        </div>
        <div class="border-t border-green-100"></div>
        <div class="p-4">
          <p class="text-sm text-gray-600 mb-4">Provide a reason for reporting "<strong>{{ book.title }}</strong>":</p>

          <label class="block text-green-700 font-semibold mb-1">Reason <span class="text-red-500">*</span></label>
          <select v-model="reportReason" class="w-full border border-green-300 rounded shadow-sm mb-3 focus:border-green-600 focus:ring-2 focus:ring-green-200 transition">
            <option value="">Select a reason</option>
            <option value="Inappropriate content">Inappropriate content</option>
            <option value="Copyright violation">Copyright violation</option>
            <option value="Poor quality">Poor quality</option>
            <option value="Broken file">Broken file</option>
            <option value="Wrong category">Wrong category</option>
            <option value="Spam">Spam</option>
            <option value="Other">Other</option>
          </select>

          <label class="block text-green-700 font-semibold mb-1">Additional Details</label>
          <textarea v-model="reportDescription" rows="3" class="w-full border border-green-300 rounded shadow-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" placeholder="Any additional info..."></textarea>
          <div class="text-sm text-gray-500 mt-1">{{ reportDescription.length }}/1000 characters</div>
        </div>
        <div class="px-4 py-3 border-t flex justify-end gap-2 bg-white bg-opacity-80">
          <button class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-400" @click="closeReportModal">
            <font-awesome-icon icon="xmark-circle" /> Cancel
          </button>
          <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-400 disabled:opacity-50 flex items-center gap-2" @click="submitReport" :disabled="isSubmittingReport || !reportReason.trim()">
            <font-awesome-icon v-if="isSubmittingReport" icon="hourglass-half" />
            <font-awesome-icon v-else icon="flag" />
            <span v-if="isSubmittingReport">
              <svg class="inline w-4 h-4 animate-spin mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
              Submitting...
            </span>
            <span v-else>Submit Report</span>
          </button>
        </div>
      </div>
    </div>
  </teleport>
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
button:focus, a:focus, select:focus, textarea:focus {
  outline: 2px solid #34d399;
  outline-offset: 2px;
}
</style>
