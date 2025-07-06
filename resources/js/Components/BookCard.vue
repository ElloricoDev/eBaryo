<script setup>
import { router, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Swal from 'sweetalert2';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBook, faEye, faBookmark, faFlag, faXmarkCircle, faHourglassHalf, faXmark } from '@fortawesome/free-solid-svg-icons'

library.add(faBook, faEye, faBookmark, faFlag, faXmarkCircle, faHourglassHalf, faXmark)

const props = defineProps({
  book: Object,
  isSaved: Boolean
});

const emit = defineEmits(['save', 'unsave', 'report']);

const showReportModal = ref(false);
const reportReason = ref('');
const reportDescription = ref('');
const isSubmittingReport = ref(false);

const isBookSaved = computed(() => props.isSaved);
const avatarUrl = computed(() => props.book.cover_image || '/images/default.svg');

const goToRead = () => {
  const params = { id: props.book.id };
  if (props.book.from) params.from = props.book.from;
  router.visit(route('books.read', params), { preserveScroll: true });
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

const handleReport = () => showReportModal.value = true;

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
    class="relative animate-fade-in bg-white bg-opacity-90 border border-green-200 rounded-2xl shadow-md hover:shadow-2xl hover:scale-[1.025] transition-all duration-300 w-full max-w-[260px] min-w-[200px] flex flex-col overflow-hidden group"
    tabindex="0"
  >
    <!-- Animated Gradient Background -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-50 via-white to-green-100 animate-gradient-move"></div>
    <div class="bg-gradient-to-b from-green-50 to-white p-4 pb-0 flex flex-col items-center">
      <img
        :src="avatarUrl"
        class="w-[120px] h-[170px] object-cover rounded-xl shadow group-hover:scale-105 transition-transform duration-300 border border-green-100 bg-white"
        alt="Book Cover"
      />
    </div>
    <div class="flex-1 flex flex-col px-5 pt-3 pb-2">
      <h5 class="text-green-800 font-extrabold text-xl mb-1 truncate text-center">{{ book.title }}</h5>
      <p class="text-xs text-center mb-2">
        <span class="text-green-600 font-medium">Author:</span>
        <span class="text-gray-500">{{ book.author || 'Unknown' }}</span>
      </p>
      <div class="border-t border-green-100 my-2"></div>
      <div class="mt-auto flex flex-col gap-2">
        <div class="flex gap-2 justify-center">
          <button
            class="flex-1 flex items-center justify-center gap-1 px-3 py-1 bg-green-600 text-white rounded-lg font-semibold shadow hover:bg-green-700 focus:bg-green-800 animate-pulse-cta focus:outline-none focus:ring-2 focus:ring-green-400 transition-colors duration-200"
            @click="goToRead"
          >
            <font-awesome-icon icon="book" class="text-white text-lg" />
            <span>Read</span>
          </button>
          <Link
            :href="route('books.view', { id: book.id, from: book.from || 'books' })"
            class="flex-1 flex items-center justify-center gap-1 px-3 py-1 border border-green-600 text-green-700 rounded-lg font-semibold shadow hover:bg-green-50 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-400 transition-colors duration-200"
          >
            <font-awesome-icon icon="eye" class="text-green-700 text-lg" />
            <span>View</span>
          </Link>
        </div>
        <div class="flex gap-2 justify-center">
          <button
            v-if="!isBookSaved"
            class="flex-1 flex items-center justify-center gap-1 px-3 py-1 border border-green-600 text-green-700 rounded-lg font-semibold shadow hover:bg-green-50 focus:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-400 transition-colors duration-200"
            @click="handleSave"
          >
            <font-awesome-icon icon="bookmark" class="text-green-700 text-lg" />
            <span>Save</span>
          </button>
          <button
            v-else
            class="flex-1 flex items-center justify-center gap-1 px-3 py-1 border border-yellow-500 text-yellow-600 rounded-lg font-semibold shadow hover:bg-yellow-50 focus:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-colors duration-200"
            @click="handleUnsave"
          >
            <font-awesome-icon icon="xmark" class="text-yellow-600 text-lg" />
            <span>Unsave</span>
          </button>
          <button
            class="flex-1 flex items-center justify-center gap-1 px-3 py-1 border border-red-500 text-red-600 rounded-lg font-semibold shadow hover:bg-red-50 focus:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-400 transition-colors duration-200"
            @click="handleReport"
          >
            <font-awesome-icon icon="flag" class="text-red-600 text-lg" />
            <span>Report</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="showReportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="relative animate-fade-in bg-white bg-opacity-95 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
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
