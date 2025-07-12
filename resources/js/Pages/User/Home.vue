<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { usePage, router, Head, Link } from "@inertiajs/vue3";
import BookSection from "@/Components/BookSection.vue";
import ContinueReadingSection from "@/Components/ContinueReadingSection.vue";
import { ref, onMounted, onUnmounted, computed } from "vue";
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



// Recommended books 
const recommendedBooksFromProps = computed(() => {
    if (!books.length) return [];
    return books.slice(0, 10); // Show more recommended books
});

// Recent activity
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
    return (props.newBooks || []).slice(0, 40); // Show up to 40 new books
});

const recommendedBooks = computed(() => {
    return (props.recommendedBooks || []).slice(0, 40); // Show up to 40 recommended books
});

const hotBooks = computed(() => {
    return (props.hotBooks || []).slice(0, 40); // Show up to 40 hot books
});

const mostReadBooks = computed(() => {
    return (props.mostReadBooks || []).slice(0, 40); // Show up to 40 most read books
});

const highestRatedBooks = computed(() => {
    return (props.highestRatedBooks || [])
        .filter((book) => typeof book.average_rating === "number" && book.reviews_count > 0)
        .slice(0, 40); // Show up to 40 highest rated books
});

const continueReadingSectionRef = ref(null);

const scrollContinueReading = (direction) => {
    const section = continueReadingSectionRef.value;
    if (section && section.$el && section.$el.querySelector('.continue-reading-list')) {
        const list = section.$el.querySelector('.continue-reading-list');
        const scrollAmount = 300; // Adjust as needed
        if (direction === 'prev') {
            list.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else if (direction === 'next') {
            list.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    }
};

// Pagination state for each section
const booksPerPage = 4;
const sectionPages = {
    recommended: ref(0),
    new: ref(0),
    hot: ref(0),
    mostread: ref(0),
    highestrated: ref(0),
};

function paginatedBooks(list, section) {
    const page = sectionPages[section].value;
    const start = page * booksPerPage;
    return list.slice(start, start + booksPerPage);
}

function nextSectionPage(section, totalBooks) {
    const maxPage = Math.floor((totalBooks - 1) / booksPerPage);
    if (sectionPages[section].value < maxPage) {
        sectionPages[section].value++;
    }
}

function prevSectionPage(section) {
    if (sectionPages[section].value > 0) {
        sectionPages[section].value--;
    }
}

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
        <div class="relative">
            <button
                class="absolute left-2 bottom-14 z-10
                       bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold
                       py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent
                       sm:left-2 sm:top-1/2 sm:bottom-auto sm:py-3 sm:px-4 sm:text-xl
                       transition-all duration-200"
                aria-label="Previous Page"
                tabindex="0"
                @click="scrollContinueReading('prev')"
            >
                <font-awesome-icon icon="chevron-left" />
            </button>
            <!-- Continue Reading Section -->
            <ContinueReadingSection
                ref="continueReadingSectionRef"
                :continueReadingList="continueReadingList"
                :savedBookIds="savedBookIds"
                :auth="$page.props.auth"
                @save="saveBook"
                @unsave="unsaveBook"
            />
            <button
                class="absolute right-2 bottom-14 z-10
                       bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold
                       py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent
                       sm:right-2 sm:top-1/2 sm:bottom-auto sm:py-3 sm:px-4 sm:text-xl
                       transition-all duration-200"
                aria-label="Next Page"
                tabindex="0"
                @click="scrollContinueReading('next')"
            >
                <font-awesome-icon icon="chevron-right" />
            </button>
        </div>
       

        <!-- Recommended Books Section -->
        <div class="relative">
            <button
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Previous Recommended Books"
                tabindex="0"
                @click="prevSectionPage('recommended')"
                :disabled="sectionPages.recommended === 0"
            >
                <font-awesome-icon icon="chevron-left" />
            </button>
            <BookSection
                title="Recommended for You"
                icon="thumbs-up"
                :books="paginatedBooks(recommendedBooks, 'recommended')"
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
            <button
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Next Recommended Books"
                tabindex="0"
                @click="nextSectionPage('recommended', recommendedBooks.length)"
                :disabled="(sectionPages.recommended + 1) * booksPerPage >= recommendedBooks.length"
            >
                <font-awesome-icon icon="chevron-right" />
            </button>
        </div>
        <!-- New Books Section -->
        <div class="relative">
            <button
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Previous New Books"
                tabindex="0"
                @click="prevSectionPage('new')"
                :disabled="sectionPages.new === 0"
            >
                <font-awesome-icon icon="chevron-left" />
            </button>
            <BookSection
                title="New Books"
                icon="star"
                :books="paginatedBooks(newBooks, 'new')"
                sectionType="new"
                :savedBookIds="savedBookIds"
                :auth="$page.props.auth"
                emptyMessage="No new books available at the moment. Check back soon for fresh additions!"
                emptyIcon="star"
                @save="saveBook"
                @unsave="unsaveBook"
            />
            <button
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Next New Books"
                tabindex="0"
                @click="nextSectionPage('new', newBooks.length)"
                :disabled="(sectionPages.new + 1) * booksPerPage >= newBooks.length"
            >
                <font-awesome-icon icon="chevron-right" />
            </button>
        </div>
        <!-- Hot Books Section -->
        <div class="relative">
            <button
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Previous Hot Books"
                tabindex="0"
                @click="prevSectionPage('hot')"
                :disabled="sectionPages.hot === 0"
            >
                <font-awesome-icon icon="chevron-left" />
            </button>
            <BookSection
                title="Hot Books"
                icon="fire"
                :books="paginatedBooks(hotBooks, 'hot')"
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
            <button
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Next Hot Books"
                tabindex="0"
                @click="nextSectionPage('hot', hotBooks.length)"
                :disabled="(sectionPages.hot + 1) * booksPerPage >= hotBooks.length"
            >
                <font-awesome-icon icon="chevron-right" />
            </button>
        </div>
        <!-- Most Read Books Section -->
        <div class="relative">
            <button
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Previous Most Read Books"
                tabindex="0"
                @click="prevSectionPage('mostread')"
                :disabled="sectionPages.mostread === 0"
            >
                <font-awesome-icon icon="chevron-left" />
            </button>
            <BookSection
                title="Most Read Books"
                icon="chart-line"
                :books="paginatedBooks(mostReadBooks, 'mostread')"
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
            <button
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Next Most Read Books"
                tabindex="0"
                @click="nextSectionPage('mostread', mostReadBooks.length)"
                :disabled="(sectionPages.mostread + 1) * booksPerPage >= mostReadBooks.length"
            >
                <font-awesome-icon icon="chevron-right" />
            </button>
        </div>
        <!-- Highest Ratings Section -->
        <div class="relative">
            <button
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Previous Highest Rated Books"
                tabindex="0"
                @click="prevSectionPage('highestrated')"
                :disabled="sectionPages.highestrated === 0"
            >
                <font-awesome-icon icon="chevron-left" />
            </button>
            <BookSection
                title="Highest Ratings"
                icon="trophy"
                :books="paginatedBooks(highestRatedBooks, 'highestrated')"
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
            <button
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Next Highest Rated Books"
                tabindex="0"
                @click="nextSectionPage('highestrated', highestRatedBooks.length)"
                :disabled="(sectionPages.highestrated + 1) * booksPerPage >= highestRatedBooks.length"
            >
                <font-awesome-icon icon="chevron-right" />
            </button>
        </div>

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
