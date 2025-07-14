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
        .filter(
            (book) =>
                typeof book.average_rating === "number" &&
                book.reviews_count > 0
        )
        .slice(0, 40); // Show up to 40 highest rated books
});

const continueReadingSectionRef = ref(null);

const scrollContinueReading = (direction) => {
    const section = continueReadingSectionRef.value;
    if (
        section &&
        section.$el &&
        section.$el.querySelector(".continue-reading-list")
    ) {
        const list = section.$el.querySelector(".continue-reading-list");
        const scrollAmount = 300; // Adjust as needed
        if (direction === "prev") {
            list.scrollBy({ left: -scrollAmount, behavior: "smooth" });
        } else if (direction === "next") {
            list.scrollBy({ left: scrollAmount, behavior: "smooth" });
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
            <!-- Continue Reading Section -->
            <ContinueReadingSection
                ref="continueReadingSectionRef"
                :continueReadingList="continueReadingList"
                :savedBookIds="savedBookIds"
                :auth="$page.props.auth"
                @save="saveBook"
                @unsave="unsaveBook"
            />
        </div>

        <!-- Recommended Books Section -->
        <div class="relative">
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
        </div>
        <!-- New Books Section -->
        <div class="relative">
            <BookSection
                title="New Books for This Month"
                icon="star"
                :books="newBooks"
                sectionType="new"
                :savedBookIds="savedBookIds"
                :auth="$page.props.auth"
                emptyMessage="There is no new book for this month."
                emptyIcon="star"
                @save="saveBook"
                @unsave="unsaveBook"
            />
        </div>
        <!-- Hot Books Section -->
        <div class="relative">
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
        </div>
        <!-- Most Read Books Section -->
        <div class="relative">
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
        </div>
        <!-- Highest Ratings Section -->
        <div class="relative">
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
