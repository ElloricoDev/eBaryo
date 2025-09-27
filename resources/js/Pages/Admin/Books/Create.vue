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

  <div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Hero Header -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
            <font-awesome-icon icon="plus" class="w-8 h-8 text-white" />
          </div>
          <div>
            <h1 class="text-3xl font-bold text-slate-800">Add New Book</h1>
            <p class="text-slate-600 mt-1">Add a new book to your digital library</p>
          </div>
        </div>
        <Link :href="route('admin.books.index')"
          class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-colors"
        >
          <font-awesome-icon icon="arrow-left" class="w-4 h-4" /> Back to Books
        </Link>
      </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
      <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Title -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Book Title</label>
            <input v-model="form.title" type="text" placeholder="Enter book title"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-colors"
              :class="{'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.title}" />
            <p v-if="form.errors.title" class="text-red-500 text-sm mt-2">{{ form.errors.title }}</p>
          </div>

          <!-- Author -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Author</label>
            <input v-model="form.author" type="text" placeholder="Enter author name"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-colors"
              :class="{'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.author}" />
            <p v-if="form.errors.author" class="text-red-500 text-sm mt-2">{{ form.errors.author }}</p>
          </div>

          

          <!-- Published Year -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Published Year</label>
            <input v-model="form.published_year" type="number" placeholder="Enter published year"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-colors"
              :class="{'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.published_year}" />
            <p v-if="form.errors.published_year" class="text-red-500 text-sm mt-2">{{ form.errors.published_year }}</p>
          </div>

          <!-- Publisher -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Publisher</label>
            <input v-model="form.publisher" type="text" placeholder="Enter publisher"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-colors"
              :class="{'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.publisher}" />
            <p v-if="form.errors.publisher" class="text-red-500 text-sm mt-2">{{ form.errors.publisher }}</p>
          </div>

          <!-- Language -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Language</label>
            <input v-model="form.language" type="text" placeholder="Enter language"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-colors"
              :class="{'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.language}" />
            <p v-if="form.errors.language" class="text-red-500 text-sm mt-2">{{ form.errors.language }}</p>
          </div>

          <!-- Category -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Category</label>
            <select v-model="form.category_id"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-colors"
              :class="{'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.category_id}">
              <option value="">Select Category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
            <p v-if="form.errors.category_id" class="text-red-500 text-sm mt-2">{{ form.errors.category_id }}</p>
          </div>

          <!-- Status -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Status</label>
            <select v-model="form.status"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-colors"
              :class="{'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.status}">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            <p v-if="form.errors.status" class="text-red-500 text-sm mt-2">{{ form.errors.status }}</p>
          </div>

          <!-- Description -->
          <div class="md:col-span-2">
            <label class="block text-slate-700 font-semibold mb-2">Description</label>
            <textarea v-model="form.description" rows="4" placeholder="Enter book description"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-colors resize-none"
              :class="{'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.description}"></textarea>
            <p v-if="form.errors.description" class="text-red-500 text-sm mt-2">{{ form.errors.description }}</p>
          </div>

          <!-- Cover Image -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Cover Image</label>
            <input type="file" @change="onCoverChange" accept="image/*"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200" />
            <p v-if="form.errors.cover_image" class="text-red-500 text-sm mt-2">{{ form.errors.cover_image }}</p>
            <div v-if="coverPreview" class="mt-3">
              <img :src="coverPreview" alt="Cover Preview" class="w-24 h-32 object-cover rounded-xl border border-slate-200 shadow-sm" />
            </div>
          </div>

          <!-- Ebook File -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Ebook File</label>
            <input type="file" @change="onEbookChange" accept=".pdf,.epub,.mobi,.txt,.docx,.azw3,.fb2,.djvu,.rtf,.html,.htm"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200" />
            <p v-if="ebookName" class="text-sm text-slate-600 mt-2">Selected: {{ ebookName }}</p>
            <p v-if="form.errors.ebook_file" class="text-red-500 text-sm mt-2">{{ form.errors.ebook_file }}</p>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4 pt-4">
          <button type="submit" 
            class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-3 rounded-2xl transition-all duration-200 inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="form.processing">
            <font-awesome-icon icon="plus-square" class="w-4 h-4" />
            {{ form.processing ? 'Adding Book...' : 'Add Book' }}
          </button>
          <Link :href="route('admin.books.index')" 
            class="px-8 py-3 border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold rounded-2xl transition-all duration-200 flex items-center gap-2">
            <font-awesome-icon icon="xmark" class="w-4 h-4" /> Cancel
          </Link>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.input-field {
  @apply w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-200;
}
</style>
