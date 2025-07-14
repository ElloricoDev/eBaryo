<script setup>
import { usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, watch, onBeforeUnmount } from 'vue';
import ePub from 'epubjs';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faArrowLeft } from '@fortawesome/free-solid-svg-icons';

library.add(faArrowLeft);

const { props } = usePage();
const book = props.book;


const viewer = ref(null);
const epubBook = ref(null);
const rendition = ref(null);
const progress = ref(0); // percentage read
const fontSize = ref(18);
const canPrev = ref(true);
const canNext = ref(true);

onMounted(() => {
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
    epubBook.value.ready.then(() => {
      rendition.value.display();
    });
    rendition.value.on('relocated', (location) => {
      progress.value = Math.round(location.start.percentage * 100);
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

const updateNav = (location) => {
  canPrev.value = !location.atStart;
  canNext.value = !location.atEnd;
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
    <div class="epub-controls">
      <button @click="prevPage" :disabled="!canPrev">Prev</button>
      <span>Read: {{ progress }}%</span>
      <button @click="nextPage" :disabled="!canNext">Next</button>
      <button @click="zoomOut">A-</button>
      <span>Font: {{ fontSize }}px</span>
      <button @click="zoomIn">A+</button>
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
.epub-controls button {
  background: #16a34a;
  color: #fff;
  border: none;
  border-radius: 0.375rem;
  padding: 0.25rem 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}
.epub-controls button:disabled {
  background: #d1d5db;
  color: #6b7280;
  cursor: not-allowed;
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