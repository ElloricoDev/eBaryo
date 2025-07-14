<script setup>
import { usePage, router } from "@inertiajs/vue3";
import { computed, ref, onMounted, watch } from "vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faArrowLeft,
    faChevronLeft,
    faChevronRight,
    faSearchPlus,
    faSearchMinus,
    faExpand,
} from "@fortawesome/free-solid-svg-icons";
import * as pdfjsLib from "pdfjs-dist";
import pdfjsWorkerSrc from "pdfjs-dist/build/pdf.worker.mjs?url";

library.add(
    faArrowLeft,
    faChevronLeft,
    faChevronRight,
    faSearchPlus,
    faSearchMinus,
    faExpand
);

defineOptions({ layout: UserLayout });

const { props } = usePage();
const book = props.book;
const lastPercent = props.lastPercent || 0;

const goBack = () => {
    window.history.length > 1 ? window.history.back() : router.visit("/home");
};

const fileUrl = computed(() => book.ebook_file);

if (typeof window !== "undefined") {
    const setVh = () => {
        document.documentElement.style.setProperty(
            "--vh",
            `${window.innerHeight * 0.01}px`
        );
    };
    setVh();
    window.addEventListener("resize", setVh);
}

const canvasRef = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const zoom = ref(1);
const isLoading = ref(false);
const pageInput = ref(1);

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorkerSrc;

const isMobile = () => window.innerWidth <= 768;

const fitToWidthScale = (unscaledViewport, container) => {
    return container.offsetWidth / unscaledViewport.width;
};

const fitToWidth = () => {
    zoom.value = 1;
    renderPDF(fileUrl.value);
};

const saveProgress = () => {
    if (!totalPages.value) return;
    const percent = currentPage.value / totalPages.value;
    router.post(
        route("books.saveProgress", { id: book.id }),
        { percent },
        { preserveState: true, preserveScroll: true }
    );
};

const renderPDF = async (url) => {
    if (!url) return;
    isLoading.value = true;
    const loadingTask = pdfjsLib.getDocument(url);
    const pdf = await loadingTask.promise;
    totalPages.value = pdf.numPages;
    if (currentPage.value === 1 && lastPercent > 0 && lastPercent < 1) {
        currentPage.value = Math.max(
            1,
            Math.round(lastPercent * totalPages.value)
        );
        pageInput.value = currentPage.value;
    }
    const page = await pdf.getPage(currentPage.value);

    const canvas = canvasRef.value;
    const container = canvas.parentNode;
    const unscaledViewport = page.getViewport({ scale: 1 });

    let scale = zoom.value;
    if (isMobile()) {
        const fitScale = fitToWidthScale(unscaledViewport, container);
        scale = fitScale * zoom.value;
    }

    const viewport = page.getViewport({ scale });

    const context = canvas.getContext("2d");
    context.clearRect(0, 0, canvas.width, canvas.height);
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    await page.render({ canvasContext: context, viewport }).promise;
    isLoading.value = false;
};

const goToPage = (pageNum) => {
    if (pageNum < 1 || pageNum > totalPages.value) return;
    currentPage.value = pageNum;
    pageInput.value = pageNum;
    renderPDF(fileUrl.value);
    saveProgress();
};

const goToPageInput = () => {
    let pageNum = parseInt(pageInput.value, 10);
    if (isNaN(pageNum)) pageNum = 1;
    if (pageNum < 1) pageNum = 1;
    if (pageNum > totalPages.value) pageNum = totalPages.value;
    currentPage.value = pageNum;
    renderPDF(fileUrl.value);
    saveProgress();
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        pageInput.value = currentPage.value;
        renderPDF(fileUrl.value);
        saveProgress();
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        pageInput.value = currentPage.value;
        renderPDF(fileUrl.value);
        saveProgress();
    }
};

const zoomIn = () => {
    zoom.value = Math.min(zoom.value + 0.1, 3);
    renderPDF(fileUrl.value);
};

const zoomOut = () => {
    zoom.value = Math.max(zoom.value - 0.1, 0.5);
    renderPDF(fileUrl.value);
};

onMounted(() => {
    renderPDF(fileUrl.value);
});

