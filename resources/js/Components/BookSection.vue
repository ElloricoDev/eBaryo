<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from "vue";
import BookCard from "@/Components/BookCard.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    icon: {
        type: String,
        required: true,
    },
    books: {
        type: Array,
        default: () => [],
    },
    sectionType: {
        type: String,
        required: true,
    },
    savedBookIds: {
        type: Array,
        default: () => [],
    },
    auth: {
        type: Object,
        required: true,
    },
    emptyMessage: {
        type: String,
        default: "No books available at the moment.",
    },
    emptyIcon: {
        type: String,
        default: "book",
    },
    badgeText: {
        type: String,
        default: "",
    },
    badgeColor: {
        type: String,
        default: "bg-green-400",
    },
    borderColor: {
        type: String,
        default: "border-green-400",
    },
    bgColor: {
        type: String,
        default: "bg-green-50",
    },
    iconColor: {
        type: String,
        default: "text-green-600",
    },
    titleColor: {
        type: String,
        default: "text-green-700",
    },
});

const emit = defineEmits(["save", "unsave"]);

const scrollRowRef = ref(null);
const hasPrev = ref(false);
const hasNext = ref(false);

const updateScrollButtons = () => {
    const el = scrollRowRef.value;
    if (!el) return;
    hasPrev.value = el.scrollLeft > 0;
    hasNext.value = el.scrollLeft + el.clientWidth < el.scrollWidth;
};

const scrollByAmount = (amount) => {
    const el = scrollRowRef.value;
    if (!el) return;
    el.scrollBy({ left: amount, behavior: "smooth" });
};

const handleScroll = () => {
    updateScrollButtons();
};

watch(
    () => props.books,
    () => {
        nextTick(() => {
            updateScrollButtons();
        });
    }
);

onMounted(() => {
    nextTick(() => {
        updateScrollButtons();
        if (scrollRowRef.value) {
            scrollRowRef.value.addEventListener("scroll", handleScroll);
        }
    });
});
onUnmounted(() => {
    if (scrollRowRef.value) {
        scrollRowRef.value.removeEventListener("scroll", handleScroll);
    }
});

const saveBook = (book) => {
    emit("save", book);
};

const unsaveBook = (book) => {
    emit("unsave", book);
};
</script>

<template>
    <div
        :class="
            borderColor
                ? `mb-8 border-2 ${borderColor} ${bgColor} rounded-xl shadow-lg p-4 relative`
                : 'mb-8 relative'
        "
    >
        <div class="flex items-center mb-3">
            <font-awesome-icon
                :icon="icon"
                class="text-xl mr-2"
                :class="iconColor"
            />
            <h3 class="text-lg sm:text-xl font-bold mr-2" :class="titleColor">
                {{ title }}
            </h3>
            <span
                v-if="badgeText"
                class="text-white text-xs font-bold px-2 py-1 rounded-full ml-2"
                :class="badgeColor"
            >
                {{ badgeText }}
            </span>
        </div>

        <div v-if="books.length > 0" class="relative">
            <!-- Edge fade hints -->
            <div aria-hidden="true" class="pointer-events-none absolute inset-y-0 left-0 w-8 bg-gradient-to-r from-white to-transparent rounded-l-xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-white to-transparent rounded-r-xl"></div>

            <button
                v-if="hasPrev"
                class="nav-btn left-0"
                aria-label="Previous Books"
                tabindex="0"
                @click="() => scrollByAmount(-300)"
            >
                <font-awesome-icon icon="chevron-left" />
            </button>
            <div
                ref="scrollRowRef"
                class="section-scroll-row hide-scrollbar md:gap-6"
            >
                <div
                    v-for="book in books"
                    :key="book.id"
                    class="flex flex-col items-center min-w-[140px] max-w-[160px] sm:min-w-[180px] sm:max-w-[180px] flex-shrink-0 group relative"
                >
                    <!-- Percentage badge at the top of the cover image for continue reading -->
                    <div
                        v-if="sectionType === 'continue'"
                        class="absolute top-2 left-1/2 -translate-x-1/2 bg-green-600 text-white text-xs font-bold px-3 py-1 rounded shadow-lg z-30 border border-white"
                        style="white-space: nowrap; pointer-events: none;"
                    >
                        {{ Math.round((book.last_percent || book.progress || 0) * 100) }}% read
                    </div>
                    <BookCard
                        :book="{ ...book, from: sectionType }"
                        :isSaved="savedBookIds.includes(book.id)"
                        :auth="auth"
                        @save="saveBook"
                        @unsave="unsaveBook"
                        class="w-full"
                    >
                        <template v-if="sectionType === 'mostread'" #footer>
                            <div class="flex items-center justify-between w-full">
                                <div
                                    class="flex items-center gap-1 text-sm font-bold text-blue-800"
                                    title="Total times this book has been read by all users"
                                >
                                    <font-awesome-icon
                                        icon="eye"
                                        class="text-blue-500"
                                    />
                                    <span>{{ book.read_count }}</span>
                                    <span class="text-xs font-normal text-blue-600"
                                        >read{{
                                            book.read_count === 1 ? '' : 's'
                                        }}</span
                                    >
                                </div>
                            </div>
                        </template>
                        <template v-else-if="sectionType === 'continue'" #footer>
                            <div class="w-full mt-2">
                                <div class="h-2 bg-gray-200 rounded-full relative overflow-hidden">
                                    <div
                                        class="h-2 bg-green-400 rounded-full transition-all"
                                        :style="{ width: ((book.last_percent || book.progress || 0) * 100) + '%' }"
                                    ></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1 text-center">
                                    {{ Math.round((book.last_percent || book.progress || 0) * 100) }}% read
                                </div>
                                <Link
                                    :href="route('books.epubReader', { id: book.id })"
                                    class="block mt-1 bg-green-50 hover:bg-green-100 text-green-700 font-semibold text-xs rounded px-3 py-1 text-center transition"
                                >
                                    Continue Read
                                </Link>
                            </div>
                        </template>
                    </BookCard>
                </div>
            </div>
            <button
                v-if="hasNext"
                class="nav-btn right-0"
                aria-label="Next Books"
                tabindex="0"
                @click="() => scrollByAmount(300)"
            >
                <font-awesome-icon icon="chevron-right" />
            </button>
        </div>

        <div v-else class="text-center text-gray-500 py-8">
            <font-awesome-icon
                :icon="emptyIcon"
                class="text-3xl mb-2"
                :class="iconColor.replace('-600', '-400')"
            />
            <div>{{ emptyMessage }}</div>
        </div>
    </div>
</template>

<style scoped>
.hide-scrollbar {
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.section-scroll-row {
    display: flex;
    overflow-x: auto;
    gap: 0;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    padding-bottom: 0.5rem;
    scroll-behavior: smooth;
    width: 100%;
    touch-action: pan-x;
}
@media (min-width: 640px) {
    .section-scroll-row {
        gap: 0;
        padding-left: 2.5rem;
        padding-right: 2.5rem;
    }
}
.nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: rgba(255, 255, 255, 0.85);
    color: #047857; /* green-700 */
    font-weight: 700;
    padding: 0.5rem;
    border-radius: 9999px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
}
.nav-btn:hover { background: #ecfdf5; box-shadow: 0 6px 16px rgba(0,0,0,0.12); transform: translateY(-50%) scale(1.05); }
.nav-btn:focus-visible { outline: 3px solid rgba(16, 185, 129, 0.6); outline-offset: 2px; }
</style>
