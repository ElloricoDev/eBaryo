<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, usePage, Link, router, Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faArrowLeft, faPlusSquare, faBook, faUser, faBarcode, faCalendar, faBuilding, faLanguage, faTags, faInfoCircle, faAlignLeft, faImage, faFile, faXmark, faEnvelope
} from '@fortawesome/free-solid-svg-icons'

library.add(faArrowLeft, faPlusSquare, faBook, faUser, faBarcode, faCalendar, faBuilding, faLanguage, faTags, faInfoCircle, faAlignLeft, faImage, faFile, faXmark, faEnvelope)

defineOptions({ layout: AdminLayout })

const { props } = usePage();
const categories = props.categories || [];

const form = useForm({
  title: '',
  author: '',
  isbn: '',
  published_year: '',
  description: '',
  cover_image: null,
  ebook_file: null,
  category_id: '',
  publisher: '',
  language: '',
  status: 'active',
});

const coverPreview = ref(null);
const ebookName = ref('');

const onCoverChange = (e) => {
  const file = e.target.files[0];
  form.cover_image = file;
  coverPreview.value = file ? URL.createObjectURL(file) : null;
};

const onEbookChange = (e) => {
  const file = e.target.files[0];
  if (file && file.size > 800 * 1024 * 1024) {
    Swal.fire({ icon: 'error', title: 'File too large', text: 'The ebook file must be 800MB or less.' });
    form.ebook_file = null;
    ebookName.value = '';
    e.target.value = '';
    return;
  }
  form.ebook_file = file;
  ebookName.value = file ? file.name : '';
};

const submit = () => {
  form.post(route('admin.books.store'), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({ icon: 'success', title: 'Book Created', text: 'The book has been added successfully.', timer: 2000, showConfirmButton: false });
      router.visit(route('admin.books.index'));
    },
  });
};
</script>

<template>
  <Head title="Create Book" />

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
        <font-awesome-icon icon="plus" /> Add Book
      </h1>
    </div>

    <form @submit.prevent="submit" enctype="multipart/form-data" class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 rounded-2xl p-8 shadow-2xl">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="relative">
          <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="book" /> Title</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="book" /></span>
          <input v-model="form.title" type="text" class="input-field pl-10" />
          <p v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</p>
        </div>

        <div class="relative">
          <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="user" /> Author</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="user" /></span>
          <input v-model="form.author" type="text" class="input-field pl-10" />
          <p v-if="form.errors.author" class="text-red-600 text-sm mt-1">{{ form.errors.author }}</p>
        </div>

        <div class="relative">
          <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="barcode" /> ISBN</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="barcode" /></span>
          <input v-model="form.isbn" type="text" class="input-field pl-10" />
          <p v-if="form.errors.isbn" class="text-red-600 text-sm mt-1">{{ form.errors.isbn }}</p>
        </div>

        <div class="relative">
          <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="calendar" /> Published Year</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="calendar" /></span>
          <input v-model="form.published_year" type="number" class="input-field pl-10" />
          <p v-if="form.errors.published_year" class="text-red-600 text-sm mt-1">{{ form.errors.published_year }}</p>
        </div>

        <div class="relative">
          <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="building" /> Publisher</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="building" /></span>
          <input v-model="form.publisher" type="text" class="input-field pl-10" />
          <p v-if="form.errors.publisher" class="text-red-600 text-sm mt-1">{{ form.errors.publisher }}</p>
        </div>

        <div class="relative">
          <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="language" /> Language</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="language" /></span>
          <input v-model="form.language" type="text" class="input-field pl-10" />
          <p v-if="form.errors.language" class="text-red-600 text-sm mt-1">{{ form.errors.language }}</p>
        </div>

        <div class="relative">
          <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="tags" /> Category</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="tags" /></span>
          <select v-model="form.category_id" class="input-field pl-10">
            <option value="">Select Category</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <p v-if="form.errors.category_id" class="text-red-600 text-sm mt-1">{{ form.errors.category_id }}</p>
        </div>

        <div class="relative">
          <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="info-circle" /> Status</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="info-circle" /></span>
          <select v-model="form.status" class="input-field pl-10">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <p v-if="form.errors.status" class="text-red-600 text-sm mt-1">{{ form.errors.status }}</p>
        </div>

        <div class="md:col-span-2 relative">
          <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="align-left" /> Description</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="align-left" /></span>
          <textarea v-model="form.description" rows="4" class="input-field pl-10"></textarea>
          <p v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</p>
        </div>

        <div>
          <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="image" /> Cover Image</label>
          <div class="relative">
            <input type="file" class="input-field file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200 transition-all duration-150" @change="onCoverChange" accept="image/*" />
          </div>
          <p v-if="form.errors.cover_image" class="text-red-600 text-sm mt-1">{{ form.errors.cover_image }}</p>
          <div v-if="coverPreview" class="mt-2">
            <img :src="coverPreview" alt="Cover Preview" class="w-20 h-20 object-cover rounded border border-green-600" />
          </div>
        </div>

        <div>
          <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="file" /> Ebook File</label>
          <div class="relative">
            <input type="file" class="input-field file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200 transition-all duration-150" @change="onEbookChange" accept=".pdf,.epub,.mobi,.txt,.docx,.azw3,.fb2,.djvu,.rtf,.html,.htm" />
          </div>
          <p v-if="ebookName" class="text-sm text-gray-500 mt-1">Selected: {{ ebookName }}</p>
          <p v-if="form.errors.ebook_file" class="text-red-600 text-sm mt-1">{{ form.errors.ebook_file }}</p>
        </div>
      </div>

      <div class="mt-8 flex gap-4">
        <button type="submit" class="bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-bold px-8 py-3 rounded-full shadow-xl transition-transform duration-200 inline-flex items-center gap-2 justify-center text-lg hover:scale-105" :disabled="form.processing">
          <font-awesome-icon icon="plus-square" /> {{ form.processing ? 'Saving...' : 'Add Book' }}
        </button>
        <Link :href="route('admin.books.index')" class="px-8 py-3 border border-green-600 text-green-700 hover:bg-green-600 hover:text-white font-semibold rounded-full shadow-sm transition flex items-center gap-2 text-lg">
          <font-awesome-icon icon="xmark" /> Cancel
        </Link>
      </div>
    </form>
  </div>
</template>

<style scoped>
.input-field {
  @apply w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-200 transition-all duration-150;
}
</style>
