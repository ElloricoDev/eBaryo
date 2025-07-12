<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { usePage, router, Head, Link } from "@inertiajs/vue3";
import BookSection from "@/Components/BookSection.vue";
import ContinueReadingSection from "@/Components/ContinueReadingSection.vue";
import { ref, onMounted, onUnmounted, computed } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faPlayCircle,
    faSearch,
    faHome,
    faStar,
    faFire,
    faChartLine,
    faTrophy,
    faThumbsUp,
    faBookOpen,
    faBookmark,
    faUser,
    faCommentDots,
    faEye,
    faChevronLeft,
    faChevronRight,
} from "@fortawesome/free-solid-svg-icons";
library.add(
    faPlayCircle,
    faSearch,
    faHome,
    faStar,
    faFire,
    faChartLine,
    faTrophy,
    faThumbsUp,
    faBookOpen,
    faBookmark,
    faUser,
    faCommentDots,
    faEye,
    faChevronLeft,
    faChevronRight
);

const { props } = usePage();
const books = props.books || [];
const savedBookIds = ref(
    Array.isArray(props.saved_books) ? props.saved_books : []
);
const continueReading = props.continueReading || null;
const search = ref("");
const continueReadingList = props.continueReadingList || [];

const user = props.auth?.user || {};



// Quick stats
const booksRead = computed(() =>
    props.reading_logs ? props.reading_logs.length : 0
);
const booksSaved = computed(() =>
    Array.isArray(props.saved_books) ? props.saved_books.length : 0
);
const reviewsWritten = computed(() =>
    props.reviews ? props.reviews.length : 0
);

// Recommended books (random 3 for now) - using props directly
const recommendedBooksFromProps = computed(() => {
    if (!books.length) return [];
    return books.slice(0, 3);
});

// Recent activity (last 3 reading logs)
const recentActivity = computed(() => {
    if (!props.reading_logs) return [];
    return props.reading_logs.slice(0, 3);
});

const filteredBooks = computed(() => {
    if (!search.value) return books;
    return books.filter(
        (book) =>
            (book.title &&
                book.title
                    .toLowerCase()
                    .includes(search.value.toLowerCase())) ||
            (book.author &&
                book.author.toLowerCase().includes(search.value.toLowerCase()))
    );
});

const handleSearch = (e) => {
    search.value = e.detail;
};

const saveBook = (book) => {
    router.post(
        route("books.save", { id: book.id }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                if (!savedBookIds.value.includes(book.id)) {
                    savedBookIds.value.push(book.id);
                }
            },
            onError: (errors) => {
                console.error("Error saving book:", errors);
            },
        }
    );
};

const unsaveBook = (book) => {
    router.post(
        route("books.unsave", { id: book.id }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                const index = savedBookIds.value.indexOf(book.id);
                if (index > -1) {
                    savedBookIds.value.splice(index, 1);
                }
            },
            onError: (errors) => {
                console.error("Error unsaving book:", errors);
            },
        }
    );
};

function restoreScrollIfNeeded() {
    const scrollPosition = sessionStorage.getItem("scrollPosition");
    const scrollBookId = sessionStorage.getItem("scrollBookId");
    if (scrollPosition && scrollBookId) {
        if (document.querySelector(`[data-book-id='${scrollBookId}']`)) {
            window.scrollTo({
                top: parseInt(scrollPosition, 10),
                behavior: "auto",
            });
            sessionStorage.removeItem("scrollPosition");
            sessionStorage.removeItem("scrollBookId");
        }
    }
}

// Book data computed properties
const newBooks = computed(() => {
    return props.newBooks || [];
});

const recommendedBooks = computed(() => {
    return props.recommendedBooks || [];
});

const hotBooks = computed(() => {
    return props.hotBooks || [];
});

const mostReadBooks = computed(() => {
    return props.mostReadBooks || [];
});

const highestRatedBooks = computed(() => {
    return (props.highestRatedBooks || []).filter(
        (book) =>
            typeof book.average_rating === "number" && book.reviews_count > 0
    );
});

onMounted(() => {
    window.addEventListener("user-search", handleSearch);
    restoreScrollIfNeeded();
});

router.on("finish", () => {
    restoreScrollIfNeeded();
});
onUnmounted(() => {
    window.removeEventListener("user-search", handleSearch);
});
</script>

