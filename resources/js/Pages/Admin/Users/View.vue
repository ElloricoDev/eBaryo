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

  <div class="max-w-5xl mx-auto px-4 py-10">
    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex flex-col md:flex-row md:items-center gap-4">
      <Link
        :href="route('admin.users.index')"
        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-full shadow-lg transition-transform duration-200 hover:scale-105"
      >
        <font-awesome-icon icon="arrow-left" /> Back
      </Link>
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="eye" /> User Details
      </h1>
    </div>

    <div class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 rounded-2xl shadow-2xl overflow-hidden">
      <div class="flex flex-col items-center pt-8 pb-2">
        <div class="bg-green-200 text-green-700 rounded-full w-20 h-20 flex items-center justify-center text-5xl shadow mb-2">
          <font-awesome-icon icon="user" />
        </div>
        <div class="text-xl font-bold text-green-700 mb-2">{{ props.user.user_name || 'N/A' }}</div>
        <span
          :class="[
            'inline-block px-4 py-1 rounded-full text-xs font-bold mb-4',
            props.user.role === 'admin' ? 'bg-green-200 text-green-800' :
            props.user.role === 'user' ? 'bg-blue-100 text-blue-700' :
            'bg-gray-200 text-gray-700'
          ]"
        >
          {{ props.user.role ? props.user.role.charAt(0).toUpperCase() + props.user.role.slice(1) : 'N/A' }}
        </span>
      </div>
      <div class="p-8">
        <table class="w-full table-auto">
          <tbody class="divide-y divide-gray-200">
            <tr class="even:bg-green-50 transition-colors">
              <th class="text-green-700 text-left font-semibold pr-6 py-3 w-1/3"><font-awesome-icon icon="user" /> First Name</th>
              <td class="font-medium text-gray-800">{{ props.user.first_name || 'N/A' }}</td>
            </tr>
            <tr class="even:bg-green-50 transition-colors">
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="user" /> Middle Name</th>
              <td>{{ props.user.middle_name || 'N/A' }}</td>
            </tr>
            <tr class="even:bg-green-50 transition-colors">
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="user" /> Last Name</th>
              <td>{{ props.user.last_name || 'N/A' }}</td>
            </tr>
            <tr class="even:bg-green-50 transition-colors">
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="phone" /> Contact Number</th>
              <td>{{ props.user.contact_number || 'N/A' }}</td>
            </tr>
            <tr class="even:bg-green-50 transition-colors">
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="map-marker-alt" /> Address</th>
              <td>{{ props.user.address || 'N/A' }}</td>
            </tr>
            <tr class="even:bg-green-50 transition-colors">
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="envelope" /> Email</th>
              <td>{{ props.user.email || 'N/A' }}</td>
            </tr>
            <tr class="even:bg-green-50 transition-colors">
              <th class="text-green-700 text-left font-semibold pr-6 py-3"><font-awesome-icon icon="calendar" /> Registered At</th>
              <td>{{ props.user.created_at ? new Date(props.user.created_at).toLocaleString() : 'N/A' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
