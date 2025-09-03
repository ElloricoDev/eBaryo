<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { usePage, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faArrowLeft,
    faBookOpen,
    faUpRightFromSquare,
    faBookmark,
    faXmark,
    faFlag,
    faBook,
    faFile,
    faDownload,
    faUser,
    faBarcode,
    faCalendar,
    faBuilding,
    faLanguage,
    faTag,
    faCircle,
    faArrowUp,
    faStar,
    faCircleXmark,
    faInfoCircle,
    faFileText,
    faExclamationTriangle,
} from "@fortawesome/free-solid-svg-icons";
import Swal from "sweetalert2";
import StarRating from "@/Components/StarRating.vue";
import ReportModal from "@/Components/ReportModal.vue";

library.add(
    faArrowLeft,
    faBookOpen,
    faUpRightFromSquare,
    faBookmark,
    faXmark,
    faFlag,
    faBook,
    faFile,
    faDownload,
    faUser,
    faBarcode,
    faCalendar,
    faBuilding,
    faLanguage,
    faTag,
    faCircle,
    faArrowUp,
    faStar,
    faCircleXmark,
    faInfoCircle,
    faFileText,
    faExclamationTriangle,
);

const { props } = usePage();
const book = props.book;
const savedBookIds = ref(props.saved_books || []);
const isSaved = computed(() => savedBookIds.value.includes(book.id));
const showReader = ref(false);
const readerSection = ref(null);
const showScrollTop = ref(false);
const reviews = ref(props.book.reviews || []);
const averageRating = ref(props.book.average_rating || 0);
const reviewCount = ref(props.book.reviews_count || 0);
const newRating = ref(0);
const newReview = ref("");
const submittingReview = ref(false);
const user = props.auth?.user || null;
const isVerified = computed(() => user && user.email_verified_at);
const userReview = ref(null);
const editingReview = ref(false);

const saveBook = () => {
    if (isSaved.value) return;
    router.post(
        route("books.save", { id: book.id }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                if (!savedBookIds.value.includes(book.id))
                    savedBookIds.value.push(book.id);
                Swal.fire({
                    icon: "success",
                    title: "Book saved!",
                    text: `"${book.title}" has been saved to your library!`,
                    timer: 1500,
                    showConfirmButton: false,
                });
            },
            onError: (errors) => {
                console.error("Error saving book:", errors);
                Swal.fire({
                    icon: "error",
                    title: "Failed to save book",
                    text: "Please try again.",
                });
            },
        }
    );
};

