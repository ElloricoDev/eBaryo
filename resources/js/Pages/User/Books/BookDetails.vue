<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { usePage, router } from '@inertiajs/vue3';
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowLeft, faBookOpen, faUpRightFromSquare, faBookmark, faXmark, faFlag, faBook, faFile, faDownload, faUser, faBarcode, faCalendar, faBuilding, faLanguage, faTag, faCircle, faArrowUp, faStar, faCircleXmark } from '@fortawesome/free-solid-svg-icons'
import Swal from 'sweetalert2';
import StarRating from '@/Components/StarRating.vue';
import ReportModal from '@/Components/ReportModal.vue';

library.add(faArrowLeft, faBookOpen, faUpRightFromSquare, faBookmark, faXmark, faFlag, faBook, faFile, faDownload, faUser, faBarcode, faCalendar, faBuilding, faLanguage, faTag, faCircle, faArrowUp, faStar, faCircleXmark)

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





const saveBook = () => {
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
};

const unsaveBook = () => {
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
};

const showReportModal = ref(false);
const reportReason = ref('');
const reportDescription = ref('');
const isSubmittingReport = ref(false);

const reportBook = () => {
  if (!isVerified.value) {
    Swal.fire({ icon: 'warning', title: 'Verify your email', text: 'Please verify your email to report a book.' });
    return;
  }
  showReportModal.value = true;
};

const submitReview = async () => {
  if (!newRating.value) {
    Swal.fire({
      icon: 'error',
      title: 'Rating required',
      text: 'Please select a rating before submitting.'
    });
    return;
  }

  submittingReview.value = true;

  router.post(
    route('books.reviews.store', { id: book.id }),
    {
      rating: newRating.value,
      review: newReview.value
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        fetchReviews();
        Swal.fire({
          icon: 'success',
          title: userReview.value ? 'Review updated!' : 'Review submitted!',
          timer: 1500,
          showConfirmButton: false
        });
        editingReview.value = false;
      },
      onError: (errors) => {
        Swal.fire({
          icon: 'error',
          title: 'Failed to submit review',
          text: errors.review || errors.rating || 'Please try again.'
        });
      },
      onFinish: () => {
        submittingReview.value = false;
      }
    }
  );
};


const closeReportModal = () => {
  showReportModal.value = false;
  reportReason.value = '';
  reportDescription.value = '';
};

const submitReport = async () => {
  if (!reportReason.value) {
    Swal.fire({
      icon: 'error',
      title: 'Reason required',
      text: 'Please select a reason for reporting.'
    });
    return;
  }
  isSubmittingReport.value = true;
  try {
    const response = await fetch(`/books/${book.id}/report`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        reason: reportReason.value,
        description: reportDescription.value
      })
    });
    if (response.ok) {
      Swal.fire({
        icon: 'success',
        title: 'Report submitted',
        text: 'Thank you for your feedback!',
        timer: 1500,
        showConfirmButton: false
      });
      closeReportModal();
    } else {
      const data = await response.json();
      Swal.fire({
        icon: 'error',
        title: 'Failed to submit report',
        text: data.message || 'Please try again.'
      });
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Failed to submit report',
      text: 'Please try again.'
    });
  } finally {
    isSubmittingReport.value = false;
  }
};



const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const handleWindowScroll = () => {
  showScrollTop.value = window.scrollY > 200;
};

const fetchReviews = async () => {
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
};

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



const goBack = () => {
  window.history.length > 1
    ? window.history.back()
    : router.visit('/home');
}
// Remove goToEpuReader
// const goToEpuReader = () => {
//   router.visit(route('books.read', { id: book.id, from: 'details' }), {
//     preserveScroll: true
//   });
// };

const handleReadClick = () => {
  if (isPdf.value) {
    router.visit(route('books.pdfReader', { id: book.id }), { preserveScroll: true });
  } else if (isEpub.value) {
    router.visit(route('books.epubReader', { id: book.id }), { preserveScroll: true });
  }
};

</script>

<template>
  <Head :title="book.title" />
  <div class="relative min-h-screen  mx-auto  px-4 overflow-x-hidden">
    <!-- Animated Gradient Background -->
    <div class="animate-fade-in bg-opacity-90 rounded-2xl shadow-xl p-6">
      <div class="mb-4">
        <button @click="goBack" class="block mb-4 text-green-700 font-bold flex items-center gap-2">
          <font-awesome-icon icon="arrow-left" class="text-lg" />
          Back
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <img
            :src="book.cover_image || '/images/default.svg'"
            :alt="book.title"
            class="book-details-cover mb-4 shadow"
          />

          <div class="space-y-2">
            <button v-if="book.ebook_file && isVerified" @click="handleReadClick" class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 focus:bg-green-800 text-white font-semibold px-4 py-2 rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-green-400 animate-pulse-cta">
              <font-awesome-icon icon="book-open" class="h-5 w-5" />
              <span>
                <template v-if="book.progress && book.progress > 0 && book.progress < 1">Continue Reading</template>
                <template v-else>Start Reading</template>
              </span>
            </button>
            <button v-if="book.ebook_file && user && !isVerified" disabled class="w-full flex items-center justify-center gap-2 bg-gray-300 text-gray-500 font-semibold px-4 py-2 rounded-lg shadow cursor-not-allowed">
              <font-awesome-icon icon="book-open" class="h-5 w-5" />
              Verify your email to read
            </button>
            <!-- Removed Open in Full Screen Link -->
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

      <!-- Ratings & Reviews Section -->
      <div class="mt-8">
        <h3 class="text-xl font-bold text-green-700 mb-2 flex items-center gap-2">
          <font-awesome-icon icon="star" class="text-yellow-400" />
          Ratings & Reviews
        </h3>
        <div v-if="book.average_rating && book.reviews_count > 0">
          <StarRating :rating="book.average_rating" :highlight="true" />
          <span class="text-sm text-gray-700 font-semibold ml-1">{{ book.average_rating }}</span>
          <span class="text-xs text-gray-500 ml-1">({{ book.reviews_count }} review{{ book.reviews_count > 1 ? 's' : '' }})</span>
        </div>
        <div v-else>
          <span class="text-xs text-gray-400">No ratings yet</span>
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

  <ReportModal
    :show="showReportModal"
    :book="book"
    :reason="reportReason"
    :description="reportDescription"
    :loading="isSubmittingReport"
    @close="closeReportModal"
    @submit="submitReport"
    @update:reason="val => reportReason = val"
    @update:description="val => reportDescription = val"
  />
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
.book-details-cover {
  width: 250px;
  height: auto;
  object-fit: contain;
  border-radius: 0.75rem;
  border: 2px solid #16a34a;
  background: #f0fdf4;
  display: block;
  margin-left: auto;
  margin-right: auto;
}
</style>