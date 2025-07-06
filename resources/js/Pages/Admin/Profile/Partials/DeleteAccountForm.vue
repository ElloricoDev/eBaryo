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
  <div class="border-2 border-green-600 rounded-2xl shadow transition hover:shadow-lg mt-6 mb-6">
    <!-- Header -->
    <div class="bg-green-600 text-white px-6 py-3 flex items-center gap-2 rounded-t-2xl">
      <font-awesome-icon icon="user-slash" />
      <span class="font-semibold">Delete Account</span>
    </div>

    <!-- Body -->
    <div class="p-6">
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Password Field -->
        <div>
          <label class="block text-green-700 font-semibold mb-1">
            <font-awesome-icon icon="key" /> Password
          </label>
          <input
            v-model="form.password"
            type="password"
            placeholder="Enter your password to confirm"
            class="w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600"
          />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
            {{ form.errors.password }}
          </div>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="form.processing"
          class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white font-semibold rounded-md shadow hover:bg-red-700 transition disabled:opacity-50"
        >
          <font-awesome-icon icon="trash" class="mr-2" />
          {{ form.processing ? 'Deleting...' : 'Delete Account' }}
        </button>
      </form>
    </div>
  </div>
</template>
