<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import { usePage, router } from "@inertiajs/vue3";
import BookSection from "@/Components/BookSection.vue";
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
const savedBookIds = ref(
    Array.isArray(props.saved_books) ? props.saved_books : []
);
const search = ref("");
const continueReadingList = props.continueReadingList || [];

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

const newBooks = computed(() => {
    return (props.newBooks || []).slice(0, 40);
});

const recommendedBooks = computed(() => {
    return (props.recommendedBooks || []).slice(0, 40);
});

const hotBooks = computed(() => {
    return (props.hotBooks || []).slice(0, 40);
});

const mostReadBooks = computed(() => {
    return (props.mostReadBooks || []).slice(0, 40);
});

const highestRatedBooks = computed(() => {
    return (props.highestRatedBooks || [])
        .filter(
            (book) =>
                typeof book.average_rating === "number" &&
                book.reviews_count > 0
        )
        .slice(0, 40);
});

// ---- Search filtering helpers ----
const normalizedSearch = computed(() => (search.value || "").trim().toLowerCase());
const hasSearch = computed(() => normalizedSearch.value.length > 0);

function matchesSearch(book) {
    const q = normalizedSearch.value;
    if (!q) return true;
    const title = (book.title || "").toLowerCase();
    const author = (book.author || "").toLowerCase();
    const category = (book.category?.name || "").toLowerCase();
    return title.includes(q) || author.includes(q) || category.includes(q);
}

const filteredRecommendedBooks = computed(() => {
    const list = recommendedBooks.value;
    if (!normalizedSearch.value) return list;
    return list.filter(matchesSearch);
});

const filteredNewBooks = computed(() => {
    const list = newBooks.value;
    if (!normalizedSearch.value) return list;
    return list.filter(matchesSearch);
});

const filteredHotBooks = computed(() => {
    const list = hotBooks.value;
    if (!normalizedSearch.value) return list;
    return list.filter(matchesSearch);
});

const filteredMostReadBooks = computed(() => {
    const list = mostReadBooks.value;
    if (!normalizedSearch.value) return list;
    return list.filter(matchesSearch);
});

const filteredHighestRatedBooks = computed(() => {
    const list = highestRatedBooks.value;
    if (!normalizedSearch.value) return list;
    return list.filter(matchesSearch);
});

const totalFilteredResults = computed(() =>
    filteredRecommendedBooks.value.length +
    filteredNewBooks.value.length +
    filteredHotBooks.value.length +
    filteredMostReadBooks.value.length +
    filteredHighestRatedBooks.value.length
);


const booksPerPage = 7;
const sectionPages = {
    recommended: ref(0),
    new: ref(0),
    hot: ref(0),
    mostread: ref(0),
    highestrated: ref(0),
};

function paginatedBooks(list, section) {
    // Support passing a ref/computed or a plain array
    const items = Array.isArray(list) ? list : Array.isArray(list?.value) ? list.value : [];
    const page = sectionPages[section].value;
    const start = page * booksPerPage;
    return items.slice(start, start + booksPerPage);
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
        <div v-if="hasSearch && totalFilteredResults === 0" class="mb-6 p-4 border border-gray-200 rounded bg-gray-50 text-gray-600">
            No results found for "{{ search }}".
        </div>
        <div class="relative">
            <BookSection
                title="Continue Reading"
                icon="play-circle"
                :books="continueReadingList.map(log => ({ ...log.book, last_percent: log.last_percent, progress: log.last_percent }))"
                sectionType="continue"
                :savedBookIds="savedBookIds"
                :auth="$page.props.auth"
                emptyMessage="No books to continue reading. Start a new book today!"
            />

            
        </div>

        <div class="relative">
            <BookSection
                title="Recommended for You"
                icon="thumbs-up"
                :books="paginatedBooks(filteredRecommendedBooks, 'recommended')"
                sectionType="home"
                :savedBookIds="savedBookIds"
                :auth="$page.props.auth"
                badgeText="Top Picks"
                badgeColor="bg-yellow-400"
                borderColor="border-yellow-400"
                bgColor="bg-yellow-50"
                iconColor="text-yellow-600"
                titleColor="text-yellow-700"
                :emptyMessage="hasSearch ? 'No results in Recommended.' : 'No recommendations available yet. Check back later for personalized picks!'"
                emptyIcon="thumbs-up"
                @save="saveBook"
                @unsave="unsaveBook"
            />
        </div>
        <div class="relative">
            <BookSection
                title="New Books for This Month"
                icon="star"
                :books="paginatedBooks(filteredNewBooks, 'new')"
                sectionType="new"
                :savedBookIds="savedBookIds"
                :auth="$page.props.auth"
                :emptyMessage="hasSearch ? 'No results in New Books.' : 'There is no new book for this month.'"
                emptyIcon="star"
                @save="saveBook"
                @unsave="unsaveBook"
            />
        </div>
        <div class="relative">
            <BookSection
                title="Hot Books"
                icon="fire"
                :books="paginatedBooks(filteredHotBooks, 'hot')"
                sectionType="hot"
                :savedBookIds="savedBookIds"
                :auth="$page.props.auth"
                :emptyMessage="hasSearch ? 'No results in Hot Books.' : 'No trending books right now. Start reading to see what\'s popular!'"
                emptyIcon="fire"
                iconColor="text-red-500"
                titleColor="text-green-700"
                @save="saveBook"
                @unsave="unsaveBook"
            />
        </div>
        <div class="relative">
            <BookSection
                title="Most Read Books"
                icon="chart-line"
                :books="paginatedBooks(filteredMostReadBooks, 'mostread')"
                sectionType="mostread"
                :savedBookIds="savedBookIds"
                :auth="$page.props.auth"
                :emptyMessage="hasSearch ? 'No results in Most Read.' : 'No reading statistics available yet. Be the first to read a book!'"
                emptyIcon="chart-line"
                iconColor="text-blue-600"
                titleColor="text-green-700"
                @save="saveBook"
                @unsave="unsaveBook"
            />
        </div>
        <div class="relative">
            <BookSection
                title="Highest Ratings"
                icon="trophy"
                :books="paginatedBooks(filteredHighestRatedBooks, 'highestrated')"
                sectionType="highestrated"
                :savedBookIds="savedBookIds"
                :auth="$page.props.auth"
                :emptyMessage="hasSearch ? 'No results in Highest Ratings.' : 'No highly rated books yet.'"
                emptyIcon="trophy"
                iconColor="text-yellow-600"
                titleColor="text-green-700"
                @save="saveBook"
                @unsave="unsaveBook"
            />
        </div>
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
