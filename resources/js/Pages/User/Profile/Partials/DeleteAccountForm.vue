<script setup>
import { useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faUserXmark, faKey, faTrash } from '@fortawesome/free-solid-svg-icons';
library.add(faUserXmark, faKey, faTrash);

const form = useForm({
  password: ''
})

const submit = async () => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: 'This action is permanent. Your account will be deleted.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel',
  })

  if (result.isConfirmed) {
    form.delete(route('user.profile.destroy'), {
      preserveScroll: true,
      onError: () => {
        // Optional: handle error
      }
    })
  }
}
</script>

<template>
  <div class="relative animate-fade-in bg-white bg-opacity-90 border-2 border-green-600 rounded-2xl shadow-xl mt-6 mb-6 overflow-hidden">
    <!-- Animated Gradient Background -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 via-green-50 to-white animate-gradient-move"></div>
    <div class="bg-green-600 text-white px-4 py-3 rounded-t-2xl flex items-center gap-2 font-medium">
      <font-awesome-icon icon="user-xmark" /> Delete Account
    </div>
    <div class="border-t border-green-100"></div>
    <div class="p-6">
      <p class="text-gray-700 mb-4">This action is permanent. Please enter your password to confirm.</p>
      <form @submit.prevent="submit" class="space-y-4">
        <div class="relative">
          <input
            type="password"
            v-model="form.password"
            id="delete_password"
            class="peer w-full border border-green-300 rounded px-3 py-2 bg-transparent focus:border-green-600 focus:ring-2 focus:ring-green-200 transition placeholder-transparent"
            placeholder="Password"
          />
          <label for="delete_password" class="absolute left-3 top-2 text-green-700 font-medium pointer-events-none transition-all peer-placeholder-shown:top-2 peer-placeholder-shown:text-base peer-focus:-top-4 peer-focus:text-xs peer-focus:text-green-600 bg-white px-1">
            <font-awesome-icon icon="key" /> Password
          </label>
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
            {{ form.errors.password }}
          </div>
        </div>

        <button
          type="submit"
          class="bg-red-600 hover:bg-red-700 focus:bg-red-800 text-white w-full py-2 px-4 rounded shadow-sm font-semibold flex items-center justify-center gap-2 animate-pulse-cta focus:outline-none focus:ring-2 focus:ring-red-400 transition disabled:opacity-60 disabled:cursor-not-allowed"
          :disabled="form.processing"
        >
          <font-awesome-icon icon="trash" />
          <span v-if="form.processing">
            <svg class="inline w-4 h-4 animate-spin mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
            Deleting...
          </span>
          <span v-else>Delete Account</span>
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
@keyframes gradient-move {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
.animate-gradient-move {
  background-size: 200% 200%;
  animation: gradient-move 8s ease-in-out infinite;
}
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fade-in {
  animation: fade-in 1s both;
}
@keyframes pulse-cta {
  0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
  50% { box-shadow: 0 0 0 8px rgba(239,68,68,0); }
}
.animate-pulse-cta {
  animation: pulse-cta 2s infinite;
}
input:focus, button:focus, a:focus {
  outline: 2px solid #ef4444;
  outline-offset: 2px;
}
</style>