watch(fileUrl, (newUrl) => {
    currentPage.value = 1;
    zoom.value = 1;
    pageInput.value = 1;
    renderPDF(newUrl);
});
</script>

<template>
    <Head :title="book.title" />
    <div class="fixed inset-0 bg-white z-50 flex flex-col">
        <div class="flex items-center p-4 bg-green-700 text-white shadow">
            <button
                @click="goBack"
                class="flex items-center gap-2 font-semibold hover:underline"
            >
                <font-awesome-icon icon="arrow-left" />
                Back
            </button>
            <span class="ml-4 text-lg font-bold truncate">{{
                book.title
            }}</span>
        </div>
        <div class="flex-1 min-h-0 pdf-viewer-container">
            <div
                class="pdf-controls card flex flex-wrap items-center justify-center gap-2 mb-4 mt-4 p-3 shadow-lg rounded-lg bg-white/90 backdrop-blur-md"
            >
                <button
                    @click="prevPage"
                    :disabled="currentPage === 1 || isLoading"
                    class="icon-btn"
                    aria-label="Previous Page"
                >
                    <font-awesome-icon icon="chevron-left" />
                </button>
                <span class="text-base flex items-center gap-1">
                    Page
                    <input
                        type="number"
                        min="1"
                        :max="totalPages"
                        v-model="pageInput"
                        @keyup.enter="goToPageInput"
                        @blur="goToPageInput"
                        class="page-input"
                        :disabled="isLoading"
                        style="width: 3.5em; text-align: center"
                    />
                    / {{ totalPages }}
                </span>
                <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages || isLoading"
                    class="icon-btn"
                    aria-label="Next Page"
                >
                    <font-awesome-icon icon="chevron-right" />
                </button>
                <button
                    @click="zoomOut"
                    :disabled="zoom <= 0.5 || isLoading"
                    class="icon-btn"
                    aria-label="Zoom Out"
                >
                    <font-awesome-icon icon="search-minus" />
                </button>
                <span class="text-base">{{ (zoom * 100).toFixed(0) }}%</span>
                <button
                    @click="zoomIn"
                    :disabled="zoom >= 3 || isLoading"
                    class="icon-btn"
                    aria-label="Zoom In"
                >
                    <font-awesome-icon icon="search-plus" />
                </button>
                <button
                    @click="fitToWidth"
                    :disabled="isLoading"
                    class="icon-btn fit-btn"
                    aria-label="Fit to Width"
                >
                    <font-awesome-icon icon="expand" />
                </button>
            </div>
            <div
                v-if="isLoading"
                class="pdf-loading flex items-center justify-center w-full h-32"
            >
                <svg
                    class="animate-spin h-8 w-8 text-green-600"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v8z"
                    ></path>
                </svg>
                <span class="ml-2 text-green-700 font-semibold"
                    >Loading...</span
                >
            </div>
            <canvas ref="canvasRef" class="pdf-canvas"></canvas>
        </div>
    </div>
</template>

<style scoped>
.fixed {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: calc(var(--vh, 1vh) * 100);
    background: #fff;
    z-index: 100;
    display: flex;
    flex-direction: column;
}
.flex-1,
.pdf-viewer-container {
    flex: 1 1 0%;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    height: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
}

.pdf-canvas {
    width: auto;
    max-width: none;
    height: auto;
    background: #fff;
    display: block;
    margin-left: auto;
    margin-right: auto;
}
.card {
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.08);
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.95);
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    margin-top: 1rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.icon-btn {
    background: #f3f4f6;
    border: none;
    border-radius: 0.5rem;
    padding: 0.5em 0.7em;
    font-size: 1.2em;
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.icon-btn:active {
    background: #e5e7eb;
}
.icon-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.fit-btn {
    background: #16a34a;
    color: #fff;
    margin-left: 0.5em;
}
.fit-btn:active {
    background: #15803d;
}
.page-input {
    border: 1px solid #d1d5db;
    border-radius: 0.4em;
    padding: 0.2em 0.4em;
    font-size: 1em;
    width: 3.5em;
    margin: 0 0.2em;
}
.pdf-loading {
    min-height: 4rem;
    color: #16a34a;
    font-size: 1.2em;
}
</style>
