<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowLeft, faEye, faUser, faPhone, faMapMarkerAlt, faEnvelope, faIdBadge, faCalendar } from '@fortawesome/free-solid-svg-icons'

library.add(faArrowLeft, faEye, faUser, faPhone, faMapMarkerAlt, faEnvelope, faIdBadge, faCalendar)

defineOptions({ layout: AdminLayout })

const props = defineProps({
  user: Object
})
</script>

<template>
  <Head title="User Details" />

  <div class="max-w-5xl mx-auto px-4 py-6">
    <div class="flex items-center gap-4 mb-6">
      <Link
        :href="route('admin.users.index')"
        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-full shadow transition"
      >
        <font-awesome-icon icon="arrow-left" /> Back
      </Link>
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="eye" /> User Details
      </h1>
    </div>

    <div class="bg-white border-2 border-green-500 rounded-2xl shadow-md overflow-hidden">
      <div class="p-6">
        <table class="w-full table-auto">
          <tbody class="divide-y divide-gray-200">
            <tr class="py-3">
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="user" /> User Name</th>
              <td class="font-medium text-gray-800">{{ props.user.user_name || 'N/A' }}</td>
            </tr>
            <tr>
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="user" /> First Name</th>
              <td>{{ props.user.first_name || 'N/A' }}</td>
            </tr>
            <tr>
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="user" /> Middle Name</th>
              <td>{{ props.user.middle_name || 'N/A' }}</td>
            </tr>
            <tr>
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="user" /> Last Name</th>
              <td>{{ props.user.last_name || 'N/A' }}</td>
            </tr>
            <tr>
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="phone" /> Contact Number</th>
              <td>{{ props.user.contact_number || 'N/A' }}</td>
            </tr>
            <tr>
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="map-marker-alt" /> Address</th>
              <td>{{ props.user.address || 'N/A' }}</td>
            </tr>
            <tr>
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="envelope" /> Email</th>
              <td>{{ props.user.email || 'N/A' }}</td>
            </tr>
            <tr>
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="id-badge" /> Role</th>
              <td>{{ props.user.role || 'N/A' }}</td>
            </tr>
            <tr>
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="calendar" /> Registered At</th>
              <td>{{ props.user.created_at ? new Date(props.user.created_at).toLocaleString() : 'N/A' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
