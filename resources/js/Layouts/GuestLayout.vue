<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBook, faHouse, faInfoCircle, faStar, faCommentDots, faRightToBracket, faCopyright, faUserCircle, faXmark } from '@fortawesome/free-solid-svg-icons'
import { faFacebook, faTwitter, faInstagram } from '@fortawesome/free-brands-svg-icons'

const toggle = ref(false)

// Prevent background scroll when sidebar is open
watch(toggle, (val) => {
  if (val) {
    document.body.classList.add('overflow-hidden')
  } else {
    document.body.classList.remove('overflow-hidden')
  }
})

library.add(faBook, faHouse, faInfoCircle, faStar, faCommentDots, faRightToBracket, faCopyright, faUserCircle, faXmark, faFacebook, faTwitter, faInstagram)
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-green-700 via-green-600 to-green-500 shadow-lg border-b fixed w-full z-50">
      <div class="container mx-auto px-4 flex flex-wrap items-center justify-between h-16">
        <Link class="text-white font-bold flex items-center gap-2 text-lg transition-transform hover:scale-105" :href="route('welcome')">
          <font-awesome-icon icon="book" class="text-2xl" /> <span>eBaryo Library</span>
        </Link>
        <button
          class="text-white focus:outline-none md:hidden relative z-50 group"
          @click="toggle = !toggle"
          :aria-expanded="toggle.toString()"
          aria-label="Toggle navigation menu"
        >
          <span :class="['block w-6 h-0.5 bg-white transition-transform duration-300', toggle ? 'rotate-45 translate-y-2' : '']"></span>
          <span :class="['block w-6 h-0.5 bg-white my-1 transition-opacity duration-300', toggle ? 'opacity-0' : '']"></span>
          <span :class="['block w-6 h-0.5 bg-white transition-transform duration-300', toggle ? '-rotate-45 -translate-y-2' : '']"></span>
        </button>

        <!-- Desktop Nav -->
        <ul
          class="hidden md:flex gap-4 items-center"
        >
          <li>
            <Link class="text-white px-3 py-2 rounded-md hover:bg-green-700 transition" :href="route('welcome')">
              <font-awesome-icon icon="house" /> Home
            </Link>
          </li>
          <li>
            <a href="#about" class="text-white px-3 py-2 rounded-md hover:bg-green-700 transition">
              <font-awesome-icon icon="info-circle" /> About
            </a>
          </li>
          <li>
            <a href="#features" class="text-white px-3 py-2 rounded-md hover:bg-green-700 transition">
              <font-awesome-icon icon="star" /> Features
            </a>
          </li>
          <li>
            <a href="#testimonials" class="text-white px-3 py-2 rounded-md hover:bg-green-700 transition">
              <font-awesome-icon icon="comment-dots" /> Testimonials
            </a>
          </li>
          <li>
            <Link class="text-white px-3 py-2 rounded-md hover:bg-green-700 transition" :href="route('login')">
              <font-awesome-icon icon="right-to-bracket" /> Login
            </Link>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Mobile Sidebar & Overlay -->
    <transition name="fade">
      <div v-if="toggle" class="fixed inset-0 z-40 bg-black bg-opacity-40 backdrop-blur-sm md:hidden transition-all duration-300" @click="toggle = false"></div>
    </transition>
    <transition name="slide">
      <aside v-if="toggle" class="fixed top-0 right-0 h-full w-72 bg-white shadow-2xl z-50 flex flex-col md:hidden transition-transform duration-300 rounded-l-2xl">
        <div class="flex flex-col items-center gap-2 px-4 h-28 border-b justify-center bg-gradient-to-r from-green-100 to-green-50">
          <font-awesome-icon icon="user-circle" class="text-4xl text-green-600 mb-1" />
          <span class="font-semibold text-green-700 text-base">Welcome, Guest!</span>
          <button @click="toggle = false" class="absolute top-4 right-4 text-green-700 hover:text-green-900 text-2xl focus:outline-none" aria-label="Close sidebar">
            <font-awesome-icon icon="xmark" />
          </button>
        </div>
        <ul class="flex flex-col gap-2 p-4 mt-2">
          <li>
            <Link @click="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 transition flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-green-400" :href="route('welcome')">
              <font-awesome-icon icon="house" /> Home
            </Link>
          </li>
          <li>
            <a @click="toggle = false" href="#about" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 transition flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-green-400">
              <font-awesome-icon icon="info-circle" /> About
            </a>
          </li>
          <li>
            <a @click="toggle = false" href="#features" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 transition flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-green-400">
              <font-awesome-icon icon="star" /> Features
            </a>
          </li>
          <li>
            <a @click="toggle = false" href="#testimonials" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 transition flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-green-400">
              <font-awesome-icon icon="comment-dots" /> Testimonials
            </a>
          </li>
          <li>
            <Link @click="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 transition flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-green-400" :href="route('login')">
              <font-awesome-icon icon="right-to-bracket" /> Login
            </Link>
          </li>
        </ul>
      </aside>
    </transition>

    <!-- Spacer for fixed navbar -->
    <div class="h-16"></div>

    <!-- Page Content -->
    <main class="flex-grow container mx-auto px-4 py-6">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-green-600 text-white text-center py-3 mt-auto shadow-sm border-t border-green-700">
      <div class="flex flex-col md:flex-row items-center justify-center gap-2">
        <small class="flex items-center gap-1"><font-awesome-icon icon="copyright" /> {{ new Date().getFullYear() }} eBaryo Library. All rights reserved.</small>
        <div class="flex gap-3 mt-1 md:mt-0">
          <a href="#" class="hover:text-green-300 transition" aria-label="Facebook"><font-awesome-icon :icon="['fab', 'facebook']" /></a>
          <a href="#" class="hover:text-green-300 transition" aria-label="Twitter"><font-awesome-icon :icon="['fab', 'twitter']" /></a>
          <a href="#" class="hover:text-green-300 transition" aria-label="Instagram"><font-awesome-icon :icon="['fab', 'instagram']" /></a>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* Optional: Font smoothing and base adjustments */
html, body {
  height: 100%;
  font-family: 'Inter', sans-serif;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s cubic-bezier(0.4,0,0.2,1);
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.slide-enter-active, .slide-leave-active {
  transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
}
.slide-enter-from, .slide-leave-to {
  transform: translateX(100%);
}
button:focus, a:focus {
  outline: 2px solid #34d399;
  outline-offset: 2px;
}
</style>
