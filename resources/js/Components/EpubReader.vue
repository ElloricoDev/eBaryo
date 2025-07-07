<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import ePub from 'epubjs'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faChevronLeft, faChevronRight } from '@fortawesome/free-solid-svg-icons'

library.add(faChevronLeft, faChevronRight)

const props = defineProps({
  url: { type: String, required: true },
  startPercent: { type: Number, default: 0 }
})

const viewer = ref(null)
let book = null
let rendition = null
const emit = defineEmits(['update:percent'])
const currentPage = ref(1)
const totalPages = ref(1)
const prevBtn = ref(null);
const nextBtn = ref(null);

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

onMounted(() => {
  book = ePub(props.url)
  rendition = book.renderTo(viewer.value, {
    width: '100%',
    height: 600
  })
  book.ready
    .then(() => book.locations.generate(1000))
    .then(() => {
      rendition.on('relocated', handleRelocated)
      if (props.startPercent > 0 && props.startPercent < 1) {
        const cfi = book.locations.cfiFromPercentage(props.startPercent)
        rendition.display(cfi)
      } else {
        displayFirstContent()
      }
    })
})

onBeforeUnmount(() => {
  if (rendition) rendition.destroy()
  if (book) book.destroy()
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
  <div class="relative">
    <!-- Left (Previous) Button -->
    <button
      ref="prevBtn"
      class="absolute left-0 top-1/2 -translate-y-1/2 z-10
             bg-white bg-opacity-80 hover:bg-green-100 text-green-700 font-bold
             py-3 px-4 text-xl rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent
             sm:px-3 sm:py-2 sm:text-base
             -ml-6 sm:ml-0"
      @click="handlePrevClick"
      aria-label="Previous Page"
    >
      <font-awesome-icon icon="chevron-left" />
    </button>

    <!-- The viewer itself -->
    <div ref="viewer" class="border-2 border-green-600 rounded-xl overflow-hidden bg-green-50"></div>

    <!-- Right (Next) Button -->
    <button
      ref="nextBtn"
      class="absolute right-0 top-1/2 -translate-y-1/2 z-10
             bg-white bg-opacity-80 hover:bg-green-100 text-green-700 font-bold
             py-3 px-4 text-xl rounded-full shadow focus:outline-none focus:ring-0 focus:ring-transparent
             sm:px-3 sm:py-2 sm:text-base
             -mr-6 sm:mr-0"
      @click="handleNextClick"
      aria-label="Next Page"
    >
      <font-awesome-icon icon="chevron-right" />
    </button>

    <!-- Page indicator -->
    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 bg-white bg-opacity-80 px-3 py-1 rounded shadow text-green-700 text-sm font-semibold">
      Page {{ currentPage }} / {{ totalPages }}
    </div>
  </div>
</template>
