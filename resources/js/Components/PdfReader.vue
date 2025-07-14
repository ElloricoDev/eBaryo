<template>
  <div :class="['w-full h-full flex flex-col', { 'dark': isDark }]">
    <div v-if="!url" class="flex-1 flex items-center justify-center text-gray-500">
      <fa icon="file-pdf" class="text-4xl mr-2" /> PDF file not available.
    </div>
    <div v-else class="flex-1 overflow-auto relative pdf-bg" @touchstart="onTouchStart" @touchend="onTouchEnd">
      <!-- Toolbar: top for desktop, bottom for mobile -->
      <div class="pdf-toolbar" :class="{ 'pdf-toolbar-bottom': isMobile }">
        <div class="pdf-toolbar-group">
          <button @click="prevPage" :disabled="pageNum <= 1" :title="'Previous Page'" class="pdf-btn"><fa icon="angle-left" /></button>
          <span class="mx-1 text-xs md:text-base">{{ pageNum }} / {{ numPages }}</span>
          <button @click="nextPage" :disabled="pageNum >= numPages" :title="'Next Page'" class="pdf-btn"><fa icon="angle-right" /></button>
          <input type="number" v-model.number="inputPage" @change="jumpToPage" :min="1" :max="numPages" class="pdf-input" :title="'Jump to Page'" />
        </div>
        <div class="pdf-toolbar-group">
          <button @click="zoomOut" :title="'Zoom Out'" class="pdf-btn"><fa icon="search-minus" /></button>
          <button @click="zoomIn" :title="'Zoom In'" class="pdf-btn"><fa icon="search-plus" /></button>
          <button @click="fitToWidth" :title="'Fit to Width'" class="pdf-btn"><fa icon="arrows-alt-h" /></button>
          <button @click="fitToPage" :title="'Fit to Page'" class="pdf-btn"><fa icon="expand" /></button>
          <button @click="rotate" :title="'Rotate'" class="pdf-btn"><fa icon="redo" /></button>
        </div>
        <div class="pdf-toolbar-group">
          <button @click="download" :title="'Download PDF'" class="pdf-btn"><fa icon="download" /></button>
          <button @click="toggleDark" :title="isDark ? 'Light Mode' : 'Dark Mode'" class="pdf-btn"><fa :icon="isDark ? 'sun' : 'moon'" /></button>
        </div>
      </div>
      <div class="pdf-canvas-container">
        <canvas ref="pdfCanvas" class="pdf-canvas transition-transform duration-200" :style="{ transform: `rotate(${rotation}deg)` }"/>
      </div>
      <transition name="fade">
        <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 dark:bg-gray-900 dark:bg-opacity-80 z-10">
          <fa icon="spinner" spin class="text-4xl text-green-700 dark:text-green-400" />
        </div>
      </transition>
      <transition name="fade">
        <div v-if="error" class="absolute inset-0 flex flex-col items-center justify-center text-red-500 z-10">
          <fa icon="exclamation-triangle" class="text-4xl mb-2" />
          <span class="mb-2">{{ error }}</span>
          <button @click="retry" class="px-4 py-2 bg-white dark:bg-gray-800 rounded shadow hover:bg-gray-100 dark:hover:bg-gray-700 transition">Retry</button>
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue';
import * as pdfjsLib from 'pdfjs-dist/build/pdf';
import { FontAwesomeIcon as Fa } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faAngleLeft, faAngleRight, faSearchMinus, faSearchPlus, faArrowsAltH, faExpand, faRedo, faDownload, faMoon, faSun, faFilePdf, faExclamationTriangle, faSpinner } from '@fortawesome/free-solid-svg-icons';
library.add(faAngleLeft, faAngleRight, faSearchMinus, faSearchPlus, faArrowsAltH, faExpand, faRedo, faDownload, faMoon, faSun, faFilePdf, faExclamationTriangle, faSpinner);

const props = defineProps({
  url: String
});

const pdfCanvas = ref(null);
const loading = ref(false);
const error = ref('');
const scale = ref(1.2);
const pageNum = ref(1);
const numPages = ref(1);
const inputPage = ref(1);
const rotation = ref(0);
const isDark = ref(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
const isMobile = ref(false);
let pdfDoc = null;
let lastFit = 'width';
let touchStartX = 0;

pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.min.js';

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768;
};

onMounted(() => {
  checkMobile();
  window.addEventListener('resize', checkMobile);
});

const renderPage = async (num = 1) => {
  if (!pdfDoc) return;
  loading.value = true;
  error.value = '';
  try {
    const page = await pdfDoc.getPage(num);
    const viewport = page.getViewport({ scale: scale.value, rotation: rotation.value });
    const canvas = pdfCanvas.value;
    const context = canvas.getContext('2d');
    canvas.height = viewport.height;
    canvas.width = viewport.width;
    await page.render({ canvasContext: context, viewport }).promise;
    pageNum.value = num;
    inputPage.value = num;
  } catch (e) {
    error.value = 'Failed to render PDF page.';
  } finally {
    loading.value = false;
  }
};

const fitToWidth = async () => {
  await nextTick();
  const container = pdfCanvas.value.parentElement;
  const page = await pdfDoc.getPage(pageNum.value);
  const viewport = page.getViewport({ scale: 1, rotation: rotation.value });
  const width = container.clientWidth;
  scale.value = width / viewport.width;
  lastFit = 'width';
};
const fitToPage = async () => {
  await nextTick();
  const container = pdfCanvas.value.parentElement;
  const page = await pdfDoc.getPage(pageNum.value);
  const viewport = page.getViewport({ scale: 1, rotation: rotation.value });
  const width = container.clientWidth;
  const height = container.clientHeight;
  scale.value = Math.min(width / viewport.width, height / viewport.height);
  lastFit = 'page';
};