const unsaveBook = () => {
    if (!isSaved.value) return;
    Swal.fire({
        title: "Unsave this book?",
        text: `Are you sure you want to remove "${book.title}" from your saved books?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Unsave",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                route("books.unsave", { id: book.id }),
                {},
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        const index = savedBookIds.value.indexOf(book.id);
                        if (index > -1) savedBookIds.value.splice(index, 1);
                        Swal.fire({
                            icon: "success",
                            title: "Book unsaved!",
                            text: `"${book.title}" has been removed.`,
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    },
                    onError: (errors) => {
                        console.error("Error unsaving book:", errors);
                        Swal.fire({
                            icon: "error",
                            title: "Failed to unsave book",
                            text: "Please try again.",
                        });
                    },
                }
            );
        }
    });
};

const showReportModal = ref(false);
const reportReason = ref("");
const reportDescription = ref("");
const isSubmittingReport = ref(false);

const reportBook = () => {
    if (!isVerified.value) {
        Swal.fire({
            icon: "warning",
            title: "Verify your email",
            text: "Please verify your email to report a book.",
        });
        return;
    }
    showReportModal.value = true;
};

const submitReview = async () => {
    if (!newRating.value) {
        Swal.fire({
            icon: "error",
            title: "Rating required",
            text: "Please select a rating before submitting.",
        });
        return;
    }

    submittingReview.value = true;

    router.post(
        route("books.reviews.store", { id: book.id }),
        {
            rating: newRating.value,
            review: newReview.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                fetchReviews();
                Swal.fire({
                    icon: "success",
                    title: userReview.value
                        ? "Review updated!"
                        : "Review submitted!",
                    timer: 1500,
                    showConfirmButton: false,
                });
                editingReview.value = false;
            },
            onError: (errors) => {
                Swal.fire({
                    icon: "error",
                    title: "Failed to submit review",
                    text: errors.review || errors.rating || "Please try again.",
                });
            },
            onFinish: () => {
                submittingReview.value = false;
            },
        }
    );
};

const closeReportModal = () => {
    showReportModal.value = false;
    reportReason.value = "";
    reportDescription.value = "";
};

const submitReport = async () => {
    if (!reportReason.value) {
        Swal.fire({
            icon: "error",
            title: "Reason required",
            text: "Please select a reason for reporting.",
        });
        return;
    }
    isSubmittingReport.value = true;
    router.post(
        route("books.report", { bookId: book.id }),
        {
            reason: reportReason.value,
            description: reportDescription.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: "success",
                    title: "Report submitted",
                    text: "Thank you for your feedback!",
                    timer: 1500,
                    showConfirmButton: false,
                });
                closeReportModal();
            },
            onError: (errors) => {
                Swal.fire({
                    icon: "error",
                    title: "Failed to submit report",
                    text: errors.reason || errors.description || "Please try again.",
                });
            },
            onFinish: () => {
                isSubmittingReport.value = false;
            },
        }
    );
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
};

const handleWindowScroll = () => {
    showScrollTop.value = window.scrollY > 200;
};

const fetchReviews = async () => {
    try {
        console.log('Fetching reviews for book:', book.id);
        const res = await fetch(`/books/${book.id}/reviews`);
        if (res.ok) {
            const data = await res.json();
            console.log('Reviews data received:', data);
            reviews.value = data.reviews || [];
            averageRating.value = data.average_rating || 0;
            reviewCount.value = data.review_count || 0;
            userReview.value = data.user_review || null;
            
            console.log('Updated reviews:', reviews.value.length, 'Average rating:', averageRating.value, 'Review count:', reviewCount.value);
            
            if (userReview.value) {
                newRating.value = userReview.value.rating;
                newReview.value = userReview.value.review;
                editingReview.value = false;
            } else {
                newRating.value = 0;
                newReview.value = "";
                editingReview.value = true;
            }
        } else {
            console.error('Failed to fetch reviews:', res.status, res.statusText);
        }
    } catch (error) {
        console.error('Error fetching reviews:', error);
    }
};

onMounted(() => {
    window.addEventListener("scroll", handleWindowScroll);
    fetchReviews();
    const el = document.getElementById("book-content");
    if (el) {
        el.scrollIntoView({ behavior: "auto" });
    }
});
onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleWindowScroll);
});

const fileType = computed(() => {
    if (!book.ebook_file) return null;
    return book.ebook_file.split(".").pop()?.toLowerCase();
});

const isPdf = computed(() => fileType.value === "pdf");
const isEpub = computed(() => fileType.value === "epub");

const goBack = () => {
    window.history.length > 1 ? window.history.back() : router.visit("/home");
};

const handleReadClick = () => {
    if (isPdf.value) {
        router.visit(route("books.pdfReader", { id: book.id }), {
            preserveScroll: true,
        });
    } else if (isEpub.value) {
        router.visit(route("books.epubReader", { id: book.id }), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="book.title" />
    <UserLayout>
        <!-- Back Button -->
        <div class="mb-6">
            <button @click="goBack" class="text-green-700 font-bold flex items-center gap-2 hover:text-green-800 transition-colors">
                <font-awesome-icon icon="arrow-left" class="text-lg" />
                Back to Library
            </button>
        </div>

        <!-- Book Details Container -->
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 hover:shadow-md transition-shadow duration-300">
                <!-- Book Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4 flex items-center justify-center gap-3">
                        <font-awesome-icon icon="book" class="text-green-600" />
                        {{ book.title }}
                    </h1>
                    <p class="text-xl text-slate-600">by {{ book.author }}</p>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <!-- Left Column - Book Cover & Actions -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-24">
                            <!-- Book Cover -->
                            <div class="text-center mb-8">
                                <img :src="book.cover_image || '/images/default.svg'" :alt="book.title"
                                    class="book-details-cover mx-auto shadow-2xl" />
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-4">
                                <!-- Read Button -->
                                <button v-if="book.ebook_file && isVerified" @click="handleReadClick"
                                    class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold px-6 py-4 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-green-200">
                                    <font-awesome-icon icon="book-open" class="h-5 w-5" />
                                    <span>
                                        <template v-if="book.progress && book.progress > 0 && book.progress < 1">
                                            Continue Reading
                                        </template>
                                        <template v-else>Start Reading</template>
                                    </span>
                                </button>

                                <!-- Email Verification Required -->
                                <button v-if="book.ebook_file && user && !isVerified" disabled
                                    class="w-full flex items-center justify-center gap-3 bg-slate-300 text-slate-500 font-semibold px-6 py-4 rounded-2xl cursor-not-allowed">
                                    <font-awesome-icon icon="book-open" class="h-5 w-5" />
                                    Verify your email to read
                                </button>

                                <!-- Save/Unsave Button -->
                                <button v-if="!isSaved" @click="saveBook"
                                    class="w-full flex items-center justify-center gap-3 border-2 border-green-600 text-green-700 hover:bg-green-50 focus:bg-green-100 font-semibold px-6 py-4 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-green-200">
                                    <font-awesome-icon icon="bookmark" class="h-5 w-5" />
                                    Save to Library
                                </button>
                                <button v-else @click="unsaveBook"
                                    class="w-full flex items-center justify-center gap-3 border-2 border-amber-500 text-amber-600 hover:bg-amber-50 focus:bg-amber-100 font-semibold px-6 py-4 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-amber-200">
                                    <font-awesome-icon icon="bookmark" class="h-5 w-5" />
                                    <font-awesome-icon icon="xmark" class="h-4 w-4" />
                                    Remove from Library
                                </button>

                                <!-- Report Button -->
                                <button @click="reportBook"
                                    class="w-full flex items-center justify-center gap-3 border-2 border-red-500 text-red-600 hover:bg-red-50 focus:bg-red-100 font-semibold px-6 py-4 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-red-200">
                                    <font-awesome-icon icon="flag" class="h-5 w-5" />
                                    Report Issue
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Book Information -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Book Details -->
                        <div class="bg-slate-50 rounded-2xl p-6">
                            <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <font-awesome-icon icon="info-circle" class="text-green-600" />
                                Book Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-green-600 font-semibold flex items-center gap-2">
                                        <font-awesome-icon icon="user" />
                                        Author:
                                    </span>
                                    <span class="text-slate-700">{{ book.author }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-green-600 font-semibold flex items-center gap-2">
                                        <font-awesome-icon icon="barcode" />
                                        ISBN:
                                    </span>
                                    <span class="text-slate-700">{{ book.isbn || "N/A" }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-green-600 font-semibold flex items-center gap-2">
                                        <font-awesome-icon icon="calendar" />
                                        Published:
                                    </span>
                                    <span class="text-slate-700">{{ book.published_year || "N/A" }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-green-600 font-semibold flex items-center gap-2">
                                        <font-awesome-icon icon="building" />
                                        Publisher:
                                    </span>
                                    <span class="text-slate-700">{{ book.publisher || "N/A" }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-green-600 font-semibold flex items-center gap-2">
                                        <font-awesome-icon icon="language" />
                                        Language:
                                    </span>
                                    <span class="text-slate-700">{{ book.language || "N/A" }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-green-600 font-semibold flex items-center gap-2">
                                        <font-awesome-icon icon="tag" />
                                        Category:
                                    </span>
                                    <span class="text-slate-700">{{ book.category?.name || "N/A" }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-green-600 font-semibold flex items-center gap-2">
                                        <font-awesome-icon icon="circle" :class="book.status === 'active' ? 'text-green-500' : 'text-red-500'" />
                                        Status:
                                    </span>
                                    <span :class="book.status === 'active' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
                                        {{ book.status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Book Description -->
                        <div class="bg-slate-50 rounded-2xl p-6">
                            <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <font-awesome-icon icon="file-text" class="text-green-600" />
                                Description
                            </h3>
                            <div class="prose max-w-none text-slate-700 leading-relaxed" v-html="book.description || '<em>No description provided.</em>'"></div>
                        </div>

                        <!-- File Availability -->
                        <div v-if="!book.ebook_file" class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
                            <font-awesome-icon icon="exclamation-triangle" class="text-amber-600 text-2xl mb-3" />
                            <p class="text-amber-800 font-medium">No eBook file available for this book.</p>
                        </div>

                        <!-- Metadata -->
                        <div class="text-sm text-slate-500 space-y-1">
                            <p>Added: {{ new Date(book.created_at).toLocaleString() }}</p>
                            <p>Last Updated: {{ new Date(book.updated_at).toLocaleString() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ratings & Reviews Section -->
            <div class="mt-12 bg-white rounded-3xl shadow-sm border border-slate-200 p-8 hover:shadow-md transition-shadow duration-300">
                <h3 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                    <font-awesome-icon icon="star" class="text-yellow-400" />
                    Ratings & Reviews
                </h3>

                <!-- Overall Rating -->
                <div class="mb-8 p-6 bg-slate-50 rounded-2xl">
                    <div class="flex items-center gap-4">
                        <StarRating :rating="averageRating" :highlight="true" />
                        <div>
                            <span class="text-2xl font-bold text-slate-700">{{ averageRating || 0 }}</span>
                            <span class="text-slate-500 ml-2">/ 5</span>
                            <div class="text-sm text-slate-600">({{ reviewCount || 0 }} review{{ reviewCount !== 1 ? 's' : '' }})</div>
                        </div>
                    </div>
                </div>

                <!-- Reviews List -->
                <div v-if="reviews.length" class="space-y-6 mb-8">
                    <div v-for="review in reviews" :key="review.id" class="bg-slate-50 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="font-semibold text-slate-700">{{ review.user?.user_name || "User" }}</span>
                            <div class="flex items-center gap-1">
                                <font-awesome-icon v-for="n in 5" :key="n" icon="star" :class="n <= review.rating ? 'text-yellow-400' : 'text-slate-300'" />
                            </div>
                            <span class="text-xs text-slate-400 ml-auto">
                                {{ new Date(review.created_at).toLocaleDateString() }}
                            </span>
                        </div>
                        <div class="text-slate-700">{{ review.review }}</div>
                    </div>
                </div>

                <!-- No Reviews Message -->
                <div v-else class="text-center py-12 text-slate-500">
                    <font-awesome-icon icon="star" class="text-4xl mb-4 text-slate-300" />
                    <p class="text-lg">No reviews yet. Be the first to review this book!</p>
                </div>

                <!-- Review Form -->
                <div v-if="isVerified" class="bg-slate-50 rounded-2xl p-6">
                    <h4 class="font-semibold mb-4 text-lg text-slate-800">
                        {{ userReview && !editingReview ? "Your Review" : userReview ? "Edit Your Review" : "Leave a Review" }}
                    </h4>
                    
                    <div v-if="userReview && !editingReview">
                        <div class="flex items-center gap-2 mb-3">
                            <span v-for="n in 5" :key="n">
                                <font-awesome-icon icon="star" :class="n <= userReview.rating ? 'text-yellow-400' : 'text-slate-300'" size="lg" />
                            </span>
                            <span class="ml-2 text-slate-600">{{ userReview.rating }} / 5</span>
                        </div>
                        <div class="mb-4 text-slate-700">{{ userReview.review }}</div>
                        <button @click="editingReview = true"
                            class="bg-green-600 text-white px-6 py-3 rounded-2xl shadow-sm hover:bg-green-700 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-green-200">
                            Edit Review
                        </button>
                    </div>
                    
                    <div v-else>
                        <div class="flex items-center gap-2 mb-4">
                            <span v-for="n in 5" :key="n" @click="newRating = n" class="cursor-pointer hover:scale-110 transition-transform">
                                <font-awesome-icon icon="star" :class="n <= newRating ? 'text-yellow-400' : 'text-slate-300'" size="lg" />
                            </span>
                            <span class="ml-3 text-slate-600 font-medium">
                                {{ newRating ? `${newRating} / 5` : "Select rating" }}
                            </span>
                        </div>
                        <textarea v-model="newReview" rows="4"
                            class="w-full border-2 border-slate-200 rounded-2xl shadow-sm focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 p-4 mb-4 resize-none"
                            placeholder="Write your review (optional)..."></textarea>
                        <button @click="submitReview" :disabled="submittingReview"
                            class="bg-green-600 text-white px-8 py-3 rounded-2xl shadow-sm hover:bg-green-700 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-green-200 disabled:opacity-50">
                            {{ submittingReview ? (userReview ? "Updating..." : "Submitting...") : (userReview ? "Update Review" : "Submit Review") }}
                        </button>
                    </div>
                </div>

                <!-- Email Verification Required Message -->
                <div v-else-if="user" class="text-center py-8 bg-amber-50 border border-amber-200 rounded-2xl">
                    <font-awesome-icon icon="exclamation-triangle" class="text-amber-600 text-2xl mb-3" />
                    <p class="text-amber-800">Please verify your email to leave a review.</p>
                </div>

                <!-- Login Required Message -->
                <div v-else class="text-center py-8 bg-slate-50 border border-slate-200 rounded-2xl">
                    <font-awesome-icon icon="user" class="text-slate-600 text-2xl mb-3" />
                    <p class="text-slate-700">Please log in to leave a review.</p>
                </div>
            </div>
        </div>

        <!-- Scroll to Top Button -->
        <button v-show="showScrollTop" @click="scrollToTop"
            class="fixed bottom-8 right-8 bg-green-600 text-white rounded-full p-4 shadow-lg z-50 hover:bg-green-700 transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-green-200">
            <font-awesome-icon icon="arrow-up" class="h-5 w-5" />
        </button>

        <!-- Report Modal -->
        <ReportModal :show="showReportModal" :book="book" :reason="reportReason" :description="reportDescription"
            :loading="isSubmittingReport" @close="closeReportModal" @submit="submitReport"
            @update:reason="(val) => (reportReason = val)" @update:description="(val) => (reportDescription = val)" />
    </UserLayout>
</template>

<style scoped>
@keyframes gradient-move {
    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}

.animate-gradient-move {
    background-size: 200% 200%;
    animation: gradient-move 8s ease-in-out infinite;
}

@keyframes fade-in {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.animate-fade-in {
    animation: fade-in 1s both;
}

@keyframes pulse-cta {

    0%,
    100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
    }

    50% {
        box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
    }
}

.animate-pulse-cta {
    animation: pulse-cta 2s infinite;
}

input:focus,
button:focus,
a:focus {
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
