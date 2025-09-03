<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, usePage, Head, Link } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { ref } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faArrowLeft, faPencilAlt, faUser, faPhone, faMapMarkerAlt, faEnvelope, faGraduationCap, faImage, faSave
} from '@fortawesome/free-solid-svg-icons'

library.add(faArrowLeft, faPencilAlt, faUser, faPhone, faMapMarkerAlt, faEnvelope, faGraduationCap, faImage, faSave)

defineOptions({ layout: AdminLayout })

const { props } = usePage()
const user = props.user

const form = useForm({
  user_name: user.user_name || '',
  first_name: user.first_name || '',
  middle_name: user.middle_name || '',
  last_name: user.last_name || '',
  contact_number: user.contact_number || '',
  address: user.address || '',
  email: user.email || '',
  avatar: null,
  student: user.student || 'no',
})

const avatarPreview = ref(user.avatar || null)

const submit = () => {
  form.transform((data) => ({
    ...data,
    _method: 'PUT',
  }))

  form.post(route('admin.profile.update'), {
    forceFormData: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Profile Updated',
        text: 'Your profile has been successfully updated.',
        timer: 2000,
        showConfirmButton: false,
      })
    }
  })
}

function onAvatarChange(e) {
  const file = e.target.files[0]
  form.avatar = file
  if (file) {
    avatarPreview.value = URL.createObjectURL(file)
  } else {
    avatarPreview.value = user.avatar || null
  }
}
</script>

<template>
  <Head title="Edit Profile" />

  <div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Hero Header -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-8">
      <div class="flex items-center gap-4">
        <Link
          :href="route('admin.profile.index')"
          class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-colors"
        >
          <font-awesome-icon icon="arrow-left" class="w-4 h-4" />
          Back to Profile
        </Link>
        <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center">
          <font-awesome-icon icon="pencil-alt" class="w-8 h-8 text-white" />
        </div>
        <div>
          <h1 class="text-3xl font-bold text-slate-800">Edit Profile</h1>
          <p class="text-slate-600 mt-1">Update your personal information and account settings</p>
        </div>
      </div>
    </div>

    <!-- Form -->
    <form
      @submit.prevent="submit"
      enctype="multipart/form-data"
      class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8"
    >
      <!-- Personal Information Section -->
      <div class="mb-8">
        <h3 class="text-xl font-semibold text-slate-800 mb-6">Personal Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- User Name -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">User Name</label>
            <input 
              v-model="form.user_name" 
              type="text" 
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-colors"
              placeholder="Enter your username"
            />
            <div v-if="form.errors.user_name" class="text-red-500 text-sm mt-2">{{ form.errors.user_name }}</div>
          </div>

          <!-- First Name -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">First Name</label>
            <input 
              v-model="form.first_name" 
              type="text" 
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-colors"
              placeholder="Enter your first name"
            />
            <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-2">{{ form.errors.first_name }}</div>
          </div>

          <!-- Middle Name -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Middle Name</label>
            <input 
              v-model="form.middle_name" 
              type="text" 
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-colors"
              placeholder="Enter your middle name"
            />
            <div v-if="form.errors.middle_name" class="text-red-500 text-sm mt-2">{{ form.errors.middle_name }}</div>
          </div>

          <!-- Last Name -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Last Name</label>
            <input 
              v-model="form.last_name" 
              type="text" 
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-colors"
              placeholder="Enter your last name"
            />
            <div v-if="form.errors.last_name" class="text-red-500 text-sm mt-2">{{ form.errors.last_name }}</div>
          </div>

          <!-- Contact Number -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Contact Number</label>
            <input 
              v-model="form.contact_number" 
              type="text" 
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-colors"
              placeholder="Enter your contact number"
            />
            <div v-if="form.errors.contact_number" class="text-red-500 text-sm mt-2">{{ form.errors.contact_number }}</div>
          </div>

          <!-- Student Status -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Student Status</label>
            <select 
              v-model="form.student" 
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-colors"
            >
              <option value="yes">Yes</option>
              <option value="no">No</option>
            </select>
            <div v-if="form.errors.student" class="text-red-500 text-sm mt-2">{{ form.errors.student }}</div>
          </div>
        </div>
      </div>

      <!-- Contact Information Section -->
      <div class="mb-8">
        <h3 class="text-xl font-semibold text-slate-800 mb-6">Contact Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Email -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Email Address</label>
            <input 
              v-model="form.email" 
              type="email" 
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-colors"
              placeholder="Enter your email address"
            />
            <div v-if="form.errors.email" class="text-red-500 text-sm mt-2">{{ form.errors.email }}</div>
          </div>

          <!-- Address -->
          <div>
            <label class="block text-slate-700 font-semibold mb-2">Address</label>
            <input 
              v-model="form.address" 
              type="text" 
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-colors"
              placeholder="Enter your address"
            />
            <div v-if="form.errors.address" class="text-red-500 text-sm mt-2">{{ form.errors.address }}</div>
          </div>
        </div>
      </div>

      <!-- Avatar Section -->
      <div class="mb-8">
        <h3 class="text-xl font-semibold text-slate-800 mb-6">Profile Picture</h3>
        <div class="flex flex-col items-center">
          <div class="mb-4">
            <input
              type="file"
              @change="onAvatarChange"
              accept="image/*"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-yellow-100 file:text-yellow-700 hover:file:bg-yellow-200"
            />
            <div v-if="form.errors.avatar" class="text-red-500 text-sm mt-2">{{ form.errors.avatar }}</div>
          </div>
          <div v-if="avatarPreview" class="flex justify-center">
            <img 
              :src="avatarPreview" 
              alt="Avatar Preview" 
              class="w-32 h-32 rounded-2xl object-cover shadow-lg border border-slate-200" 
            />
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-wrap gap-4 justify-end">
        <Link
          :href="route('admin.profile.index')"
          class="inline-flex items-center gap-2 px-6 py-3 border border-slate-300 text-slate-700 rounded-2xl hover:bg-slate-50 transition-colors font-semibold"
        >
          <font-awesome-icon icon="arrow-left" class="w-4 h-4" />
          Cancel
        </Link>
        <button
          type="submit"
          :disabled="form.processing"
          class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-700 hover:to-yellow-800 text-white rounded-2xl transition-all duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed font-semibold"
        >
          <font-awesome-icon icon="save" class="w-4 h-4" />
          {{ form.processing ? 'Updating Profile...' : 'Update Profile' }}
        </button>
      </div>
    </form>
  </div>
</template>


