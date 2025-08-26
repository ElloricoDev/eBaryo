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

  <div class="max-w-5xl mx-auto  px-4">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex items-center gap-3">
      <Link
        :href="route('admin.profile.index')"
        class="inline-flex items-center px-6 py-2 border border-green-600 text-green-600 hover:bg-green-50 rounded-full shadow font-semibold text-base"
      >
        <font-awesome-icon icon="arrow-left" class="mr-2" /> Back
      </Link>
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="pencil-alt" /> Edit Profile
      </h1>
    </div>

    <!-- Form -->
    <form
      @submit.prevent="submit"
      enctype="multipart/form-data"
      class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 rounded-2xl shadow-2xl p-8"
    >
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- User Name -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="user" /> User Name</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="user" /></span>
          <input v-model="form.user_name" type="text" class="form-input pl-10" />
          <div v-if="form.errors.user_name" class="text-red-500 text-sm mt-1">{{ form.errors.user_name }}</div>
        </div>

        <!-- First Name -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="user" /> First Name</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="user" /></span>
          <input v-model="form.first_name" type="text" class="form-input pl-10" />
          <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">{{ form.errors.first_name }}</div>
        </div>

        <!-- Middle Name -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="user" /> Middle Name</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="user" /></span>
          <input v-model="form.middle_name" type="text" class="form-input pl-10" />
          <div v-if="form.errors.middle_name" class="text-red-500 text-sm mt-1">{{ form.errors.middle_name }}</div>
        </div>

        <!-- Last Name -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="user" /> Last Name</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="user" /></span>
          <input v-model="form.last_name" type="text" class="form-input pl-10" />
          <div v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">{{ form.errors.last_name }}</div>
        </div>

        <!-- Contact Number -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="phone" /> Contact Number</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="phone" /></span>
          <input v-model="form.contact_number" type="text" class="form-input pl-10" />
          <div v-if="form.errors.contact_number" class="text-red-500 text-sm mt-1">{{ form.errors.contact_number }}</div>
        </div>

        <!-- Address -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="map-marker-alt" /> Address</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="map-marker-alt" /></span>
          <input v-model="form.address" type="text" class="form-input pl-10" />
          <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
        </div>

        <!-- Email -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="envelope" /> Email</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="envelope" /></span>
          <input v-model="form.email" type="email" class="form-input pl-10" />
          <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
        </div>

        <!-- Student -->
        <div class="relative">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="graduation-cap" /> Student</label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="graduation-cap" /></span>
          <select v-model="form.student" class="form-input pl-10">
            <option value="yes">Yes</option>
            <option value="no">No</option>
          </select>
          <div v-if="form.errors.student" class="text-red-500 text-sm mt-1">{{ form.errors.student }}</div>
        </div>

        <!-- Avatar Upload -->
        <div class="relative flex flex-col items-center justify-center">
          <label class="text-green-700 font-semibold mb-1 block"><font-awesome-icon icon="image" /> Avatar</label>
          <input
            type="file"
            class="form-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200"
            @change="onAvatarChange"
            accept="image/*"
          />
          <div v-if="form.errors.avatar" class="text-red-500 text-sm mt-1">{{ form.errors.avatar }}</div>
          <div v-if="avatarPreview" class="mt-4 flex justify-center">
            <img :src="avatarPreview" alt="Avatar Preview" class="w-24 h-24 rounded-full border-4 border-green-600 object-cover shadow-lg" />
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="mt-10 flex flex-wrap gap-4 justify-center">
        <button
          type="submit"
          :disabled="form.processing"
          class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white rounded-full shadow-xl font-bold text-lg disabled:opacity-50"
        >
          <font-awesome-icon icon="save" class="mr-2" /> {{ form.processing ? 'Saving...' : 'Update Profile' }}
        </button>
        <Link
          :href="route('admin.profile.index')"
          class="inline-flex items-center px-8 py-3 border border-green-600 text-green-600 rounded-full hover:bg-green-50 shadow-sm font-semibold text-lg"
        >
          <font-awesome-icon icon="arrow-left" class="mr-2" /> Cancel
        </Link>
      </div>
    </form>
  </div>
</template>

<style scoped>
.form-input {
  @apply w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-200;
}
</style>
