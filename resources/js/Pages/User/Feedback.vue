<script setup>
import { useForm, Head, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Swal from 'sweetalert2';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faCommentDots, faArrowLeft } from '@fortawesome/free-solid-svg-icons';
library.add(faCommentDots, faArrowLeft);

defineOptions({ layout: UserLayout });

const form = useForm({
  message: ''
});

const { props } = usePage();
const user = props.auth?.user || null;
const isVerified = user && user.email_verified_at;

function submit() {
  form.post(route('feedback.store'), {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Thank you!',
        text: 'Your feedback has been submitted.',
        timer: 2000,
        showConfirmButton: false
      });
      form.reset();
    }
  });
}

function goBack() { window.history.back(); }
</script>

<template>
  <Head title="Submit Feedback" />
  <div>
    <button @click="goBack" class="block sm:hidden mb-4 text-green-700 font-bold flex items-center gap-2">
      <font-awesome-icon icon="arrow-left" class="text-lg" />
      Back
    </button>
  </div>
  <div class="relative min-h-screen max-w-4xl mx-auto px-2 sm:px-4 py-4 sm:py-6 overflow-x-hidden">
    <!-- Animated Gradient Background -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 via-green-50 to-white animate-gradient-move"></div>
    <div class="animate-fade-in bg-white bg-opacity-90 rounded-2xl shadow-xl p-2 sm:p-6">
      <h1 class="text-2xl font-bold mb-6 text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="comment-dots" /> Submit Feedback
      </h1>

      <div class="bg-white border-2 border-green-600 rounded-xl shadow-md p-2 sm:p-6 hover:border-green-700 transition">
        <form @submit.prevent="submit">
          <!-- Feedback Textarea -->
          <div class="mb-4">
            <label for="message" class="block text-green-700 font-medium mb-2">Your Feedback</label>
            <textarea
              v-model="form.message"
              id="message"
              rows="5"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 focus:border-green-600 transition"
              placeholder="Share your thoughts, suggestions, or issues..."
            ></textarea>
            <div v-if="form.errors.message" class="text-red-600 text-sm mt-1 animate-fade-in">{{ form.errors.message }}</div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="form.processing || !isVerified"
            class="w-full bg-green-600 hover:bg-green-700 focus:bg-green-800 text-white py-2 rounded-md shadow-sm transition animate-pulse-cta focus:outline-none focus:ring-2 focus:ring-green-400 disabled:opacity-50 font-semibold text-base"
          >
            Submit
          </button>
          <div v-if="!isVerified" class="text-yellow-600 text-center mt-2">Please verify your email to submit feedback.</div>

          <!-- Flash Success Message -->
          <div
            v-if="$page.props.flash?.success"
            class="mt-4 text-sm text-green-700 bg-green-100 border border-green-300 rounded-md p-3 animate-fade-in"
          >
            {{ $page.props.flash.success }}
          </div>
        </form>
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
@keyframes pulse-cta {
  0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
  50% { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
}
.animate-pulse-cta {
  animation: pulse-cta 2s infinite;
}
input:focus, textarea:focus, button:focus, a:focus {
  outline: 2px solid #34d399;
  outline-offset: 2px;
}
</style>
