<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faEnvelope, faKey, faPaperPlane, faCheckCircle, faExclamationTriangle } from '@fortawesome/free-solid-svg-icons'

library.add(faEnvelope, faKey, faPaperPlane, faCheckCircle, faExclamationTriangle)

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
  <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
      <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
        <font-awesome-icon icon="envelope" class="w-5 h-5 text-green-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-slate-800">Email Verification</h3>
        <p class="text-slate-600 text-sm">Verify your email address to secure your account</p>
      </div>
    </div>

    <!-- Status Display -->
    <div v-if="isVerified" class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
          <font-awesome-icon icon="check-circle" class="w-4 h-4 text-green-600" />
        </div>
        <div>
          <h4 class="text-green-800 font-semibold">Email Verified</h4>
          <p class="text-green-700 text-sm">Your email address is verified and secure</p>
        </div>
      </div>
    </div>

    <!-- Verification Form -->
    <div v-else>
      <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
            <font-awesome-icon icon="exclamation-triangle" class="w-4 h-4 text-yellow-600" />
          </div>
          <div>
            <h4 class="text-yellow-800 font-semibold">Email Not Verified</h4>
            <p class="text-yellow-700 text-sm">Please verify your email address to secure your account</p>
          </div>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div v-if="!isGoogleUser" class="relative">
          <label class="block text-slate-700 font-semibold mb-2">Password Confirmation</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-colors"
            placeholder="Enter your password to verify"
          />
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-2">
            {{ form.errors.password }}
          </div>
        </div>

        <button
          type="submit"
          :disabled="submitting || form.processing"
          class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-2xl transition-all duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <font-awesome-icon icon="paper-plane" class="w-4 h-4" />
          {{ submitting || form.processing ? 'Sending Verification Email...' : 'Send Verification Email' }}
        </button>
      </form>
    </div>
  </div>
</template>
