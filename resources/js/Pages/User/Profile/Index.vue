<script setup>
import { usePage, Link } from '@inertiajs/vue3'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import DeleteAccountForm from './Partials/DeleteAccountForm.vue'
import VerifyEmail from './Partials/VerifyEmail.vue'
import UserLayout from '@/Layouts/UserLayout.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faUserCircle, faUser, faPhone, faLocationDot, faEnvelope, faCircleCheck, faGraduationCap, faPenToSquare, faArrowLeft } from '@fortawesome/free-solid-svg-icons';
library.add(faUserCircle, faUser, faPhone, faLocationDot, faEnvelope, faCircleCheck, faGraduationCap, faPenToSquare, faArrowLeft);
import { onMounted } from 'vue';

defineOptions({
  layout: UserLayout
})

const { props } = usePage()
const user = props.user

onMounted(() => {
  if (window.location.search.includes('verified=1')) {
    window.location.replace(window.location.pathname); // Remove query param and reload
  }
});

function goBack() { window.history.back(); }
</script>

<template>
  <Head title="My Profile" />

  <div>
    <button @click="goBack" class="block sm:hidden mb-4 text-green-700 font-bold flex items-center gap-2">
      <font-awesome-icon icon="arrow-left" class="text-lg" />
      Back
    </button>
  </div>

  <div class="relative min-h-screen max-w-5xl mx-auto px-2 sm:px-4 py-4 sm:py-6 overflow-x-hidden">
    <!-- Animated Gradient Background -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 via-green-50 to-white animate-gradient-move"></div>
    <div class="animate-fade-in bg-white bg-opacity-90 rounded-2xl shadow-xl p-2 sm:p-6 mb-6">
      <h1 class="text-2xl font-bold text-green-700 mb-6 flex items-center gap-2">
        <font-awesome-icon icon="user-circle" /> User Profile
      </h1>

      <div class="flex flex-col items-center mb-6">
        <img
          v-if="user.avatar"
          :src="user.avatar"
          alt="Avatar"
          class="rounded-full border-4 border-green-300 w-24 h-24 object-cover shadow-lg ring-2 ring-green-200"
        />
        <img
          v-else
          src="https://ui-avatars.com/api/?name=N%2FA&background=ddd&color=555"
          alt="No Avatar"
          class="rounded-full border-4 border-green-300 w-24 h-24 object-cover shadow-lg ring-2 ring-green-200"
        />
      </div>

      <div class="space-y-2 text-gray-700">
        <p><span class="font-semibold text-green-700"><font-awesome-icon icon="user" /> User Name:</span> {{ user.user_name || 'N/A' }}</p>
        <div class="border-t border-green-100 my-2"></div>
        <p><span class="font-semibold text-green-700"><font-awesome-icon icon="user" /> First Name:</span> {{ user.first_name || 'N/A' }}</p>
        <p><span class="font-semibold text-green-700"><font-awesome-icon icon="user" /> Middle Name:</span> {{ user.middle_name || 'N/A' }}</p>
        <p><span class="font-semibold text-green-700"><font-awesome-icon icon="user" /> Last Name:</span> {{ user.last_name || 'N/A' }}</p>
        <div class="border-t border-green-100 my-2"></div>
        <p><span class="font-semibold text-green-700"><font-awesome-icon icon="phone" /> Contact Number:</span> {{ user.contact_number || 'N/A' }}</p>
        <p><span class="font-semibold text-green-700"><font-awesome-icon icon="location-dot" /> Address:</span> {{ user.address || 'N/A' }}</p>
        <div class="border-t border-green-100 my-2"></div>
        <p><span class="font-semibold text-green-700"><font-awesome-icon icon="envelope" /> Email:</span> {{ user.email || 'N/A' }}</p>
        <p>
          <span class="font-semibold text-green-700"><font-awesome-icon icon="circle-check" /> Email Status:</span>
          <span v-if="user.email_verified_at" class="text-green-600 font-semibold">Verified</span>
          <span v-else class="text-red-600 font-medium">Not Verified</span>
        </p>
        <div class="border-t border-green-100 my-2"></div>
        <p>
          <span class="font-semibold text-green-700"><font-awesome-icon icon="graduation-cap" /> Student:</span>
          <span>{{ user.student === 'yes' ? 'Yes' : 'No' }}</span>
        </p>
      </div>

      <Link
        :href="route('user.profile.edit')"
        class="mt-6 inline-flex items-center justify-center w-full px-4 py-2 bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700 focus:bg-green-800 transition animate-pulse-cta focus:outline-none focus:ring-2 focus:ring-green-400"
      >
        <font-awesome-icon icon="pen-to-square" class="mr-2" /> Edit Profile
      </Link>
    </div>

    <!-- Include additional account components -->
    <VerifyEmail />
    <UpdatePasswordForm />
    <DeleteAccountForm />
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
  0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
  50% { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
}
.animate-pulse-cta {
  animation: pulse-cta 2s infinite;
}
input:focus, button:focus, a:focus {
  outline: 2px solid #34d399;
  outline-offset: 2px;
}
</style>
