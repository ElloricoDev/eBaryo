<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { usePage, Head, Link, router } from '@inertiajs/vue3';
import EpubReader from '@/Components/EpubReader.vue';
import PdfReader from '@/Components/PdfReader.vue';
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowLeft, faBookOpen, faUpRightFromSquare, faBookmark, faXmark, faFlag, faBook, faFile, faDownload, faUser, faBarcode, faCalendar, faBuilding, faLanguage, faTag, faCircle, faArrowUp, faStar } from '@fortawesome/free-solid-svg-icons'
import Swal from 'sweetalert2';

library.add(faArrowLeft, faBookOpen, faUpRightFromSquare, faBookmark, faXmark, faFlag, faBook, faFile, faDownload, faUser, faBarcode, faCalendar, faBuilding, faLanguage, faTag, faCircle, faArrowUp, faStar)

defineOptions({ layout: UserLayout });
const { props } = usePage();
const book = props.book;
const savedBookIds = ref(props.saved_books || []);
const isSaved = computed(() => savedBookIds.value.includes(book.id));
const showReader = ref(false);
const readerSection = ref(null);
const showScrollTop = ref(false);
const reviews = ref([]);
const averageRating = ref(0);
const reviewCount = ref(0);
const newRating = ref(0);
const newReview = ref('');
const submittingReview = ref(false);
const user = props.auth?.user || null;
const isVerified = computed(() => user && user.email_verified_at);
const userReview = ref(null);
const editingReview = ref(false);

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
      Swal.fire({ icon: 'success', title: 'Book saved!', text: `"${book.title}" has been saved to your library!`, timer: 1500, showConfirmButton: false });
    },
    onError: (errors) => {
      console.error('Error saving book:', errors);
      Swal.fire({ icon: 'error', title: 'Failed to save book', text: 'Please try again.' });
    }
  });
}

