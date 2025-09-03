<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { usePage, Link } from '@inertiajs/vue3'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import DeleteAccountForm from './Partials/DeleteAccountForm.vue'
import VerifyEmail from './Partials/VerifyEmail.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faUserCircle, faUser, faPhone, faMapMarkerAlt, faEnvelope, faCheckCircle, faIdBadge, faGraduationCap, faPencilAlt, faExclamationTriangle
} from '@fortawesome/free-solid-svg-icons'

library.add(faUserCircle, faUser, faPhone, faMapMarkerAlt, faEnvelope, faCheckCircle, faIdBadge, faGraduationCap, faPencilAlt, faExclamationTriangle)

defineOptions({ layout: AdminLayout })

const { props } = usePage()
const user = props.user
</script>

<template>
  <Head title="My Profile" />

  <div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Hero Header -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-8">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
          <font-awesome-icon icon="user-circle" class="w-8 h-8 text-white" />
        </div>
        <div>
          <h1 class="text-3xl font-bold text-slate-800">Admin Profile</h1>
          <p class="text-slate-600 mt-1">Manage your account settings and personal information</p>
        </div>
      </div>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="grid grid-cols-1 lg:grid-cols-4">
        <!-- Avatar Section -->
        <div class="lg:col-span-1 bg-slate-50 p-8 flex flex-col items-center justify-center">
          <div class="relative">
            <img
              v-if="user.avatar"
              :src="user.avatar"
              alt="Avatar"
              class="w-32 h-32 rounded-2xl object-cover shadow-lg border border-slate-200 mb-4"
            />
            <div v-else class="w-32 h-32 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg border border-slate-200 mb-4">
              <font-awesome-icon icon="user" class="text-4xl text-white" />
            </div>
            <div class="text-center">
              <h2 class="text-xl font-bold text-slate-800 mb-2">{{ user.user_name || 'N/A' }}</h2>
              <span
                :class="[
                  'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                  user.role === 'admin' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'
                ]"
              >
                <font-awesome-icon icon="id-badge" class="w-4 h-4 mr-1" />
                {{ user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : 'N/A' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Profile Information Section -->
        <div class="lg:col-span-3 p-8">
          <div class="mb-6">
            <h3 class="text-2xl font-bold text-slate-800 mb-2">Personal Information</h3>
            <p class="text-slate-600">Complete profile details and account information</p>
          </div>

          <!-- Profile Details Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="space-y-4">
              <div>
                <label class="block text-slate-700 font-semibold mb-1">User Name</label>
                <p class="text-slate-600">{{ user.user_name || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">First Name</label>
                <p class="text-slate-600">{{ user.first_name || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Middle Name</label>
                <p class="text-slate-600">{{ user.middle_name || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Last Name</label>
                <p class="text-slate-600">{{ user.last_name || 'N/A' }}</p>
              </div>
            </div>
            <div class="space-y-4">
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Email Address</label>
                <p class="text-slate-600">{{ user.email || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Contact Number</label>
                <p class="text-slate-600">{{ user.contact_number || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Address</label>
                <p class="text-slate-600">{{ user.address || 'N/A' }}</p>
              </div>
              <div>
                <label class="block text-slate-700 font-semibold mb-1">Student Status</label>
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-medium">
                  {{ user.student === 'yes' ? 'Yes' : 'No' }}
                </div>
              </div>
            </div>
          </div>

          <!-- Email Verification Status -->
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6">
            <h4 class="text-slate-700 font-semibold mb-2">Email Verification Status</h4>
            <div class="flex items-center gap-2">
              <font-awesome-icon 
                :icon="user.email_verified_at ? 'check-circle' : 'exclamation-triangle'" 
                :class="[
                  'w-5 h-5',
                  user.email_verified_at ? 'text-green-600' : 'text-yellow-600'
                ]" 
              />
              <span 
                :class="[
                  'font-medium',
                  user.email_verified_at ? 'text-green-700' : 'text-yellow-700'
                ]"
              >
                {{ user.email_verified_at ? 'Email Verified' : 'Email Not Verified' }}
              </span>
            </div>
          </div>

          <!-- Edit Profile Button -->
          <Link
            :href="route('admin.profile.edit')"
            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-2xl transition-all duration-200 shadow-sm"
          >
            <font-awesome-icon icon="pencil-alt" class="w-4 h-4" />
            Edit Profile
          </Link>
        </div>
      </div>
    </div>

    <!-- Account Management Sections -->
    <div class="mt-8 space-y-6">
      <VerifyEmail />
      <UpdatePasswordForm />
      <DeleteAccountForm />
    </div>
  </div>
</template>
