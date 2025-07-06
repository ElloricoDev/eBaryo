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

  <div class="max-w-5xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="flex items-center mb-6 gap-3">
      <Link
        :href="route('admin.profile.index')"
        class="inline-flex items-center px-4 py-2 border border-green-600 text-green-600 hover:bg-green-50 rounded-lg transition"
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
      class="bg-white border-2 border-green-600 rounded-2xl shadow p-6 hover:shadow-lg transition"
    >
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- User Name -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="user" /> User Name</label>
          <input v-model="form.user_name" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600" />
          <div v-if="form.errors.user_name" class="text-red-500 text-sm mt-1">{{ form.errors.user_name }}</div>
        </div>

        <!-- First Name -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="user" /> First Name</label>
          <input v-model="form.first_name" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600" />
          <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">{{ form.errors.first_name }}</div>
        </div>

        <!-- Middle Name -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="user" /> Middle Name</label>
          <input v-model="form.middle_name" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600" />
          <div v-if="form.errors.middle_name" class="text-red-500 text-sm mt-1">{{ form.errors.middle_name }}</div>
        </div>

        <!-- Last Name -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="user" /> Last Name</label>
          <input v-model="form.last_name" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600" />
          <div v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">{{ form.errors.last_name }}</div>
        </div>

        <!-- Contact Number -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="phone" /> Contact Number</label>
          <input v-model="form.contact_number" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600" />
          <div v-if="form.errors.contact_number" class="text-red-500 text-sm mt-1">{{ form.errors.contact_number }}</div>
        </div>

        <!-- Address -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="map-marker-alt" /> Address</label>
          <input v-model="form.address" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600" />
          <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
        </div>

        <!-- Email -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="envelope" /> Email</label>
          <input v-model="form.email" type="email" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600" />
          <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
        </div>

        <!-- Student -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="graduation-cap" /> Student</label>
          <select v-model="form.student" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600">
            <option value="yes">Yes</option>
            <option value="no">No</option>
          </select>
          <div v-if="form.errors.student" class="text-red-500 text-sm mt-1">{{ form.errors.student }}</div>
        </div>

        <!-- Avatar Upload -->
        <div>
          <label class="text-green-700 font-semibold"><font-awesome-icon icon="image" /> Avatar</label>
          <input
            type="file"
            class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200"
            @change="onAvatarChange"
            accept="image/*"
          />
          <div v-if="form.errors.avatar" class="text-red-500 text-sm mt-1">{{ form.errors.avatar }}</div>
          <div v-if="avatarPreview" class="mt-3">
            <img :src="avatarPreview" alt="Avatar Preview" class="w-20 h-20 rounded-full border border-green-600 object-cover" />
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="mt-6 flex flex-wrap gap-3">
        <button
          type="submit"
          :disabled="form.processing"
          class="inline-flex items-center px-5 py-2 bg-green-600 text-white rounded-full shadow hover:bg-green-700 transition disabled:opacity-50"
        >
          <font-awesome-icon icon="save" class="mr-2" /> {{ form.processing ? 'Saving...' : 'Update Profile' }}
        </button>
        <Link
          :href="route('admin.profile.index')"
          class="inline-flex items-center px-5 py-2 border border-green-600 text-green-600 rounded-full hover:bg-green-50 transition"
        >
          <font-awesome-icon icon="arrow-left" class="mr-2" /> Cancel
        </Link>
      </div>
    </form>
  </div>
</template>