function unsaveBook() {
  if (!isSaved.value) return;
  Swal.fire({
    title: 'Unsave this book?',
    text: `Are you sure you want to remove "${book.title}" from your saved books?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Unsave',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('books.unsave', { id: book.id }), {}, {
        preserveScroll: true,
        onSuccess: () => {
          const index = savedBookIds.value.indexOf(book.id);
          if (index > -1) savedBookIds.value.splice(index, 1);
          Swal.fire({ icon: 'success', title: 'Book unsaved!', text: `"${book.title}" has been removed.`, timer: 1500, showConfirmButton: false });
        },
        onError: (errors) => {
          console.error('Error unsaving book:', errors);
          Swal.fire({ icon: 'error', title: 'Failed to unsave book', text: 'Please try again.' });
        }
      });
    }
  });
}

const showReportModal = ref(false);
const reportReason = ref('');
const reportDescription = ref('');
const isSubmittingReport = ref(false);

function reportBook() {
  if (!isVerified.value) {
    Swal.fire({ icon: 'warning', title: 'Verify your email', text: 'Please verify your email to report a book.' });
    return;
  }
  showReportModal.value = true;
}

async function submitReport() {
  if (!reportReason.value.trim()) {
    Swal.fire({ icon: 'error', title: 'Reason Required', text: 'Please provide a reason.' });
    return;
  }
  isSubmittingReport.value = true;
  try {
    const res = await fetch(`/books/${book.id}/report`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
      },
      body: JSON.stringify({ reason: reportReason.value, description: reportDescription.value }),
    });
    isSubmittingReport.value = false;
    if (res.ok) {
      showReportModal.value = false;
      reportReason.value = '';
      reportDescription.value = '';
      Swal.fire({ icon: 'success', title: 'Report Submitted', text: 'Thank you for your report.', timer: 2000, showConfirmButton: false });
    } else {
      const err = await res.json();
      Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Failed to submit. Try again.' });
    }
  } catch (e) {
    isSubmittingReport.value = false;
    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to submit. Try again.' });
  }
}

function closeReportModal() {
  showReportModal.value = false;
  reportReason.value = '';
  reportDescription.value = '';
}

function closeBook() {
  showReader.value = false;
  nextTick(() => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleWindowScroll() {
  showScrollTop.value = window.scrollY > 200;
}

async function fetchReviews() {
  const res = await fetch(`/books/${book.id}/reviews`);
  if (res.ok) {
    const data = await res.json();
    reviews.value = data.reviews;
    averageRating.value = data.average_rating;
    reviewCount.value = data.review_count;
    userReview.value = data.user_review;
    if (userReview.value) {
      newRating.value = userReview.value.rating;
      newReview.value = userReview.value.review;
      editingReview.value = false;
    } else {
      newRating.value = 0;
      newReview.value = '';
      editingReview.value = true;
    }
  }
}

async function submitReview() {
  if (!newRating.value) {
    Swal.fire({ icon: 'error', title: 'Rating required', text: 'Please select a rating before submitting.' });
    return;
  }
  submittingReview.value = true;
  router.post(route('books.reviews.store', { id: book.id }), {
    rating: newRating.value,
    review: newReview.value
  }, {
    preserveScroll: true,
    onSuccess: () => {
      fetchReviews();
      Swal.fire({ icon: 'success', title: userReview.value ? 'Review updated!' : 'Review submitted!', timer: 1500, showConfirmButton: false });
      editingReview.value = false;
    },
    onError: (errors) => {
      Swal.fire({ icon: 'error', title: 'Failed to submit review', text: errors.review || errors.rating || 'Please try again.' });
    },
    onFinish: () => {
      submittingReview.value = false;
    }
  });
}

onMounted(() => {
  window.addEventListener('scroll', handleWindowScroll);
  fetchReviews();
  const el = document.getElementById('book-content');
  if (el) {
    el.scrollIntoView({ behavior: 'auto' });
  }
});
onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleWindowScroll);
});

const fileType = computed(() => {
  if (!book.ebook_file) return null;
  return book.ebook_file.split('.').pop()?.toLowerCase();
});

const isPdf = computed(() => fileType.value === 'pdf');
const isEpub = computed(() => fileType.value === 'epub');

const params = new URLSearchParams(window.location.search);
const returnTo = params.get('returnTo');

const routeMap = {
  home: 'home',
  saved: 'books.saved',
  feedback: 'feedback.create',
  profile: 'user.profile.index',
  // add more as needed
};

function goBack() {
  if (returnTo && routeMap[returnTo]) {
    router.visit(route(routeMap[returnTo]), {
      preserveScroll: true,
      onFinish: () => {
        const scrollPosition = sessionStorage.getItem('scrollPosition');
        if (scrollPosition) {
          window.scrollTo({ top: parseInt(scrollPosition, 10), behavior: 'auto' });
          sessionStorage.removeItem('scrollPosition');
        }
      }
    });
  } else {
    window.history.back();
  }
}
</script>

<template>
  <Head :title="book.title" />
  <div class="relative min-h-screen max-w-6xl mx-auto py-6 px-4 overflow-x-hidden">
    <!-- Animated Gradient Background -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 via-green-50 to-white animate-gradient-move"></div>
    <div class="animate-fade-in bg-white bg-opacity-90 rounded-2xl shadow-xl p-6">
      <div class="mb-4">
        <button @click="goBack" class="block sm:hidden mb-4 text-green-700 font-bold flex items-center gap-2">
          <font-awesome-icon icon="arrow-left" class="text-lg" />
          Back
        </button>
        <Link :href="getBackRoute()" class="hidden sm:inline-flex items-center gap-2 text-green-700 border border-green-600 hover:bg-green-50 focus:bg-green-100 transition px-4 py-2 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-green-400">
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
            <button v-if="book.ebook_file && isVerified" @click="handleReadNow" class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 focus:bg-green-800 text-white font-semibold px-4 py-2 rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-green-400 animate-pulse-cta">
              <font-awesome-icon icon="book-open" class="h-5 w-5" />
              {{ showReader ? 'Hide Reader' : 'Read Now' }}
            </button>
            <button v-if="book.ebook_file && user && !isVerified" disabled class="w-full flex items-center justify-center gap-2 bg-gray-300 text-gray-500 font-semibold px-4 py-2 rounded-lg shadow cursor-not-allowed">
              <font-awesome-icon icon="book-open" class="h-5 w-5" />
              Verify your email to read
            </button>

            <Link
              v-if="book.ebook_file && isVerified"
              :href="route('books.read', { id: book.id, from: 'details' })"
              class="w-full flex items-center justify-center gap-2 border border-green-600 text-green-700 hover:bg-green-50 focus:bg-green-100 font-semibold px-4 py-2 rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-green-400"
            >
              <font-awesome-icon icon="up-right-from-square" class="h-5 w-5" />
              Open in Full Screen
            </Link>
            <button v-if="book.ebook_file && user && !isVerified" disabled class="w-full flex items-center justify-center gap-2 border border-gray-300 text-gray-500 font-semibold px-4 py-2 rounded-lg shadow cursor-not-allowed">
              <font-awesome-icon icon="up-right-from-square" class="h-5 w-5" />
              Verify your email to open
            </button>

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

        <div class="md:col-span-2" id="book-content">
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
        <!-- Close Book Button -->
        <button @click="closeBook" class="mb-4 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded shadow flex items-center gap-2">
          <font-awesome-icon icon="xmark" class="h-5 w-5" />
          Close Book
        </button>
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

      <!-- Ratings & Reviews Section -->
      <div class="mt-8">
        <h3 class="text-xl font-bold text-green-700 mb-2 flex items-center gap-2">
          <font-awesome-icon icon="star" class="text-yellow-400" />
          Ratings & Reviews
        </h3>
        <div class="flex items-center gap-4 mb-2">
          <span class="text-2xl font-bold text-yellow-500">{{ averageRating || '0.0' }}</span>
          <span class="text-gray-600">/ 5.0</span>
          <span class="text-gray-500">({{ reviewCount }} review{{ reviewCount === 1 ? '' : 's' }})</span>
        </div>
        <!-- Review List -->
        <div v-if="reviews.length" class="space-y-4 mb-6">
          <div v-for="review in reviews" :key="review.id" class="bg-green-50 rounded-lg p-4 shadow">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-semibold text-green-700">{{ review.user?.user_name || 'User' }}</span>
              <span class="flex items-center gap-1">
                <font-awesome-icon v-for="n in 5" :key="n" icon="star" :class="n <= review.rating ? 'text-yellow-400' : 'text-gray-300'" />
              </span>
              <span class="text-xs text-gray-400 ml-auto">
                Posted: {{ new Date(review.created_at).toLocaleDateString() }}
                <span v-if="review.updated_at && review.updated_at !== review.created_at"> | Updated: {{ new Date(review.updated_at).toLocaleDateString() }}</span>
              </span>
            </div>
            <div class="text-gray-700">{{ review.review }}</div>
          </div>
        </div>
        <div v-else class="text-gray-400 mb-6">No reviews yet. Be the first to review this book!</div>
        <!-- Review Form -->
        <div v-if="isVerified" class="bg-white rounded-lg p-4 shadow border border-green-100">
          <h4 class="font-semibold mb-2">{{ userReview && !editingReview ? 'Your Review' : (userReview ? 'Edit Your Review' : 'Leave a Review') }}</h4>
          <div v-if="userReview && !editingReview">
            <div class="flex items-center gap-2 mb-2">
              <span v-for="n in 5" :key="n" class="cursor-pointer">
                <font-awesome-icon icon="star" :class="n <= userReview.rating ? 'text-yellow-400' : 'text-gray-300'" size="lg" />
              </span>
              <span class="ml-2 text-gray-600">{{ userReview.rating }} / 5</span>
            </div>
            <div class="mb-2 text-gray-700">{{ userReview.review }}</div>
            <button @click="editingReview = true" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 focus:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-400">Edit Review</button>
          </div>
          <div v-else>
            <div class="flex items-center gap-2 mb-2">
              <span v-for="n in 5" :key="n" @click="newRating = n" class="cursor-pointer">
                <font-awesome-icon icon="star" :class="n <= newRating ? 'text-yellow-400' : 'text-gray-300'" size="lg" />
              </span>
              <span class="ml-2 text-gray-600">{{ newRating ? `${newRating} / 5` : 'Select rating' }}</span>
            </div>
            <textarea v-model="newReview" rows="3" class="w-full border border-green-300 rounded shadow-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition mb-2" placeholder="Write your review (optional)..."></textarea>
            <button @click="submitReview" :disabled="submittingReview" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 focus:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-400 disabled:opacity-50">
              {{ submittingReview ? (userReview ? 'Updating...' : 'Submitting...') : (userReview ? 'Update Review' : 'Submit Review') }}
            </button>
          </div>
        </div>
        <div v-else-if="user" class="text-yellow-600">Please verify your email to leave a review.</div>
        <div v-else class="text-gray-500">Please log in to leave a review.</div>
      </div>
      <!-- Scroll to Top Floating Button -->
      <button
        v-show="showScrollTop"
        @click="scrollToTop"
        class="fixed bottom-6 right-6 bg-green-600 text-white rounded-full p-3 shadow-lg z-50"
        style="transition: opacity 0.3s;"
      >
        <font-awesome-icon icon="arrow-up" class="h-5 w-5" />
      </button>
    </div>
  </div>

  <!-- Report Modal -->
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
input:focus, button:focus, a:focus {
  outline: 2px solid #34d399;
  outline-offset: 2px;
}
</style>