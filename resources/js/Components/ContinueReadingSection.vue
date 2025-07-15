<script setup>
import BookCard from "@/Components/BookCard.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { ref, onMounted, onUnmounted, nextTick } from "vue";

const props = defineProps({
    continueReadingList: {
        type: Array,
        default: () => [],
    },
    savedBookIds: {
        type: Array,
        default: () => [],
    },
    auth: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["save", "unsave"]);

const saveBook = (book) => {
    emit("save", book);
};

const unsaveBook = (book) => {
    emit("unsave", book);
};

const scrollRowRef = ref(null);
const hasPrev = ref(false);
const hasNext = ref(false);

const updateScrollButtons = () => {
    const el = scrollRowRef.value;
    if (!el) return;
    hasPrev.value = el.scrollLeft > 0;
    hasNext.value = el.scrollLeft + el.clientWidth < el.scrollWidth - 1;
};

const scrollByAmount = (amount) => {
    const el = scrollRowRef.value;
    if (!el) return;
    el.scrollBy({ left: amount, behavior: "smooth" });
};

const handleScroll = () => {
    updateScrollButtons();
};

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
</script>

<template>
    <div
        class="mb-8 border-2 border-green-400 bg-green-50 rounded-xl shadow-lg p-4 relative"
    >
        <div class="flex items-center mb-3">
            <font-awesome-icon
                icon="play-circle"
                class="text-green-600 text-xl mr-2"
            />
            <h3 class="text-lg sm:text-xl font-bold text-green-700 mr-2">
                Continue Reading
            </h3>
            <span
                class="bg-green-400 text-white text-xs font-bold px-2 py-1 rounded-full ml-2"
            >
                In Progress
            </span>
        </div>
        <div class="border-b border-gray-200 mb-4"></div>

        <div v-if="continueReadingList.length" class="relative">
            <button
                v-if="hasPrev"
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Previous Continue Reading"
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
                    v-for="log in continueReadingList"
                    :key="log.id"
                    class="mx-2 flex flex-col items-center min-w-[180px] max-w-[220px] sm:min-w-[260px] sm:max-w-[260px] flex-shrink-0 group relative"
                >
                    <BookCard
                        :book="{
                            ...log.book,
                            from: 'continue',
                            highlightProgress: true,
                            progress: log.last_percent,
                        }"
                        :isSaved="savedBookIds.includes(log.book.id)"
                        :auth="auth"
                        @save="saveBook"
                        @unsave="unsaveBook"
                    >
                        <template #footer>
                            <div class="w-full mt-2">
                                <div class="h-2 bg-gray-200 rounded-full relative overflow-hidden">
                                    <div
                                        class="h-2 bg-green-400 rounded-full transition-all"
                                        :style="{ width: ((log.last_percent || 0) * 100) + '%' }"
                                    ></div>
                                </div>
                                <Link
                                    :href="route('books.epubReader', { id: log.book.id })"
                                    class="block mt-1 bg-green-50 hover:bg-green-100 text-green-700 font-semibold text-xs rounded px-3 py-1 text-center transition"
                                >
                                    Continue Read
                                </Link>
                            </div>
                        </template>
                    </BookCard>
                    <!-- Hover overlay for percent, outside BookCard -->
                    <div
                        class="absolute top-2 left-1/2 -translate-x-1/2 bg-green-600 text-white text-xs font-bold px-3 py-1 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-30 border border-white"
                        style="white-space: nowrap;"
                    >
                        {{ Math.round((log.last_percent || 0) * 100) }}% read
                    </div>
                </div>
            </div>
            <button
                v-if="hasNext"
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
                aria-label="Next Continue Reading"
                tabindex="0"
                @click="() => scrollByAmount(300)"
            >
                <font-awesome-icon icon="chevron-right" />
            </button>
        </div>

        <div v-else class="text-center text-gray-400 py-10 animate-fade-in">
            <font-awesome-icon
                icon="play-circle"
                class="text-gray-200 text-4xl mb-2 animate-pulse"
            />
            <div class="text-base mt-2">No books in progress. Start reading to see your progress here!</div>
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
    gap: 0.5rem;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    padding-bottom: 0.5rem;
    scroll-behavior: smooth;
    width: 100%;
    touch-action: pan-x;
}
@media (min-width: 640px) {
    .section-scroll-row {
        gap: 1rem;
        padding-left: 2.5rem;
        padding-right: 2.5rem;
    }
}
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in {
    animation: fade-in 1s both;
}
@keyframes pulse-cta {
    0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
    50% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
}
.animate-pulse {
    animation: pulse-cta 2s infinite;
}
</style>
