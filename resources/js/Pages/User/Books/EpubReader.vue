<script setup>
import { usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, watch, onBeforeUnmount, nextTick } from 'vue';
import ePub from 'epubjs';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faArrowLeft, faChevronLeft, faChevronRight, faSearchPlus, faSearchMinus, faExpand, faSpinner } from '@fortawesome/free-solid-svg-icons';

library.add(faArrowLeft, faChevronLeft, faChevronRight, faSearchPlus, faSearchMinus, faExpand, faSpinner);

const { props } = usePage();
const book = props.book;
const lastPercent = props.lastPercent || 0;

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
  const percent = currentPage.value / totalPages.value;
  router.post(
    route('books.saveProgress', { id: book.id }),
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
    const cfi = epubBook.value.locations.cfiFromPercentage((pageNum - 1) / totalPages.value);
    rendition.value.display(cfi);
  }
  saveProgress();
};

const updateNav = (location) => {
  canPrev.value = !location.atStart;
  canNext.value = !location.atEnd;
  // Update progress and page
  progress.value = Math.round(location.start.percentage * 100);
  if (epubBook.value && epubBook.value.locations && totalPages.value > 1) {
    currentPage.value = Math.round(location.start.percentage * totalPages.value) + 1;
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
      width: '100%',
      height: '100%',
      flow: 'paginated',
      manager: 'default',
      spread: 'auto'
    });
    rendition.value.themes.fontSize(`${fontSize.value}px`);
    epubBook.value.ready.then(async () => {
      // Generate locations for accurate page navigation
      await epubBook.value.locations.generate(1000);
      totalPages.value = epubBook.value.locations.total;
      // Start at lastPercent if available
      let startCfi = null;
      if (lastPercent > 0 && lastPercent < 1 && epubBook.value.locations) {
        startCfi = epubBook.value.locations.cfiFromPercentage(lastPercent);
        // Validate CFI: if not found, fallback to start
        if (!startCfi || typeof startCfi !== 'string' || startCfi === 'epubcfi(/6/2[cover]!/6)') {
          startCfi = undefined;
        } else {
          currentPage.value = Math.round(lastPercent * totalPages.value) + 1;
          pageInput.value = currentPage.value;
        }
      }
      try {
        await rendition.value.display(startCfi);
      } catch (e) {
        // If CFI is invalid or section not found, fallback to start
        await rendition.value.display();
      }
      isLoading.value = false;
    });
    rendition.value.on('relocated', (location) => {
      updateNav(location);
    });
    rendition.value.on('rendered', () => {
      // EPUB rendered
    });
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
  window.history.length > 1
    ? window.history.back()
    : router.visit('/home');
}

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
    <div class="epub-topbar">
      <button @click="goBack" class="flex items-center gap-2 font-semibold hover:underline">
        <font-awesome-icon icon="arrow-left" />
        Back
      </button>
      <span class="ml-4 text-lg font-bold truncate">{{ book.title }}</span>
    </div>
    <div class="epub-controls card flex flex-wrap items-center justify-center gap-2 mb-4 mt-4 p-3 shadow-lg rounded-lg bg-white/90 backdrop-blur-md">
      <button @click="prevPage" :disabled="!canPrev || isLoading" class="icon-btn" aria-label="Previous Page">
        <font-awesome-icon icon="chevron-left" />
      </button>
      <span class="text-base flex items-center gap-1">
        Page
        <input type="number" min="1" :max="totalPages" v-model="pageInput" @keyup.enter="goToPageInput" @blur="goToPageInput" class="page-input" :disabled="isLoading" style="width: 3.5em; text-align: center;" />
        / {{ totalPages }}
      </span>
      <button @click="nextPage" :disabled="!canNext || isLoading" class="icon-btn" aria-label="Next Page">
        <font-awesome-icon icon="chevron-right" />
      </button>
      <button @click="zoomOut" :disabled="isLoading" class="icon-btn" aria-label="Zoom Out">
        <font-awesome-icon icon="search-minus" />
      </button>
      <span class="text-base">Font: {{ fontSize }}px</span>
      <button @click="zoomIn" :disabled="isLoading" class="icon-btn" aria-label="Zoom In">
        <font-awesome-icon icon="search-plus" />
      </button>
    </div>
    <div v-if="isLoading" class="epub-loading flex items-center justify-center w-full h-32">
      <font-awesome-icon icon="spinner" spin class="text-green-600 text-3xl" />
      <span class="ml-2 text-green-700 font-semibold">Loading...</span>
    </div>
    <div ref="viewer" class="epub-viewer"></div>
  </div>
</template>

<style scoped>
.epub-reader-root {
  width: 100vw;
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f5f5f5;
}
.epub-topbar {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 2rem;
  background: #15803d;
  color: #fff;
  font-size: 1.1rem;
  border-bottom: 1px solid #e5e7eb;
  box-shadow: 0 2px 4px rgba(0,0,0,0.03);
  z-index: 20;
}
.card {
  box-shadow: 0 2px 12px 0 rgba(0,0,0,0.08);
  border-radius: 1rem;
  background: rgba(255,255,255,0.95);
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
.page-input {
  border: 1px solid #d1d5db;
  border-radius: 0.4em;
  padding: 0.2em 0.4em;
  font-size: 1em;
  width: 3.5em;
  margin: 0 0.2em;
}
.epub-loading {
  min-height: 4rem;
  color: #16a34a;
  font-size: 1.2em;
}
.epub-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  box-shadow: 0 2px 4px rgba(0,0,0,0.03);
  z-index: 10;
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
}
</style> 