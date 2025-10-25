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

  <div class="mx-auto px-4">
    <!-- Hero Header -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center">
            <font-awesome-icon icon="eye" class="w-8 h-8 text-white" />
          </div>
          <div>
            <h1 class="text-3xl font-bold text-slate-800">User Details</h1>
            <p class="text-slate-600 mt-1">View comprehensive user information</p>
          </div>
        </div>
        <Link
          :href="route('admin.users.index')"
          class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-colors"
        >
          <font-awesome-icon icon="arrow-left" class="w-4 h-4" /> Back to Users
        </Link>
      </div>
    </div>

    <!-- User Details Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="grid grid-cols-1 lg:grid-cols-4">
        <!-- User Avatar Section -->
        <div class="lg:col-span-1 bg-slate-50 p-8 flex flex-col items-center justify-center">
          <div class="relative">
            <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg mb-4">
              <font-awesome-icon icon="user" class="text-5xl text-white" />
            </div>
            <div class="text-center">
              <h2 class="text-xl font-bold text-slate-800 mb-2">{{ props.user.user_name || 'N/A' }}</h2>
              <span
                :class="[
                  'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                  props.user.role === 'admin' ? 'bg-green-100 text-green-700' :
                  props.user.role === 'user' ? 'bg-blue-100 text-blue-700' :
                  'bg-slate-100 text-slate-700'
                ]"
              >
                {{ props.user.role ? props.user.role.charAt(0).toUpperCase() + props.user.role.slice(1) : 'N/A' }}
              </span>
            </div>
          </div>
        </div>

        <!-- User Information Section -->
        <div class="lg:col-span-3 p-8">
          <div class="mb-6">
            <h3 class="text-2xl font-bold text-slate-800 mb-2">Personal Information</h3>
            <p class="text-slate-600">Complete user profile details</p>
          </div>

          <!-- User Details Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="space-y-4">
              <div>
                <label class="block text-slate-700 font-semibold mb-1">First Name</label>
                <p class="text-slate-600">{{ props.user.first_name || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Middle Name</label>
                <p class="text-slate-600">{{ props.user.middle_name || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Last Name</label>
                <p class="text-slate-600">{{ props.user.last_name || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Contact Number</label>
                <p class="text-slate-600">{{ props.user.contact_number || 'N/A' }}</p>
              </div>
            </div>
            <div class="space-y-4">
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Email Address</label>
                <p class="text-slate-600">{{ props.user.email || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Address</label>
                <p class="text-slate-600">{{ props.user.address || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Registration Date</label>
                <p class="text-slate-600">{{ props.user.created_at ? new Date(props.user.created_at).toLocaleDateString() : 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Account Status</label>
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                  <font-awesome-icon icon="check-circle" class="w-4 h-4 mr-1" />
                  Active
                </div>
              </div>
            </div>
          </div>

          <!-- Additional Information -->
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
            <h4 class="text-slate-700 font-semibold mb-2">Account Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-slate-600">User ID:</span>
                <span class="font-mono text-slate-800 ml-2">#{{ props.user.id }}</span>
              </div>
              <div>
                <span class="text-slate-600">Last Updated:</span>
                <span class="text-slate-800 ml-2">{{ props.user.updated_at ? new Date(props.user.updated_at).toLocaleDateString() : 'N/A' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
