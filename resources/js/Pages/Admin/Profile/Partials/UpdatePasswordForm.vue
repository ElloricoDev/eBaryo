<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faShieldAlt, faKey, faArrowRotateRight, faInfoCircle } from '@fortawesome/free-solid-svg-icons'

library.add(faShieldAlt, faKey, faArrowRotateRight, faInfoCircle)

const { props } = usePage()
const user = props.user
const isGoogleUser = user.google_id !== null

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.put(route('admin.profile.password.update'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      Swal.fire({
        icon: 'success',
        title: 'Password Updated',
        text: 'Your password has been successfully updated.',
        timer: 2000,
        showConfirmButton: false,
      })
    }
  })
}
</script>

<template>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
      <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
        <font-awesome-icon icon="shield-alt" class="w-5 h-5 text-blue-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-slate-800">Update Password</h3>
        <p class="text-slate-600 text-sm">Change your account password for better security</p>
      </div>
    </div>

    <!-- Google Account Notice -->
    <div v-if="isGoogleUser" class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
          <font-awesome-icon icon="info-circle" class="w-4 h-4 text-blue-600" />
        </div>
        <div>
          <h4 class="text-blue-800 font-semibold">Google Account</h4>
          <p class="text-blue-700 text-sm">You cannot change your password because you signed in using Google.</p>
        </div>
      </div>
    </div>

    <!-- Password Update Form -->
    <form v-else @submit.prevent="submit" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-slate-700 font-semibold mb-2">Current Password</label>
          <input
            v-model="form.current_password"
            type="password"
            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
            placeholder="Enter your current password"
          />
          <div v-if="form.errors.current_password" class="text-red-500 text-sm mt-2">
            {{ form.errors.current_password }}
          </div>
        </div>

        <div>
          <label class="block text-slate-700 font-semibold mb-2">New Password</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
            placeholder="Enter your new password"
          />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-2">
            {{ form.errors.password }}
          </div>
        </div>
      </div>

      <div>
        <label class="block text-slate-700 font-semibold mb-2">Confirm New Password</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
          placeholder="Confirm your new password"
        />
        <div v-if="form.errors.password_confirmation" class="text-red-500 text-sm mt-2">
          {{ form.errors.password_confirmation }}
        </div>
      </div>

      <button
        type="submit"
        :disabled="form.processing"
        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-2xl transition-all duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <font-awesome-icon icon="arrow-rotate-right" class="w-4 h-4" />
        {{ form.processing ? 'Updating Password...' : 'Update Password' }}
      </button>
    </form>
  </div>
</template>
