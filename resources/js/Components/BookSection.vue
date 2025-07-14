<script setup>
import { ref, onMounted, onUnmounted, nextTick } from "vue";
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
            <button
                v-if="hasPrev"
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
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
                <BookCard
                    v-for="book in books"
                    :key="book.id"
                    :book="{ ...book, from: sectionType }"
                    :isSaved="savedBookIds.includes(book.id)"
                    :auth="auth"
                    @save="saveBook"
                    @unsave="unsaveBook"
                    class="mx-2 flex flex-col items-center min-w-[180px] max-w-[220px] sm:min-w-[260px] sm:max-w-[260px] flex-shrink-0"
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
                                        book.read_count === 1 ? "" : "s"
                                    }}</span
                                >
                            </div>
                        </div>
                    </template>
                </BookCard>
            </div>
            <button
                v-if="hasNext"
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200"
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
</style>
