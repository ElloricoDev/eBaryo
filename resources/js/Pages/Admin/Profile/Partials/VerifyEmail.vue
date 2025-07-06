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
  <div class="border-2 border-green-600 rounded-2xl shadow transition hover:shadow-lg mb-6">
    <!-- Header -->
    <div class="bg-green-600 text-white px-6 py-3 flex items-center gap-2 rounded-t-2xl">
      <font-awesome-icon icon="envelope" />
      <span class="font-semibold">Verify Email</span>
    </div>

    <!-- Body -->
    <div class="p-6">
      <!-- Success Alert -->
      <div v-if="isVerified" class="bg-green-100 text-green-800 px-4 py-3 rounded-md mb-4">
        Your email is already verified.
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-4">
        <div v-if="!isGoogleUser && !isVerified">
          <label class="block text-green-700 font-semibold mb-1">
            <font-awesome-icon icon="key" /> Password
          </label>
          <input
            v-model="form.password"
            type="password"
            class="w-full rounded-md border-gray-300 shadow-sm focus:ring-green-600 focus:border-green-600"
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
          class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white font-semibold rounded-md shadow hover:bg-green-700 transition disabled:opacity-50"
        >
          <font-awesome-icon icon="paper-plane" class="mr-2" />
          {{ submitting || form.processing ? 'Sending...' : 'Send Verification Email' }}
        </button>
      </form>
    </div>
  </div>
</template>
