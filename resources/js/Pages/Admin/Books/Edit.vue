<template>
  <Head title="Edit Book" />
  <div class="max-w-6xl mx-auto px-4 py-10">
    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center gap-4">
      <Link :href="route('admin.books.index')"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-700 text-white font-bold rounded-full shadow-lg hover:from-green-600 hover:to-green-800 transition-transform duration-200 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 hover:scale-105"
      >
        <font-awesome-icon icon="arrow-left" class="text-lg" />
        <span class="hidden sm:inline">Back to Books</span>
        <span class="sm:hidden">Back</span>
      </Link>
      <h1 class="text-2xl font-bold text-green-600 flex items-center gap-2">
        <font-awesome-icon icon="pencil-alt" /> Edit Book
      </h1>
    </div>

    <form @submit.prevent="submit" enctype="multipart/form-data" class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 rounded-2xl shadow-2xl p-8">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Title -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="book" /> Title</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="book" /></span>
          <input v-model="form.title" type="text" class="form-input pl-10" />
          <p v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</p>
        </div>

        <!-- Author -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="user" /> Author</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="user" /></span>
          <input v-model="form.author" type="text" class="form-input pl-10" />
          <p v-if="form.errors.author" class="text-red-600 text-sm mt-1">{{ form.errors.author }}</p>
        </div>

        <!-- ISBN -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="barcode" /> ISBN</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="barcode" /></span>
          <input v-model="form.isbn" type="text" class="form-input pl-10" />
          <p v-if="form.errors.isbn" class="text-red-600 text-sm mt-1">{{ form.errors.isbn }}</p>
        </div>

        <!-- Published Year -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="calendar" /> Published Year</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="calendar" /></span>
          <input v-model="form.published_year" type="number" class="form-input pl-10" />
          <p v-if="form.errors.published_year" class="text-red-600 text-sm mt-1">{{ form.errors.published_year }}</p>
        </div>

        <!-- Publisher -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="building" /> Publisher</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="building" /></span>
          <input v-model="form.publisher" type="text" class="form-input pl-10" />
          <p v-if="form.errors.publisher" class="text-red-600 text-sm mt-1">{{ form.errors.publisher }}</p>
        </div>

        <!-- Language -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="language" /> Language</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="language" /></span>
          <input v-model="form.language" type="text" class="form-input pl-10" />
          <p v-if="form.errors.language" class="text-red-600 text-sm mt-1">{{ form.errors.language }}</p>
        </div>

        <!-- Category -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="tags" /> Category</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="tags" /></span>
          <select v-model="form.category_id" class="form-select pl-10">
            <option value="">Select Category</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <p v-if="form.errors.category_id" class="text-red-600 text-sm mt-1">{{ form.errors.category_id }}</p>
        </div>

        <!-- Status -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="info-circle" /> Status</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="info-circle" /></span>
          <select v-model="form.status" class="form-select pl-10">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <p v-if="form.errors.status" class="text-red-600 text-sm mt-1">{{ form.errors.status }}</p>
        </div>

        <!-- Description -->
        <div class="md:col-span-2 relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="align-left" /> Description</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="align-left" /></span>
          <textarea v-model="form.description" rows="3" class="form-textarea pl-10"></textarea>
          <p v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</p>
        </div>

        <!-- Cover Image -->
        <div>
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="image" /> Cover Image</label>
          <div class="relative">
            <input type="file" @change="onCoverChange" accept="image/*" class="form-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200 transition-all duration-150" />
          </div>
          <p v-if="form.errors.cover_image" class="text-red-600 text-sm mt-1">{{ form.errors.cover_image }}</p>
          <div v-if="coverPreview" class="mt-2">
            <img :src="coverPreview" alt="Cover Preview" class="w-20 h-20 object-cover rounded border border-green-600" />
          </div>
        </div>

        <!-- Ebook File -->
        <div>
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="file" /> Ebook File</label>
          <div class="relative">
            <input type="file" @change="onEbookChange" accept=".pdf,.epub,.mobi,.txt,.docx,.azw3,.fb2,.djvu,.rtf,.html,.htm" class="form-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200 transition-all duration-150" />
          </div>
          <p v-if="ebookName" class="text-sm text-gray-600 mt-1">Selected: {{ ebookName }}</p>
          <p v-if="form.errors.ebook_file" class="text-red-600 text-sm mt-1">{{ form.errors.ebook_file }}</p>
          <div v-if="book.ebook_file && !ebookName" class="mt-2">
            <a :href="book.ebook_file" target="_blank" class="text-blue-600 underline text-sm">Current Ebook</a>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="mt-8 flex gap-4">
        <button type="submit" class="bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-bold px-8 py-3 rounded-full shadow-xl transition-transform duration-200 inline-flex items-center gap-2 justify-center text-lg hover:scale-105" :disabled="form.processing">
          <font-awesome-icon icon="save" /> {{ form.processing ? 'Saving...' : 'Update Book' }}
        </button>
        <Link :href="route('admin.books.index')" class="px-8 py-3 border border-green-600 text-green-700 hover:bg-green-600 hover:text-white font-semibold rounded-full shadow-sm transition flex items-center gap-2 text-lg">
          <font-awesome-icon icon="xmark" /> Cancel
        </Link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faArrowLeft, faPencilAlt, faBook, faUser, faBarcode, faCalendar, faBuilding, faLanguage, faTags, faInfoCircle, faAlignLeft, faImage, faFile, faSave, faXmark
} from '@fortawesome/free-solid-svg-icons'

library.add(faArrowLeft, faPencilAlt, faBook, faUser, faBarcode, faCalendar, faBuilding, faLanguage, faTags, faInfoCircle, faAlignLeft, faImage, faFile, faSave, faXmark)

import { useForm, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2';
import { ref } from 'vue';

defineOptions({ layout: AdminLayout })

const props = defineProps({
  book: {
    type: Object,
    default: () => ({})
  },
  categories: {
    type: Array,
    default: () => []
  }
})

const form = useForm({
  title: props.book?.title || '',
  author: props.book?.author || '',
  isbn: props.book?.isbn || '',
  published_year: props.book?.published_year || '',
  description: props.book?.description || '',
  cover_image: null,
  ebook_file: null,
  category_id: props.book?.category_id || '',
  publisher: props.book?.publisher || '',
  language: props.book?.language || '',
  status: props.book?.status || 'active',
})

const coverPreview = ref(props.book?.cover_image || null)
const ebookName = ref('')

const onCoverChange = (e) => {
  const file = e.target.files[0]
  form.cover_image = file
  coverPreview.value = file ? URL.createObjectURL(file) : (props.book?.cover_image || null)
}

const onEbookChange = (e) => {
  const file = e.target.files[0]
  form.ebook_file = file
  ebookName.value = file ? file.name : ''
}

const submit = () => {
  form.post(route('admin.books.update', { id: props.book?.id }), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({ icon: 'success', title: 'Book Updated', text: 'The book has been updated successfully.', timer: 2000, showConfirmButton: false });
    },
  })
}
</script>

<style scoped>
.btn-primary {
  @apply bg-green-600 text-white font-semibold px-4 py-2 rounded-full shadow hover:bg-green-700 transition;
}
.btn-outline {
  @apply border border-green-600 text-green-600 font-semibold px-4 py-2 rounded-full hover:bg-green-100 transition;
}
.form-input {
  @apply w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-200 transition-all duration-150;
}
.form-select {
  @apply w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-200 transition-all duration-150;
}
.form-textarea {
  @apply w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-200 transition-all duration-150;
}
</style>