<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { onMounted } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faCommentDots, faCircle, faInbox, faArrowLeft } from '@fortawesome/free-solid-svg-icons';
library.add(faCommentDots, faCircle, faInbox, faArrowLeft);

defineOptions({ layout: UserLayout });

const props = defineProps({
  feedbacks: Array,
  hasNewResponses: Boolean
});

onMounted(() => {
  if (props.hasNewResponses) {
    Swal.fire({
      icon: 'info',
      title: 'New Response!',
      text: 'An admin has responded to your feedback.',
      timer: 2500,
      showConfirmButton: false
    });
  }
});

const formatDate = (dateStr) => {
  const date = new Date(dateStr);
  return new Intl.DateTimeFormat('en-PH', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(date);
};

const goBack = () => {
  window.history.length > 1
    ? window.history.back()
    : Inertia.visit('/home');
}
</script>

<template>
  <Head title="My Feedback" />
  <div class="relative min-h-screen">
    <button @click="goBack" class="block sm:hidden mb-4 text-green-700 font-bold flex items-center gap-2">
      <font-awesome-icon icon="arrow-left" class="text-lg" />
      Back
    </button>
    <!-- Animated Gradient Background -->
    <div class="animate-fade-in bg-white bg-opacity-90 rounded-2xl shadow-xl p-2 sm:p-6">
      <h1 class="text-2xl font-bold text-green-700 mb-6 flex items-center gap-2">
        <font-awesome-icon icon="comment-dots" /> My Feedback
      </h1>

      <div class="bg-white border-2 border-green-600 rounded-xl shadow-md hover:border-green-700 transition">
        <div class="overflow-x-auto">
          <table class="min-w-full table-auto text-sm text-left text-gray-700">
            <thead class="bg-green-100 text-green-800 uppercase text-xs font-semibold">
              <tr>
                <th scope="col" class="px-4 py-3">Message</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3">Admin Response</th>
                <th scope="col" class="px-4 py-3">Responded At</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="fb in props.feedbacks"
                :key="fb.id"
                class="hover:bg-green-50 focus-within:bg-green-100 border-b border-gray-200 transition group"
              >
                <td class="px-4 py-3 whitespace-pre-wrap">{{ fb.message }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center gap-1">
                    <font-awesome-icon icon="circle" :class="fb.status === 'pending' ? 'text-yellow-400' : 'text-green-500'" />
                    <span :class="fb.status === 'pending' ? 'text-yellow-500' : 'text-green-600 font-semibold'">
                      {{ fb.status.charAt(0).toUpperCase() + fb.status.slice(1) }}
                    </span>
                  </span>
                </td>
                <td class="px-4 py-3 whitespace-pre-wrap">
                  <span v-if="fb.response" class="text-green-700">{{ fb.response }}</span>
                  <span v-else class="text-gray-400 italic">No response yet</span>
                </td>
                <td class="px-4 py-3">
                  {{ fb.responded_at ? formatDate(fb.responded_at) : '-' }}
                </td>
              </tr>

              <tr v-if="!props.feedbacks.length">
                <td colspan="4" class="px-4 py-10 text-center text-gray-500 animate-fade-in">
                  <div class="flex flex-col items-center justify-center">
                    <font-awesome-icon icon="inbox" class="text-green-200 text-6xl mb-4" />
                    <span class="text-lg">No feedback found.<br><span class="text-sm text-gray-400">Your submitted feedback will appear here.</span></span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
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
input:focus, button:focus, a:focus, tr:focus-within {
  outline: 2px solid #34d399;
  outline-offset: 2px;
}
</style>