const loadPdf = async () => {
  if (!props.url) return;
  loading.value = true;
  error.value = '';
  pageNum.value = 1;
  inputPage.value = 1;
  scale.value = 1.2;
  rotation.value = 0;
  try {
    pdfDoc = await pdfjsLib.getDocument(props.url).promise;
    numPages.value = pdfDoc.numPages;
    await fitToWidth();
    await renderPage(1);
  } catch (e) {
    error.value = 'Failed to load PDF.';
  } finally {
    loading.value = false;
  }
};
const nextPage = () => {
  if (pageNum.value < numPages.value) renderPage(pageNum.value + 1);
};
const prevPage = () => {
  if (pageNum.value > 1) renderPage(pageNum.value - 1);
};
const jumpToPage = () => {
  if (inputPage.value >= 1 && inputPage.value <= numPages.value) renderPage(inputPage.value);
};
const zoomIn = () => {
  scale.value = Math.min(scale.value + 0.2, 3);
  renderPage(pageNum.value);
};
const zoomOut = () => {
  scale.value = Math.max(scale.value - 0.2, 0.5);
  renderPage(pageNum.value);
};
const rotate = () => {
  rotation.value = (rotation.value + 90) % 360;
  renderPage(pageNum.value);
};
const download = () => {
  const link = document.createElement('a');
  link.href = props.url;
  link.download = props.url.split('/').pop();
  link.click();
};
const retry = () => {
  error.value = '';
  loadPdf();
};
const toggleDark = () => {
  isDark.value = !isDark.value;
};
// Touch swipe for page navigation
const onTouchStart = (e) => {
  if (e.touches && e.touches.length === 1) {
    touchStartX = e.touches[0].clientX;
  }
};
const onTouchEnd = (e) => {
  if (e.changedTouches && e.changedTouches.length === 1) {
    const dx = e.changedTouches[0].clientX - touchStartX;
    if (Math.abs(dx) > 50) {
      if (dx < 0) nextPage();
      else prevPage();
    }
  }
};

watch(() => props.url, loadPdf);
onMounted(loadPdf);
</script>

<style scoped>
.pdf-bg {
  background: #f3f4f6;
  min-height: 0;
  min-width: 0;
  position: relative;
}
.dark .pdf-bg {
  background: #18181b;
}
.pdf-toolbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
  background: rgba(255,255,255,0.98);
  box-shadow: 0 2px 12px rgba(0,0,0,0.10);
  padding: 0.5rem 2rem;
  z-index: 40;
  border-bottom-left-radius: 1.5rem;
  border-bottom-right-radius: 1.5rem;
  transition: background 0.2s, box-shadow 0.2s;
}
.pdf-toolbar-bottom {
  position: absolute;
  top: unset;
  bottom: 1.5rem;
  left: 50%;
  transform: translateX(-50%);
  width: auto;
  border-radius: 2rem;
  box-shadow: 0 2px 12px rgba(0,0,0,0.10);
  padding: 0.5rem 1rem;
  background: rgba(255,255,255,0.95);
}
@media (max-width: 767px) {
  .pdf-toolbar {
    position: absolute;
    top: unset;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    width: auto;
    border-radius: 2rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.10);
    padding: 0.5rem 1rem;
    background: rgba(255,255,255,0.95);
  }
}
.dark .pdf-toolbar, .dark .pdf-toolbar-bottom {
  background: rgba(24,24,27,0.98);
  box-shadow: 0 2px 12px rgba(0,0,0,0.30);
}
.pdf-toolbar-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.pdf-btn {
  background: none;
  border: none;
  padding: 0.6rem;
  border-radius: 50%;
  font-size: 1.3rem;
  color: #222;
  transition: background 0.2s, color 0.2s;
  cursor: pointer;
  outline: none;
  display: flex;
  align-items: center;
  justify-content: center;
}
.pdf-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.pdf-btn:hover:not(:disabled), .pdf-btn:focus:not(:disabled) {
  background: #e5e7eb;
  color: #16a34a;
}
.dark .pdf-btn {
  color: #eee;
}
.dark .pdf-btn:hover:not(:disabled), .dark .pdf-btn:focus:not(:disabled) {
  background: #27272a;
  color: #4ade80;
}
.pdf-input {
  width: 2.5rem;
  padding: 0.2rem 0.3rem;
  border-radius: 0.5rem;
  border: 1px solid #d1d5db;
  text-align: center;
  font-size: 1rem;
  outline: none;
  transition: border 0.2s;
}
.pdf-input:focus {
  border: 1.5px solid #16a34a;
}
.pdf-canvas-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  width: 100%;
  padding-top: 4.5rem;
  padding-bottom: 2rem;
}
@media (max-width: 767px) {
  .pdf-canvas-container {
    padding-top: 1rem;
    padding-bottom: 5.5rem;
  }
}
.pdf-canvas {
  max-width: 100vw;
  max-height: 90vh;
  background: #fff;
  box-shadow: 0 2px 16px rgba(0,0,0,0.10);
  border-radius: 0.5rem;
  transition: background 0.2s, box-shadow 0.2s;
}
.dark .pdf-canvas {
  background: #23272f;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.flex-1 {
  flex: 1 1 0%;
  min-height: 0;
  overflow: auto;
}
</style> 