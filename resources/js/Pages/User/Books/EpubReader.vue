<script setup>
import { usePage, router } from "@inertiajs/vue3";
import { ref, onMounted, watch, onBeforeUnmount, nextTick } from "vue";
import ePub from "epubjs";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faArrowLeft,
    faChevronLeft,
    faChevronRight,
    faSearchPlus,
    faSearchMinus,
    faExpand,
    faSpinner,
} from "@fortawesome/free-solid-svg-icons";

library.add(
    faArrowLeft,
    faChevronLeft,
    faChevronRight,
    faSearchPlus,
    faSearchMinus,
    faExpand,
    faSpinner
);

const { props } = usePage();
const book = props.book;

const viewer = ref(null);
const epubBook = ref(null);
const rendition = ref(null);
const progress = ref(0); // percentage read
const fontSize = ref(18);
const canPrev = ref(true);
const canNext = ref(true);
const isLoading = ref(true);
const pageInput = ref(1);
const totalPages = ref(1);
const currentPage = ref(1);

const saveProgress = () => {
    if (!totalPages.value) return;
    const percent = Math.round((currentPage.value / totalPages.value) * 100);
    
    router.post(
        route("books.saveProgress", { id: book.id }),
        { percent },
        { preserveState: true, preserveScroll: true }
    );
};

const goToPageInput = () => {
    let pageNum = parseInt(pageInput.value, 10);
    if (isNaN(pageNum)) pageNum = 1;
    if (pageNum < 1) pageNum = 1;
    if (pageNum > totalPages.value) pageNum = totalPages.value;
    currentPage.value = pageNum;
    if (rendition.value && epubBook.value) {
        const cfi = epubBook.value.locations.cfiFromPercentage(
            (pageNum - 1) / totalPages.value
        );
        rendition.value.display(cfi);
    }
    saveProgress();
};

const updateNav = (location) => {
    canPrev.value = !location.atStart;
    canNext.value = !location.atEnd;
    progress.value = Math.round(location.start.percentage * 100);
    if (epubBook.value && epubBook.value.locations && totalPages.value > 1) {
        currentPage.value =
            Math.round(location.start.percentage * totalPages.value) + 1;
        pageInput.value = currentPage.value;
    }
    saveProgress();
};

onMounted(async () => {
    isLoading.value = true;
    await nextTick();
    if (props.url && viewer.value) {
        epubBook.value = ePub(props.url);
        rendition.value = epubBook.value.renderTo(viewer.value, {
            width: "100%",
            height: "100%",
            flow: "paginated",
            manager: "default",
            spread: "auto",
        });
        rendition.value.themes.fontSize(`${fontSize.value}px`);
        epubBook.value.ready.then(async () => {
            await epubBook.value.locations.generate(1000);
            totalPages.value = epubBook.value.locations.total;
            let startCfi = null;
            
            // Convert percentage back to decimal for epub.js
            const lastPercentDecimal = props.lastPercent ? props.lastPercent / 100 : 0;
            
            if (
                lastPercentDecimal > 0 &&
                lastPercentDecimal < 1 &&
                epubBook.value.locations
            ) {
                startCfi = epubBook.value.locations.cfiFromPercentage(lastPercentDecimal);
                if (
                    !startCfi ||
                    typeof startCfi !== "string" ||
                    startCfi === "epubcfi(/6/2[cover]!/6)"
                ) {
                    startCfi = undefined;
                } else {
                    // Calculate the correct page number from the percentage
                    currentPage.value = Math.round(lastPercentDecimal * totalPages.value) + 1;
                    pageInput.value = currentPage.value;
                }
            }
            try {
                await rendition.value.display(startCfi);
            } catch (e) {
                await rendition.value.display();
            }
            isLoading.value = false;
        });
        rendition.value.on("relocated", (location) => {
            updateNav(location);
        });
        rendition.value.on("rendered", () => {});
    }
});

watch(fontSize, (val) => {
    if (rendition.value) {
        rendition.value.themes.fontSize(`${val}px`);
    }
});

const prevPage = () => {
    if (rendition.value) {
        rendition.value.prev();
    }
};

const nextPage = () => {
    if (rendition.value) {
        rendition.value.next();
    }
};

const zoomIn = () => {
    fontSize.value += 2;
};

const zoomOut = () => {
    if (fontSize.value > 10) fontSize.value -= 2;
};

const goBack = () => {
    window.history.length > 1 ? window.history.back() : router.visit("/home");
};

onBeforeUnmount(() => {
    if (rendition.value) {
        rendition.value.destroy?.();
        rendition.value = null;
    }
    if (epubBook.value) {
        epubBook.value.destroy?.();
        epubBook.value = null;
    }
});
</script>

