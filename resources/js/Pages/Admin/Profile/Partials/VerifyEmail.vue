<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faEnvelope, faKey, faPaperPlane } from '@fortawesome/free-solid-svg-icons'

library.add(faEnvelope, faKey, faPaperPlane)

const { props } = usePage()
const user = props.user
const isGoogleUser = user.google_id !== null
const isVerified = computed(() => !!user.email_verified_at)

const form = useForm({
  password: '',
})

const submitting = ref(false)

const submit = () => {
  submitting.value = true
  form.post(route('admin.profile.sendVerificationEmail'), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Verification Email Sent',
        text: 'A verification email has been sent to your email address.',
        timer: 2000,
        showConfirmButton: false,
      })
      form.reset()
    },
    onError: () => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: form.errors.password || form.errors.message || 'Failed to send verification email.',
      })
    },
    onFinish: () => {
      submitting.value = false
    }
  })
}
</script>

<template>
  <div class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 rounded-2xl shadow-2xl transition hover:shadow-2xl mb-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-500 text-white px-6 py-4 flex items-center gap-2 rounded-t-2xl shadow">
      <font-awesome-icon icon="envelope" />
      <span class="font-semibold text-lg">Verify Email</span>
    </div>

    <!-- Body -->
    <div class="p-8">
      <!-- Success Alert -->
      <div v-if="isVerified" class="bg-green-100 text-green-800 px-4 py-3 rounded-md mb-4 font-semibold">
        Your email is already verified.
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <div v-if="!isGoogleUser && !isVerified" class="relative">
          <label class="block text-green-700 font-semibold mb-1">
            <font-awesome-icon icon="key" /> Password
          </label>
          <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="key" /></span>
          <input
            v-model="form.password"
            type="password"
            class="w-full rounded-full border border-green-500 shadow-sm focus:ring-2 focus:ring-green-300 focus:border-green-600 pl-10 py-2 transition-all duration-150"
            :disabled="isVerified"
            placeholder="Enter your password"
          />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
            {{ form.errors.password }}
          </div>
        </div>

        <button
          type="submit"
          :disabled="submitting || form.processing || isVerified"
          class="w-full inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-bold rounded-full shadow-xl text-lg transition-transform duration-200 hover:scale-105 disabled:opacity-50"
        >
          <font-awesome-icon icon="paper-plane" class="mr-2" />
          {{ submitting || form.processing ? 'Sending...' : 'Send Verification Email' }}
        </button>
      </form>
    </div>
  </div>
</template>
