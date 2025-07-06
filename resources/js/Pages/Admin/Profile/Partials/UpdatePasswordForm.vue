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
  <div class="border-2 border-green-600 rounded-2xl shadow transition hover:shadow-lg mb-6">
    <!-- Header -->
    <div class="bg-green-600 text-white px-6 py-3 flex items-center gap-2 rounded-t-2xl">
      <font-awesome-icon icon="shield-alt" />
      <span class="font-semibold">Update Password</span>
    </div>

    <!-- Body -->
    <div class="p-6">
      <!-- Google Account Info -->
      <div v-if="isGoogleUser" class="bg-blue-100 text-blue-800 px-4 py-3 rounded-md mb-4">
        You cannot change your password because you signed in using Google.
      </div>

      <!-- Password Form -->
      <form v-else @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-green-700 font-semibold mb-1">
            <font-awesome-icon icon="key" /> Current Password
          </label>
          <input
            v-model="form.current_password"
            type="password"
            class="w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600"
          />
          <div v-if="form.errors.current_password" class="text-red-500 text-sm mt-1">
            {{ form.errors.current_password }}
          </div>
        </div>

        <div>
          <label class="block text-green-700 font-semibold mb-1">
            <font-awesome-icon icon="key" /> New Password
          </label>
          <input
            v-model="form.password"
            type="password"
            class="w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600"
          />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
            {{ form.errors.password }}
          </div>
        </div>

        <div>
          <label class="block text-green-700 font-semibold mb-1">
            <font-awesome-icon icon="key" /> Confirm New Password
          </label>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600"
          />
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white font-semibold rounded-md shadow hover:bg-green-700 transition disabled:opacity-50"
        >
          <font-awesome-icon icon="arrow-rotate-right" class="mr-2" />
          {{ form.processing ? 'Updating...' : 'Update Password' }}
        </button>
      </form>
    </div>
  </div>
</template>
