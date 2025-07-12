<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import ePub from 'epubjs'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faChevronLeft, faChevronRight } from '@fortawesome/free-solid-svg-icons'
import UserLayout from '@/Layouts/UserLayout.vue'
import { Inertia } from '@inertiajs/inertia'
// import { useRouter } from 'vue-router'

defineOptions({ layout: UserLayout })

library.add(faChevronLeft, faChevronRight)

const props = defineProps({
  url: { type: String, required: true },
  startPercent: { type: Number, default: 0 },
  book: { type: Object, default: null },
  lastPercent: { type: Number, default: 0 }
})

const viewer = ref(null)
let book = null
let rendition = null
const emit = defineEmits(['update:percent'])
const currentPage = ref(1)
const totalPages = ref(1)
const prevBtn = ref(null);
const nextBtn = ref(null);
const loading = ref(true);

function displayFirstContent() {
  book.ready.then(() => {
    const firstSpine = book.spine.spineItems.find(
      item =>
        !item.properties.includes('cover-image') &&
        !item.idref.toLowerCase().includes('cover') &&
        item.linear !== 'no'
    );
    if (firstSpine) {
      rendition.display(firstSpine.href);
    } else {
      const fallback = book.spine.spineItems.find(item => item.linear !== 'no');
      fallback ? rendition.display(fallback.href) : rendition.display();
    }
  });
}

function nextPage() {
  if (rendition) rendition.next();
}
function prevPage() {
  if (rendition) rendition.prev();
}

function handleRelocated(location) {
  if (book && book.locations && location && location.start?.cfi) {
    const percent = book.locations.percentageFromCfi(location.start.cfi)
    emit('update:percent', percent)
    if (book.locations.length() > 0) {
      currentPage.value = book.locations.locationFromCfi(location.start.cfi)
      totalPages.value = book.locations.length()
    }
  }
}

function handlePrevClick(event) {
  prevPage();
  event.currentTarget.blur();
}

function handleNextClick(event) {
  nextPage();
  event.currentTarget.blur();
}

function handleKeydown(event) {
  if (event.key === 'ArrowLeft') prevPage();
  if (event.key === 'ArrowRight') nextPage();
}

function goBack() {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    Inertia.visit('/user/books');
  }
}

onMounted(() => {
  book = ePub(props.url || props.book?.ebook_file)
  rendition = book.renderTo(viewer.value, {
    width: '100%',
    height: window.innerWidth < 640 ? Math.floor(window.innerHeight * 0.8) : 600
  })

  // MutationObserver to patch iframe sandbox
  const observer = new MutationObserver(() => {
    const iframes = viewer.value?.getElementsByTagName('iframe') || [];
    for (const iframe of iframes) {
      iframe.setAttribute('sandbox', 'allow-scripts allow-same-origin');
    }
  });
  if (viewer.value) {
    observer.observe(viewer.value, { childList: true, subtree: true });
  }
  book.ready
    .then(() => book.locations.generate(1000))
    .then(() => {
      rendition.on('relocated', handleRelocated)
      const percent = props.startPercent || props.lastPercent || 0;
      if (percent > 0 && percent < 1) {
        const cfi = book.locations.cfiFromPercentage(percent)
        rendition.display(cfi)
      } else {
        displayFirstContent()
      }
      loading.value = false;
      setTimeout(() => {
        if (viewer.value) viewer.value.classList.add('fade-in');
      }, 10);
    })
  if (viewer.value) {
    viewer.value.setAttribute('tabindex', '0');
    viewer.value.addEventListener('keydown', handleKeydown);
  }
})

onBeforeUnmount(() => {
  if (rendition) rendition.destroy()
  if (book) book.destroy()
  if (viewer.value) viewer.value.removeEventListener('keydown', handleKeydown);
})

watch(() => props.url, (newUrl) => {
  if (book && newUrl) {
    book.destroy()
    book = ePub(newUrl)
    rendition = book.renderTo(viewer.value, {
      width: '100%',
      height: 600
    })
    displayFirstContent()
  }
})
</script>

<template>
    <Head :title="props.book?.title || 'Book'" />
  <!-- Back Button OUTSIDE the frame -->
  <button
    class="absolute left-4 top-16 z-50 pointer-events-auto bg-white bg-opacity-90 hover:bg-green-100 text-green-700 font-bold py-2 px-3 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent transition-all duration-200 flex items-center gap-2"
    @click="goBack"
    aria-label="Back"
    tabindex="0"
    style="min-width: 44px; min-height: 44px;"
  >
    <font-awesome-icon icon="chevron-left" />
    <span class="hidden sm:inline">Back</span>
  </button>
  <div class="relative">
    <!-- Loading Spinner -->
    <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-70 z-20">
      <svg class="animate-spin h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
      </svg>
    </div>
    <!-- Left (Previous) Button -->
    <button
      ref="prevBtn"
      class="absolute left-2 bottom-14 z-10
             bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold
             py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent
             sm:left-2 sm:top-1/2 sm:bottom-auto sm:py-3 sm:px-4 sm:text-xl
             transition-all duration-200"
      @click="handlePrevClick"
      aria-label="Previous Page"
      tabindex="0"
    >
      <font-awesome-icon icon="chevron-left" />
    </button>

    <!-- The viewer itself -->
    <div ref="viewer" class="border-2 border-green-600 rounded-xl overflow-hidden bg-green-50 opacity-0 transition-opacity duration-500 min-h-[400px] sm:min-h-[600px]" @focus="viewer.value && viewer.value.focus()"></div>

    <!-- Right (Next) Button -->
    <button
      ref="nextBtn"
      class="absolute right-2 bottom-14 z-10
             bg-white bg-opacity-60 hover:bg-green-100 text-green-700 font-bold
             py-2 px-2 text-lg rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent
             sm:right-2 sm:top-1/2 sm:bottom-auto sm:py-3 sm:px-4 sm:text-xl
             transition-all duration-200"
      @click="handleNextClick"
      aria-label="Next Page"
      tabindex="0"
    >
      <font-awesome-icon icon="chevron-right" />
    </button>

    <!-- Page indicator -->
    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 bg-white bg-opacity-80 px-3 py-1 rounded shadow text-green-700 text-sm font-semibold z-20"
         aria-live="polite">
      Page {{ currentPage }} / {{ totalPages }}
    </div>
  </div>
</template>

<style scoped>
.fade-in {
  opacity: 1 !important;
}
[ref="viewer"] {
  opacity: 0;
}
</style> 