<template>
    <div class="epub-reader-root">
        <!-- Enhanced Top Bar -->
        <div class="epub-topbar">
            <div class="flex items-center justify-between w-full">
                <button
                    @click="goBack"
                    class="flex items-center gap-2 font-semibold hover:underline transition-all duration-200 hover:scale-105"
                >
                    <font-awesome-icon icon="arrow-left" />
                    Back to Book
                </button>
                <span class="text-xl font-bold truncate max-w-md">{{ book.title }}</span>
                <div class="w-20"></div> <!-- Spacer for centering -->
            </div>
        </div>

        <!-- Enhanced Controls Panel -->
        <div class="epub-controls card">
            <!-- Navigation Controls -->
            <div class="flex items-center gap-3">
                <button
                    @click="prevPage"
                    :disabled="!canPrev || isLoading"
                    class="control-btn"
                    aria-label="Previous Page"
                >
                    <font-awesome-icon icon="chevron-left" />
                </button>
                
                <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-slate-200">
                    <span class="text-sm font-medium text-slate-700">Page</span>
                    <input
                        type="number"
                        min="1"
                        :max="totalPages"
                        v-model="pageInput"
                        @keyup.enter="goToPageInput"
                        @blur="goToPageInput"
                        class="page-input"
                        :disabled="isLoading"
                    />
                    <span class="text-slate-500">/ {{ totalPages }}</span>
                </div>
                
                <button
                    @click="nextPage"
                    :disabled="!canNext || isLoading"
                    class="control-btn"
                    aria-label="Next Page"
                >
                    <font-awesome-icon icon="chevron-right" />
                </button>
            </div>

            <!-- Font Controls -->
            <div class="flex items-center gap-3">
                <button
                    @click="zoomOut"
                    :disabled="isLoading"
                    class="control-btn"
                    aria-label="Zoom Out"
                >
                    <font-awesome-icon icon="search-minus" />
                </button>
                
                <div class="bg-white px-4 py-2 rounded-xl border border-slate-200">
                    <span class="text-sm font-medium text-slate-700">Font: {{ fontSize }}px</span>
                </div>
                
                <button
                    @click="zoomIn"
                    :disabled="isLoading"
                    class="control-btn"
                    aria-label="Zoom In"
                >
                    <font-awesome-icon icon="search-plus" />
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div
            v-if="isLoading"
            class="epub-loading"
        >
            <div class="flex flex-col items-center gap-4">
                <font-awesome-icon
                    icon="spinner"
                    spin
                    class="text-green-600 text-4xl"
                />
                <div class="text-center">
                    <div class="text-green-700 font-semibold text-lg">Loading Book...</div>
                    <div class="text-green-600 text-sm">Please wait while we prepare your reading experience</div>
                </div>
            </div>
        </div>

        <!-- Reader Viewport -->
        <div ref="viewer" class="epub-viewer"></div>
    </div>
</template>

<style scoped>
.epub-reader-root {
    width: 100vw;
    height: 100vh;
    display: flex;
    flex-direction: column;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.epub-topbar {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);
    color: #fff;
    font-size: 1.1rem;
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    z-index: 20;
}

.card {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    border-radius: 1.5rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 1rem 1.5rem;
    margin: 1rem 2rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.control-btn {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0.75em 1em;
    font-size: 1.1em;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #475569;
    min-width: 44px;
    min-height: 44px;
}

.control-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.control-btn:active:not(:disabled) {
    transform: translateY(0);
}

.control-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.page-input {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.25em 0.5em;
    font-size: 1em;
    width: 3.5em;
    text-align: center;
    background: #fff;
    transition: all 0.2s ease;
}

.page-input:focus {
    outline: none;
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
}

.epub-loading {
    min-height: 8rem;
    color: #16a34a;
    font-size: 1.2em;
    display: flex;
    align-items: center;
    justify-content: center;
}

.epub-viewer {
    flex: 1 1 0%;
    width: 100%;
    height: 0;
    min-height: 0;
    overflow: auto;
    background: #fff;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .card {
        flex-direction: column;
        gap: 1rem;
        margin: 1rem;
        padding: 1rem;
    }
    
    .epub-topbar {
        padding: 1rem;
    }
    
    .epub-topbar span {
        font-size: 1rem;
        max-width: 200px;
    }
}

/* Custom Scrollbar */
.epub-viewer::-webkit-scrollbar {
    width: 8px;
}

.epub-viewer::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.epub-viewer::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.epub-viewer::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
