<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUserSlash, faKey, faTrash } from '@fortawesome/free-solid-svg-icons'

library.add(faUserSlash, faKey, faTrash)

const { props } = usePage()
const user = props.user

const form = useForm({
  password: '',
})

const submit = () => {
  form.delete(route('admin.profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Account Deleted',
        text: 'Your account has been successfully deleted.',
        timer: 2000,
        showConfirmButton: false,
      })
    }
  })
}
</script>

<template>
  <div class="bg-gradient-to-br from-white via-red-50 to-red-100 border-2 border-red-400 rounded-2xl shadow-2xl transition hover:shadow-2xl mt-10 mb-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-600 to-red-500 text-white px-6 py-4 flex items-center gap-2 rounded-t-2xl shadow">
      <font-awesome-icon icon="user-slash" />
      <span class="font-semibold text-lg">Delete Account</span>
    </div>

    <!-- Body -->
    <div class="p-8">
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Password Field -->
        <div class="relative">
          <label class="block text-red-700 font-semibold mb-1">
            <font-awesome-icon icon="key" /> Password
          </label>
          <span class="absolute left-3 top-10 text-red-400"><font-awesome-icon icon="key" /></span>
          <input
            v-model="form.password"
            type="password"
            placeholder="Enter your password to confirm"
            class="w-full rounded-full border border-red-400 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-600 pl-10 py-2 transition-all duration-150"
          />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
            {{ form.errors.password }}
          </div>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="form.processing"
          class="w-full inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-bold rounded-full shadow-xl text-lg transition-transform duration-200 hover:scale-105 disabled:opacity-50"
        >
          <font-awesome-icon icon="trash" class="mr-2" />
          {{ form.processing ? 'Deleting...' : 'Delete Account' }}
        </button>
      </form>
    </div>
  </div>
</template>
