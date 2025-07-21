<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { usePage } from '@inertiajs/vue3'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import DeleteAccountForm from './Partials/DeleteAccountForm.vue'
import VerifyEmail from './Partials/VerifyEmail.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faUserCircle, faUser, faPhone, faMapMarkerAlt, faEnvelope, faCheckCircle, faIdBadge, faGraduationCap, faPencilAlt
} from '@fortawesome/free-solid-svg-icons'

library.add(faUserCircle, faUser, faPhone, faMapMarkerAlt, faEnvelope, faCheckCircle, faIdBadge, faGraduationCap, faPencilAlt)

defineOptions({ layout: AdminLayout })

const { props } = usePage()
const user = props.user
</script>

<template>
  <Head title="My Profile" />

  <div class="max-w-4xl mx-auto py-10 px-4">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex items-center gap-2">
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="user-circle" /> Admin Profile
      </h1>
    </div>

    <!-- Profile Card -->
    <div
      class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 rounded-2xl shadow-2xl p-8 hover:shadow-2xl transition"
    >
      <!-- Avatar -->
      <div class="text-center mb-6">
        <img
          v-if="user.avatar"
          :src="user.avatar"
          alt="Avatar"
          class="mx-auto w-28 h-28 rounded-full border-4 border-green-600 object-cover shadow-lg"
        />
        <div v-else>
          <img
            src="https://ui-avatars.com/api/?name=N%2FA&background=ddd&color=555"
            alt="No Avatar"
            class="mx-auto w-28 h-28 rounded-full border-4 border-green-600 object-cover shadow-lg"
          />
        </div>
        <span
          :class="[
            'inline-block px-4 py-1 rounded-full mt-4 font-bold text-sm',
            user.role === 'admin' ? 'bg-green-200 text-green-800' : 'bg-blue-100 text-blue-700'
          ]"
        >
          <font-awesome-icon icon="id-badge" class="mr-1" /> {{ user.role || 'N/A' }}
        </span>
      </div>

      <!-- Info -->
      <div class="space-y-3 text-gray-700">
        <div class="flex items-center gap-2"><strong class="text-green-700"><font-awesome-icon icon="user" /> User Name:</strong> <span>{{ user.user_name || 'N/A' }}</span></div>
        <div class="flex items-center gap-2"><strong class="text-green-700"><font-awesome-icon icon="user" /> First Name:</strong> <span>{{ user.first_name || 'N/A' }}</span></div>
        <div class="flex items-center gap-2"><strong class="text-green-700"><font-awesome-icon icon="user" /> Middle Name:</strong> <span>{{ user.middle_name || 'N/A' }}</span></div>
        <div class="flex items-center gap-2"><strong class="text-green-700"><font-awesome-icon icon="user" /> Last Name:</strong> <span>{{ user.last_name || 'N/A' }}</span></div>
        <div class="flex items-center gap-2"><strong class="text-green-700"><font-awesome-icon icon="phone" /> Contact Number:</strong> <span>{{ user.contact_number || 'N/A' }}</span></div>
        <div class="flex items-center gap-2"><strong class="text-green-700"><font-awesome-icon icon="map-marker-alt" /> Address:</strong> <span>{{ user.address || 'N/A' }}</span></div>
        <div class="flex items-center gap-2"><strong class="text-green-700"><font-awesome-icon icon="envelope" /> Email:</strong> <span>{{ user.email || 'N/A' }}</span></div>
        <div class="flex items-center gap-2">
          <strong class="text-green-700"><font-awesome-icon icon="check-circle" /> Email Status:</strong>
          <span v-if="user.email_verified_at" class="text-green-600 font-semibold">Verified</span>
          <span v-else class="text-red-600 font-semibold">Not Verified</span>
        </div>
        <div class="flex items-center gap-2"><strong class="text-green-700"><font-awesome-icon icon="graduation-cap" /> Student:</strong> <span>{{ user.student === 'yes' ? 'Yes' : 'No' }}</span></div>
      </div>

      <!-- Edit Button -->
      <Link
        :href="route('admin.profile.edit')"
        class="block w-full text-center bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-semibold mt-8 py-3 rounded-full shadow-xl transition-transform duration-200 hover:scale-105 text-lg"
      >
        <font-awesome-icon icon="pencil-alt" class="mr-1" /> Edit Profile
      </Link>
    </div>

    <!-- Partials -->
    <div class="mt-10 space-y-6">
      <VerifyEmail />
      <UpdatePasswordForm />
      <DeleteAccountForm />
    </div>
  </div>
</template>
