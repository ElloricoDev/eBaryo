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

  <div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Header -->
    <h1 class="text-2xl font-bold text-green-600 flex items-center gap-2 mb-6">
      <font-awesome-icon icon="user-circle" /> Admin Profile
    </h1>

    <!-- Profile Card -->
    <div
      class="bg-white border-2 border-green-600 rounded-2xl shadow p-6 hover:shadow-lg transition"
    >
      <!-- Avatar -->
      <div class="text-center mb-4">
        <img
          v-if="user.avatar"
          :src="user.avatar"
          alt="Avatar"
          class="mx-auto w-24 h-24 rounded-full border-2 border-green-600 object-cover"
        />
        <div v-else>
          <img
            src="https://ui-avatars.com/api/?name=N%2FA&background=ddd&color=555"
            alt="No Avatar"
            class="mx-auto w-24 h-24 rounded-full border-2 border-green-600 object-cover"
          />
        </div>
      </div>

      <!-- Info -->
      <div class="space-y-2 text-gray-700">
        <p><strong class="text-green-700"><font-awesome-icon icon="user" /> User Name:</strong> {{ user.user_name || 'N/A' }}</p>
        <p><strong class="text-green-700"><font-awesome-icon icon="user" /> First Name:</strong> {{ user.first_name || 'N/A' }}</p>
        <p><strong class="text-green-700"><font-awesome-icon icon="user" /> Middle Name:</strong> {{ user.middle_name || 'N/A' }}</p>
        <p><strong class="text-green-700"><font-awesome-icon icon="user" /> Last Name:</strong> {{ user.last_name || 'N/A' }}</p>
        <p><strong class="text-green-700"><font-awesome-icon icon="phone" /> Contact Number:</strong> {{ user.contact_number || 'N/A' }}</p>
        <p><strong class="text-green-700"><font-awesome-icon icon="map-marker-alt" /> Address:</strong> {{ user.address || 'N/A' }}</p>
        <p><strong class="text-green-700"><font-awesome-icon icon="envelope" /> Email:</strong> {{ user.email || 'N/A' }}</p>
        <p>
          <strong class="text-green-700"><font-awesome-icon icon="check-circle" /> Email Status:</strong>
          <span v-if="user.email_verified_at" class="text-green-600 font-semibold">Verified</span>
          <span v-else class="text-red-600 font-semibold">Not Verified</span>
        </p>
        <p><strong class="text-green-700"><font-awesome-icon icon="id-badge" /> Role:</strong> {{ user.role || 'N/A' }}</p>
        <p>
          <strong class="text-green-700"><font-awesome-icon icon="graduation-cap" /> Student:</strong>
          <span>{{ user.student === 'yes' ? 'Yes' : 'No' }}</span>
        </p>
      </div>

      <!-- Edit Button -->
      <Link
        :href="route('admin.profile.edit')"
        class="block w-full text-center bg-green-600 text-white font-semibold mt-6 py-2 rounded-full shadow hover:bg-green-700 transition"
      >
        <font-awesome-icon icon="pencil-alt" class="mr-1" /> Edit Profile
      </Link>
    </div>

    <!-- Partials -->
    <div class="mt-8 space-y-6">
      <VerifyEmail />
      <UpdatePasswordForm />
      <DeleteAccountForm />
    </div>
  </div>
</template>
