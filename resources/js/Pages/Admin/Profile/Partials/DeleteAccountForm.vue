<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUserSlash, faKey, faTrash, faExclamationTriangle } from '@fortawesome/free-solid-svg-icons'

library.add(faUserSlash, faKey, faTrash, faExclamationTriangle)

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
  <div class="bg-white rounded-2xl shadow-sm border border-red-200 p-8">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
      <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
        <font-awesome-icon icon="user-slash" class="w-5 h-5 text-red-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-slate-800">Delete Account</h3>
        <p class="text-slate-600 text-sm">Permanently delete your account and all associated data</p>
      </div>
    </div>

    <!-- Warning Notice -->
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
          <font-awesome-icon icon="exclamation-triangle" class="w-4 h-4 text-red-600" />
        </div>
        <div>
          <h4 class="text-red-800 font-semibold mb-1">Warning: This action cannot be undone</h4>
          <p class="text-red-700 text-sm">
            Once you delete your account, all of your data will be permanently removed. 
            This includes your profile information, settings, and any other data associated with your account.
          </p>
        </div>
      </div>
    </div>

    <!-- Delete Form -->
    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <label class="block text-slate-700 font-semibold mb-2">Password Confirmation</label>
        <input
          v-model="form.password"
          type="password"
          placeholder="Enter your password to confirm account deletion"
          class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors"
        />
        <div v-if="form.errors.password" class="text-red-500 text-sm mt-2">
          {{ form.errors.password }}
        </div>
      </div>

      <button
        type="submit"
        :disabled="form.processing"
        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-2xl transition-all duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <font-awesome-icon icon="trash" class="w-4 h-4" />
        {{ form.processing ? 'Deleting Account...' : 'Delete My Account' }}
      </button>
    </form>
  </div>
</template>
