<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faExclamationTriangle, faArrowRotateRight, faDownload, faChevronLeft, faChevronRight, faMagnifyingGlassPlus, faMagnifyingGlassMinus, faSpinner } from '@fortawesome/free-solid-svg-icons'

library.add(faExclamationTriangle, faArrowRotateRight, faDownload, faChevronLeft, faChevronRight, faMagnifyingGlassPlus, faMagnifyingGlassMinus, faSpinner)

// Use window.route for generating URLs (Laravel's route helper)
const route = window.route || ((name, params) => {
  // Fallback if route helper is not available
  if (name === 'files.serve') {
    return `/files/${params.filename}`
  }
  if (name === 'test.pdf') {
    return `/test-pdf/${params.filename}`
  }
  return '/'
})

const props = defineProps({
  url: { type: String, required: true },
  startPercent: { type: Number, default: 0 }
})

const viewer = ref(null)
const pdfDoc = ref(null)
const pageNum = ref(1)
const numPages = ref(1)
const scale = ref(1.5)
const canvas = ref(null)
const ctx = ref(null)
const loading = ref(false)
const error = ref(null)
const isUnmounting = ref(false)
const isDestroyed = ref(false)
const emit = defineEmits(['update:percent'])

// Load PDF.js dynamically
let pdfjsLib = null

async function loadPdfJs() {
  if (!pdfjsLib) {
    try {
          const pdfjsModule = await import('pdfjs-dist')
    pdfjsLib = pdfjsModule.default || pdfjsModule
      
      // Try local worker file first, fallback to disabled worker if it fails
      try {
        // Test if worker file is accessible
        const response = await fetch('/pdf.worker.min.js')
        if (response.ok) {
                pdfjsLib.GlobalWorkerOptions.workerSrc = '/pdf.worker.min.js'
    } else {
      throw new Error('Worker file not accessible')
    }
  } catch (error) {
    pdfjsLib.GlobalWorkerOptions.workerSrc = false
  }
} catch (importError) {
  throw new Error('Failed to load PDF.js library')
}
  }
  return pdfjsLib
}

function clearPdfJsCache() {
  pdfjsLib = null
}

function cleanupPdfDoc() {
  isUnmounting.value = true
  isDestroyed.value = true
  
  // Simply set pdfDoc to null and let garbage collection handle the cleanup
  // This avoids any private field access issues
  pdfDoc.value = null
}

function retryLoadPDF() {
  if (isDestroyed.value) return
  
  const currentError = error.value
  error.value = null
  if (currentError && (currentError.includes('version mismatch') || currentError.includes('worker'))) {
    clearPdfJsCache()
  }
  loadPDF()
}

async function checkFileExists(filename) {
  try {
    const testUrl = route('test.pdf', { filename })
    const response = await fetch(testUrl)
    return response.ok
  } catch (error) {
    return false
  }
}

async function renderPage(pageNumber) {
  if (!pdfDoc.value || !canvas.value || !ctx.value || isUnmounting.value || isDestroyed.value) return
  
  try {
    // Additional safety check for PDF document state
    if (!pdfDoc.value || typeof pdfDoc.value.getPage !== 'function') {
      throw new Error('PDF document getPage method not available')
    }
    
    const page = await pdfDoc.value.getPage(pageNumber)
    
    if (!page || typeof page.getViewport !== 'function') {
      throw new Error('PDF page getViewport method not available')
    }
    
    const viewport = page.getViewport({ scale: scale.value })
    
    // Set canvas dimensions
    canvas.value.height = viewport.height
    canvas.value.width = viewport.width
    
    // Render PDF page to canvas
    const renderContext = {
      canvasContext: ctx.value,
      viewport: viewport
    }
    
    await page.render(renderContext).promise
    
    // Update page info
    pageNum.value = pageNumber
    
    // Safely access numPages
    if (pdfDoc.value && typeof pdfDoc.value.numPages === 'number') {
      numPages.value = pdfDoc.value.numPages
      
      // Calculate and emit progress
      const percent = (pageNumber - 1) / (numPages.value - 1)
      emit('update:percent', percent)
    }
    
  } catch (err) {
    error.value = 'Failed to render PDF page: ' + (err.message || 'Unknown error')
  }
}

