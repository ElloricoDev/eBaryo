<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faShieldAlt, faKey, faArrowRotateRight } from '@fortawesome/free-solid-svg-icons'

library.add(faShieldAlt, faKey, faArrowRotateRight)

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
  <div class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 rounded-2xl shadow-2xl transition hover:shadow-2xl mb-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-500 text-white px-6 py-4 flex items-center gap-2 rounded-t-2xl shadow">
      <font-awesome-icon icon="shield-alt" />
      <span class="font-semibold text-lg">Update Password</span>
    </div>

    <!-- Body -->
    <div class="p-8">
      <!-- Google Account Info -->
      <div v-if="isGoogleUser" class="bg-blue-100 text-blue-800 px-4 py-3 rounded-md mb-4 font-semibold">
        You cannot change your password because you signed in using Google.
      </div>

      <!-- Password Form -->
      <form v-else @submit.prevent="submit" class="space-y-6">
        <div class="relative">
          <label class="block text-green-700 font-semibold mb-1">
            <font-awesome-icon icon="key" /> Current Password
          </label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="key" /></span>
          <input
            v-model="form.current_password"
            type="password"
            class="w-full rounded-full border border-green-500 shadow-sm focus:ring-2 focus:ring-green-300 focus:border-green-600 pl-10 py-2"
          />
          <div v-if="form.errors.current_password" class="text-red-500 text-sm mt-1">
            {{ form.errors.current_password }}
          </div>
        </div>

        <div class="relative">
          <label class="block text-green-700 font-semibold mb-1">
            <font-awesome-icon icon="key" /> New Password
          </label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="key" /></span>
          <input
            v-model="form.password"
            type="password"
            class="w-full rounded-full border border-green-500 shadow-sm focus:ring-2 focus:ring-green-300 focus:border-green-600 pl-10 py-2"
          />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
            {{ form.errors.password }}
          </div>
        </div>

        <div class="relative">
          <label class="block text-green-700 font-semibold mb-1">
            <font-awesome-icon icon="key" /> Confirm New Password
          </label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="key" /></span>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="w-full rounded-full border border-green-500 shadow-sm focus:ring-2 focus:ring-green-300 focus:border-green-600 pl-10 py-2"
          />
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-bold rounded-full shadow-xl text-lg disabled:opacity-50"
        >
          <font-awesome-icon icon="arrow-rotate-right" class="mr-2" />
          {{ form.processing ? 'Updating...' : 'Update Password' }}
        </button>
      </form>
    </div>
  </div>
</template>
