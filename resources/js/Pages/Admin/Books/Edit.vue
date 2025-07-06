<template>
  <Head title="Edit Book" />
  <div class="max-w-7xl mx-auto py-8 px-4">
    <div class="flex items-center gap-3 mb-6">
      <Link :href="route('admin.books.index')" class="btn-outline flex items-center gap-2">
        <font-awesome-icon icon="arrow-left" /> Back
      </Link>
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="pencil-alt" /> Edit Book
      </h1>
    </div>

    <form @submit.prevent="submit" enctype="multipart/form-data" class="bg-white border-2 border-green-600 rounded-2xl shadow-md p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Title -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="book" /> Title</label>
          <input v-model="form.title" type="text" class="form-input" />
          <p v-if="form.errors.title" class="text-red-600 text-sm">{{ form.errors.title }}</p>
        </div>

        <!-- Author -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="user" /> Author</label>
          <input v-model="form.author" type="text" class="form-input" />
          <p v-if="form.errors.author" class="text-red-600 text-sm">{{ form.errors.author }}</p>
        </div>

        <!-- ISBN -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="barcode" /> ISBN</label>
          <input v-model="form.isbn" type="text" class="form-input" />
          <p v-if="form.errors.isbn" class="text-red-600 text-sm">{{ form.errors.isbn }}</p>
        </div>

        <!-- Published Year -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="calendar" /> Published Year</label>
          <input v-model="form.published_year" type="number" class="form-input" />
          <p v-if="form.errors.published_year" class="text-red-600 text-sm">{{ form.errors.published_year }}</p>
        </div>

        <!-- Publisher -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="building" /> Publisher</label>
          <input v-model="form.publisher" type="text" class="form-input" />
          <p v-if="form.errors.publisher" class="text-red-600 text-sm">{{ form.errors.publisher }}</p>
        </div>

        <!-- Language -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="language" /> Language</label>
          <input v-model="form.language" type="text" class="form-input" />
          <p v-if="form.errors.language" class="text-red-600 text-sm">{{ form.errors.language }}</p>
        </div>

        <!-- Category -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="tags" /> Category</label>
          <select v-model="form.category_id" class="form-select">
            <option value="">Select Category</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <p v-if="form.errors.category_id" class="text-red-600 text-sm">{{ form.errors.category_id }}</p>
        </div>

        <!-- Status -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="info-circle" /> Status</label>
          <select v-model="form.status" class="form-select">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <p v-if="form.errors.status" class="text-red-600 text-sm">{{ form.errors.status }}</p>
        </div>

        <!-- Description -->
        <div class="md:col-span-2">
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="align-left" /> Description</label>
          <textarea v-model="form.description" rows="3" class="form-textarea"></textarea>
          <p v-if="form.errors.description" class="text-red-600 text-sm">{{ form.errors.description }}</p>
        </div>

        <!-- Cover Image -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="image" /> Cover Image</label>
          <input type="file" @change="onCoverChange" accept="image/*" class="form-input" />
          <p v-if="form.errors.cover_image" class="text-red-600 text-sm">{{ form.errors.cover_image }}</p>
          <div v-if="coverPreview" class="mt-2">
            <img :src="coverPreview" alt="Cover Preview" class="w-20 h-20 object-cover rounded border border-green-600" />
          </div>
        </div>

        <!-- Ebook File -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="file" /> Ebook File</label>
          <input type="file" @change="onEbookChange" accept=".pdf,.epub,.mobi,.txt,.docx,.azw3,.fb2,.djvu,.rtf,.html,.htm" class="form-input" />
          <p v-if="ebookName" class="text-sm text-gray-600 mt-1">Selected: {{ ebookName }}</p>
          <p v-if="form.errors.ebook_file" class="text-red-600 text-sm">{{ form.errors.ebook_file }}</p>
          <div v-if="book.ebook_file && !ebookName" class="mt-2">
            <a :href="book.ebook_file" target="_blank" class="text-blue-600 underline text-sm">Current Ebook</a>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-primary flex items-center gap-2 justify-center" :disabled="form.processing">
          <font-awesome-icon icon="save" /> {{ form.processing ? 'Saving...' : 'Update Book' }}
        </button>
        <Link :href="route('admin.books.index')" class="btn-outline flex items-center gap-2">
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
  @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500;
}
.form-select {
  @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500;
}
.form-textarea {
  @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500;
}
</style>