async function loadPDF() {
  if (isUnmounting.value || isDestroyed.value) return
  
  // Declare variables outside try block to fix scope issues
  let pdfUrl = props.url
  let useFileServingRoute = false
  let filename = null
  
  try {
    loading.value = true
    error.value = null
    
    if (pdfUrl.startsWith('/storage/ebooks/')) {
      filename = pdfUrl.replace('/storage/ebooks/', '')
      
      // Check if file exists first
      const fileExists = await checkFileExists(filename)
      if (!fileExists) {
        error.value = 'PDF file not found on server'
        return
      }
      
      const fileUrl = route('files.serve', { filename })
      pdfUrl = fileUrl
      useFileServingRoute = true
    }
    
    const pdfjs = await loadPdfJs()
    
    if (!pdfjs || !pdfjs.getDocument) {
      throw new Error('PDF.js library not properly loaded')
    }
    
    const loadingTask = pdfjs.getDocument(pdfUrl)
    
    if (!loadingTask || !loadingTask.promise) {
      throw new Error('Failed to create PDF loading task')
    }
    
    pdfDoc.value = await loadingTask.promise
    
    // Start from saved position or first page
    let startPage = 1
    if (props.startPercent > 0 && props.startPercent < 1 && pdfDoc.value && typeof pdfDoc.value.numPages === 'number') {
      startPage = Math.floor(props.startPercent * pdfDoc.value.numPages) + 1
    }
    
    await renderPage(startPage)
  } catch (err) {
    
    // Try fallback to direct URL if file serving route failed
    if (useFileServingRoute) {
      try {
        const pdfjs = await loadPdfJs()
        
        if (!pdfjs || !pdfjs.getDocument) {
          throw new Error('PDF.js library not properly loaded (fallback)')
        }
        
        const loadingTask = pdfjs.getDocument(props.url)
        
        if (!loadingTask || !loadingTask.promise) {
          throw new Error('Failed to create PDF loading task (fallback)')
        }
        
        pdfDoc.value = await loadingTask.promise
        
        let startPage = 1
        if (props.startPercent > 0 && props.startPercent < 1 && pdfDoc.value && typeof pdfDoc.value.numPages === 'number') {
          startPage = Math.floor(props.startPercent * pdfDoc.value.numPages) + 1
        }
        
        await renderPage(startPage)
        return
      } catch (fallbackErr) {
        // Handle specific PDF.js errors in fallback
        if (fallbackErr.name === 'InvalidPDFException') {
          error.value = 'Invalid PDF file format'
        } else if (fallbackErr.name === 'MissingPDFException') {
          error.value = 'PDF file not found or inaccessible'
        } else if (fallbackErr.name === 'UnexpectedResponseException') {
          error.value = 'Server error while loading PDF'
        } else if (fallbackErr.name === 'UnknownErrorException') {
          if (fallbackErr.message.includes('API version') && fallbackErr.message.includes('Worker version')) {
            error.value = 'PDF.js version mismatch. Please click "Retry" to reload the PDF viewer.'
          } else if (fallbackErr.message.includes('fake worker') || fallbackErr.message.includes('worker')) {
            error.value = 'PDF viewer worker error. Please click "Retry" to reload the PDF viewer.'
          } else {
            error.value = 'Unknown error occurred while loading PDF. Please try again or download the file.'
          }
        } else {
          error.value = fallbackErr.message || 'Failed to load PDF file'
        }
      }
    }
    
    // Handle specific PDF.js errors
    if (err.name === 'InvalidPDFException') {
      error.value = 'Invalid PDF file format'
    } else if (err.name === 'MissingPDFException') {
      error.value = 'PDF file not found or inaccessible'
    } else if (err.name === 'UnexpectedResponseException') {
      error.value = 'Server error while loading PDF'
    } else if (err.name === 'UnknownErrorException') {
      if (err.message.includes('API version') && err.message.includes('Worker version')) {
        error.value = 'PDF.js version mismatch. Please click "Retry" to reload the PDF viewer.'
      } else if (err.message.includes('fake worker') || err.message.includes('worker')) {
        error.value = 'PDF viewer worker error. Please click "Retry" to reload the PDF viewer.'
      } else {
        error.value = 'Unknown error occurred while loading PDF. Please try again or download the file.'
      }
    } else {
      error.value = err.message || 'Failed to load PDF file'
    }
  } finally {
    loading.value = false
  }
}

function nextPage() {
  if (pageNum.value < numPages.value && !isDestroyed.value) {
    renderPage(pageNum.value + 1)
  }
}

function prevPage() {
  if (pageNum.value > 1 && !isDestroyed.value) {
    renderPage(pageNum.value - 1)
  }
}