<template>
    <Head title="Home" />
    <UserLayout>
        <!-- Welcome Message & Quick Links -->
        <div
            v-if="continueReading && continueReading.book"
            class="bg-green-100 border-2 border-green-600 rounded-xl flex justify-between items-center p-4 mb-6 shadow-lg animate-slide-down"
        >
            <div>
                <strong class="text-green-700">Continue Reading:</strong>
                {{ continueReading.book.title }}
                <span
                    v-if="continueReading.last_percent"
                    class="text-green-800 ml-1"
                >
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
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6"
        >
            <div>
                <h2 class="text-2xl font-bold text-green-700 mb-1">
                    Welcome back,
                    {{ user.first_name || user.user_name || "User" }}!
                </h2>
                <div class="text-gray-600 text-sm">
                    Happy reading at eBaryo Library 📚
                </div>
            </div>
            <div
                class="flex flex-col md:flex-row gap-2 flex-wrap justify-center text-center md:justify-start md:text-left"
            >
                <Link
                    :href="route('user.profile.index')"
                    class="w-full md:w-auto flex justify-center items-center gap-2 bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 focus:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-400"
                >
                    <font-awesome-icon icon="user" class="text-white text-lg" />
                    Profile
                </Link>
                <Link
                    :href="route('feedback.create')"
                    class="w-full md:w-auto flex justify-center items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 focus:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
                    <font-awesome-icon
                        icon="comment-dots"
                        class="text-white text-lg"
                    />
                    Feedback
                </Link>
                <Link
                    :href="route('books.saved')"
                    class="w-full md:w-auto flex justify-center items-center gap-2 bg-yellow-500 text-white px-4 py-2 rounded shadow hover:bg-yellow-600 focus:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                >
                    <font-awesome-icon
                        icon="bookmark"
                        class="text-white text-lg"
                    />
                    Saved Books
                </Link>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div
                class="bg-white border border-green-200 rounded-lg p-4 flex flex-col items-center shadow"
            >
                <font-awesome-icon
                    icon="book-open"
                    class="text-green-700 text-2xl mb-1"
                />
                <span class="text-3xl font-bold text-green-700">{{
                    booksRead
                }}</span>
                <span class="text-gray-600">Books Read</span>
            </div>
            <div
                class="bg-white border border-green-200 rounded-lg p-4 flex flex-col items-center shadow"
            >
                <font-awesome-icon
                    icon="bookmark"
                    class="text-yellow-600 text-2xl mb-1"
                />
                <span class="text-3xl font-bold text-yellow-600">{{
                    booksSaved
                }}</span>
                <span class="text-gray-600">Books Saved</span>
            </div>
            <div
                class="bg-white border border-green-200 rounded-lg p-4 flex flex-col items-center shadow"
            >
                <font-awesome-icon
                    icon="star"
                    class="text-blue-600 text-2xl mb-1"
                />
                <span class="text-3xl font-bold text-blue-600">{{
                    reviewsWritten
                }}</span>
                <span class="text-gray-600">Reviews Written</span>
            </div>
        </div>
        <!-- Recent Activity -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-green-700 mb-3">
                Recent Activity
            </h3>
            <div v-if="recentActivity.length" class="space-y-2">
                <div
                    v-for="log in recentActivity"
                    :key="log.id"
                    class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center gap-3 shadow"
                >
                    <span class="font-semibold text-green-700">{{
                        log.book?.title || "Book"
                    }}</span>
                    <span class="text-gray-500 text-sm"
                        >Read at:
                        {{ new Date(log.read_at).toLocaleString() }}</span
                    >
                    <span
                        v-if="log.last_percent"
                        class="text-xs text-green-600 ml-auto"
                        >Progress:
                        {{ Math.round(log.last_percent * 100) }}%</span
                    >
                </div>
            </div>
            <div v-else class="text-gray-400">No recent activity yet.</div>
        </div>
        <!-- Continue Reading Section -->
        <ContinueReadingSection
            :continueReadingList="continueReadingList"
            :savedBookIds="savedBookIds"
            :auth="$page.props.auth"
            @save="saveBook"
            @unsave="unsaveBook"
        />

        <!-- Recommended Books Section -->
        <BookSection
            title="Recommended for You"
            icon="thumbs-up"
            :books="recommendedBooks"
            sectionType="home"
            :savedBookIds="savedBookIds"
            :auth="$page.props.auth"
            badgeText="Top Picks"
            badgeColor="bg-yellow-400"
            borderColor="border-yellow-400"
            bgColor="bg-yellow-50"
            iconColor="text-yellow-600"
            titleColor="text-yellow-700"
            emptyMessage="No recommendations available yet. Check back later for personalized picks!"
            emptyIcon="thumbs-up"
            @save="saveBook"
            @unsave="unsaveBook"
        />

        <!-- New Books Section -->
        <BookSection
            title="New Books"
            icon="star"
            :books="newBooks"
            sectionType="new"
            :savedBookIds="savedBookIds"
            :auth="$page.props.auth"
            emptyMessage="No new books available at the moment. Check back soon for fresh additions!"
            emptyIcon="star"
            @save="saveBook"
            @unsave="unsaveBook"
        />
        <!-- Hot Books Section -->
        <BookSection
            title="Hot Books"
            icon="fire"
            :books="hotBooks"
            sectionType="hot"
            :savedBookIds="savedBookIds"
            :auth="$page.props.auth"
            emptyMessage="No trending books right now. Start reading to see what's popular!"
            emptyIcon="fire"
            iconColor="text-red-500"
            titleColor="text-green-700"
            @save="saveBook"
            @unsave="unsaveBook"
        />
        <!-- Most Read Books Section -->
        <BookSection
            title="Most Read Books"
            icon="chart-line"
            :books="mostReadBooks"
            sectionType="mostread"
            :savedBookIds="savedBookIds"
            :auth="$page.props.auth"
            emptyMessage="No reading statistics available yet. Be the first to read a book!"
            emptyIcon="chart-line"
            iconColor="text-blue-600"
            titleColor="text-green-700"
            @save="saveBook"
            @unsave="unsaveBook"
        />
        <!-- Highest Ratings Section -->
        <BookSection
            title="Highest Ratings"
            icon="trophy"
            :books="highestRatedBooks"
            sectionType="highestrated"
            :savedBookIds="savedBookIds"
            :auth="$page.props.auth"
            emptyMessage="No highly rated books yet."
            emptyIcon="trophy"
            iconColor="text-yellow-600"
            titleColor="text-green-700"
            @save="saveBook"
            @unsave="unsaveBook"
        />

        <!-- Animated Gradient Background -->
        <div
            class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 via-green-50 to-white animate-gradient-move"
        ></div>
        <!-- Continue Reading -->
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
@keyframes slide-down {
    from {
        transform: translateY(-30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
.animate-slide-down {
    animation: slide-down 0.7s cubic-bezier(0.4, 0, 0.2, 1) both;
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
a:focus,
button:focus,
.focus\:outline-none:focus {
    outline: 2px solid #34d399;
    outline-offset: 2px;
}

</style>