function zoomIn() {
  if (scale.value < 3 && !isDestroyed.value) {
    scale.value += 0.2
    renderPage(pageNum.value)
  }
}

function zoomOut() {
  if (scale.value > 0.5 && !isDestroyed.value) {
    scale.value -= 0.2
    renderPage(pageNum.value)
  }
}

onMounted(async () => {
  if (canvas.value) {
    ctx.value = canvas.value.getContext('2d')
    await loadPDF()
  }
})

onBeforeUnmount(() => {
  cleanupPdfDoc()
})

watch(() => props.url, async (newUrl) => {
  if (newUrl) {
    cleanupPdfDoc()
    await loadPDF()
  }
})

watch(() => props.startPercent, async (newPercent) => {
  if (pdfDoc.value && newPercent > 0 && newPercent < 1 && !isUnmounting.value && !isDestroyed.value) {
    try {
      // Check if numPages is accessible before using it
      if (pdfDoc.value.numPages && typeof pdfDoc.value.numPages === 'number') {
        const startPage = Math.floor(newPercent * pdfDoc.value.numPages) + 1
        await renderPage(startPage)
      }
    } catch (error) {
      // Handle errors when PDF document is being destroyed
    }
  }
})
</script>

<template>
  <div>
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-10">
      <font-awesome-icon icon="spinner" spin class="text-green-500 text-5xl mx-auto" />
      <p class="text-gray-500 mt-2">Loading PDF...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-10">
      <font-awesome-icon icon="exclamation-triangle" class="text-yellow-500 text-5xl" />
      <h5 class="text-yellow-600 mt-3 text-lg font-semibold">Error Loading PDF</h5>
      <p class="text-gray-500">{{ error }}</p>
      <div class="flex gap-3 justify-center mt-4 flex-wrap">
        <button class="btn border border-green-600 text-green-600 px-4 py-2 rounded hover:bg-green-50" @click="retryLoadPDF">
          <font-awesome-icon icon="arrow-rotate-right" /> Retry
        </button>
        <a :href="props.url" target="_blank" class="btn border border-blue-600 text-blue-600 px-4 py-2 rounded hover:bg-blue-50">
          <font-awesome-icon icon="download" /> Download PDF
        </a>
      </div>
    </div>

    <!-- PDF Viewer -->
    <div v-else-if="!isDestroyed">
      <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
        <div class="flex gap-2 flex-wrap">
          <button class="btn border border-green-600 text-green-600 px-3 py-1 text-sm rounded hover:bg-green-50" @click="prevPage" :disabled="pageNum <= 1">
            <font-awesome-icon icon="chevron-left" /> Previous
          </button>
          <button class="btn border border-gray-400 text-gray-600 px-3 py-1 text-sm rounded hover:bg-gray-100" @click="zoomOut" :disabled="scale <= 0.5">
            <font-awesome-icon icon="magnifying-glass-minus" />
          </button>
          <button class="btn border border-gray-400 text-gray-600 px-3 py-1 text-sm rounded hover:bg-gray-100" @click="zoomIn" :disabled="scale >= 3">
            <font-awesome-icon icon="magnifying-glass-plus" />
          </button>
        </div>
        <span class="text-green-700 text-sm font-semibold">
          Page {{ pageNum }} / {{ numPages }}
          <span class="text-gray-400">({{ Math.round(scale * 100) }}%)</span>
        </span>
        <button class="btn border border-green-600 text-green-600 px-3 py-1 text-sm rounded hover:bg-green-50" @click="nextPage" :disabled="pageNum >= numPages">
          Next <font-awesome-icon icon="chevron-right" />
        </button>
      </div>
      <div class="border-2 border-green-600 rounded-xl overflow-auto bg-green-50 max-h-[600px] flex justify-center p-4">
        <canvas ref="canvas" class="rounded shadow bg-white"></canvas>
      </div>
    </div>

    <!-- Component Destroyed State -->
    <div v-else class="text-center py-10">
      <font-awesome-icon icon="spinner" spin class="text-gray-400 text-5xl mx-auto" />
      <p class="text-gray-500 mt-2">Loading...</p>
    </div>
  </div>
</template>


<style scoped>
.pdf-viewer-green {
  border: 2px solid #198754;
  border-radius: 1rem;
  overflow: auto;
  background: #f6fef6;
  max-height: 600px;
  display: flex;
  justify-content: center;
  padding: 1rem;
}

.pdf-canvas {
  border-radius: 0.5rem;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  background: white;
}
</style> 