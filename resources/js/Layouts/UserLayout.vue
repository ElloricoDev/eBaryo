<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBook, faList, faSearch, faCaretDown, faUser, faUserCircle, faUserEdit, faUserCheck, faUserTie, faUserPlus, faUserCog, faUserAlt, faUserFriends, faUserShield, faUserGraduate, faUserTag, faUserNurse, faUserMd, faUserSecret, faUserAstronaut, faUserInjured, faUserClock, faUserSlash, faUserLock, faUserMinus, faUserTimes, faUserAltSlash, faJournalWhills, faBookmark, faCommentDots, faListCheck, faRightFromBracket, faCopyright } from '@fortawesome/free-solid-svg-icons'

library.add(faBook, faList, faSearch, faCaretDown, faUser, faUserCircle, faUserEdit, faUserCheck, faUserTie, faUserPlus, faUserCog, faUserAlt, faUserFriends, faUserShield, faUserGraduate, faUserTag, faUserNurse, faUserMd, faUserSecret, faUserAstronaut, faUserInjured, faUserClock, faUserSlash, faUserLock, faUserMinus, faUserTimes, faUserAltSlash, faJournalWhills, faBookmark, faCommentDots, faListCheck, faRightFromBracket, faCopyright)

const { props } = usePage()
const user = props.auth?.user || {}
const searchQuery = ref('')

const submitSearch = () => {
  window.dispatchEvent(new CustomEvent('user-search', { detail: searchQuery.value }))
}

watch(searchQuery, (val) => {
  if (val === '') {
    window.dispatchEvent(new CustomEvent('user-search', { detail: '' }))
  }
})

const toggle = ref(false)
const toggleDropdown = ref(false)

// Custom click-outside handler for dropdown
const dropdownRef = ref(null)

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    toggleDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})

watch(toggle, (val) => {
  if (val) {
    document.body.classList.add('overflow-hidden')
  } else {
    document.body.classList.remove('overflow-hidden')
  }
})
</script>

<template>
  <div class="min-h-screen flex flex-col relative overflow-hidden">
    <!-- Animated Gradient Background -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-100 via-green-50 to-white animate-gradient-move"></div>
    <!-- Navbar -->
    <nav class="bg-green-600 shadow-lg border-b border-green-700 fixed top-0 left-0 w-full z-50 transition-all duration-200 animate-slide-down">
      <div class="max-w-7xl mx-auto px-4 flex flex-wrap items-center justify-between h-16">
        <!-- Brand -->
        <Link class="text-white font-bold flex items-center gap-2 text-lg transition-transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-300" :href="route('home')">
          <font-awesome-icon icon="book" class="text-2xl" /> <span>eBaryo</span>
        </Link>

        <!-- Toggle for mobile -->
        <button
          @click="toggle = true"
          class="md:hidden text-white focus:outline-none focus:ring-2 focus:ring-green-300"
        >
          <font-awesome-icon icon="list" class="text-2xl" />
        </button>

        <!-- Desktop Navbar content -->
        <div class="hidden md:flex md:items-center md:justify-between md:w-auto mt-4 md:mt-0 w-full animate-slide-up">
          <!-- Search -->
          <form class="flex mx-auto md:mx-0 max-w-md w-full" @submit.prevent="submitSearch">
            <input
              v-model="searchQuery"
              type="search"
              placeholder="Search books..."
              class="flex-grow border border-green-500 rounded-l px-3 py-1 focus:outline-none focus:ring focus:ring-green-300 bg-white text-gray-900"
            />
            <button
              type="submit"
              class="bg-green-700 text-white px-3 py-1 rounded-r hover:bg-green-800 transition animate-pulse-cta focus:outline-none focus:ring-2 focus:ring-green-400"
            >
              <font-awesome-icon icon="search" />
            </button>
          </form>

          <!-- Dropdown -->
          <div class="relative md:ml-4" ref="dropdownRef">
            <button
              @click="toggleDropdown = !toggleDropdown"
              class="flex items-center gap-2 text-white px-4 py-2 bg-green-700 rounded hover:bg-green-800 transition w-full md:w-auto focus:outline-none focus:ring-2 focus:ring-green-300"
              type="button"
            >
              <img
                v-if="user.avatar"
                :src="user.avatar"
                alt="Avatar"
                class="w-8 h-8 rounded-full object-cover border border-white"
              />
              <img
                v-else
                src="https://ui-avatars.com/api/?name=N%2FA&background=ddd&color=555"
                alt="Avatar"
                class="w-8 h-8 rounded-full object-cover border border-white"
              />
              <span class="max-w-[7rem] truncate text-left" :title="user.user_name || 'User'">{{ user.user_name || 'User' }}</span>
              <font-awesome-icon icon="caret-down" class="ml-1" />
            </button>

            <!-- Dropdown menu -->
            <transition name="fade">
              <ul
                v-show="toggleDropdown"
                class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg border z-50 overflow-hidden animate-fade-in"
              >
                <li><Link class="block px-4 py-2 text-gray-700 hover:bg-green-100 focus:bg-green-200 transition" :href="route('user.profile.index')"><font-awesome-icon icon="user" class="mr-2" /> Profile</Link></li>
                <li><hr /></li>
                <li><Link class="block px-4 py-2 text-gray-700 hover:bg-green-100 focus:bg-green-200 transition" :href="route('books.index')"><font-awesome-icon icon="journal-whills" class="mr-2" /> All Books</Link></li>
                <li><Link class="block px-4 py-2 text-gray-700 hover:bg-green-100 focus:bg-green-200 transition" :href="route('books.saved')"><font-awesome-icon icon="bookmark" class="mr-2" /> Saved Books</Link></li>
                <li><hr /></li>
                <li><Link class="block px-4 py-2 text-gray-700 hover:bg-green-100 focus:bg-green-200 transition" :href="route('feedback.create')"><font-awesome-icon icon="comment-dots" class="mr-2" /> Feedback</Link></li>
                <li><Link class="block px-4 py-2 text-gray-700 hover:bg-green-100 focus:bg-green-200 transition" :href="route('feedback.my')"><font-awesome-icon icon="list-check" class="mr-2" /> My Feedback</Link></li>
                <li><hr /></li>
                <li>
                  <Link class="block px-4 py-2 text-red-600 hover:bg-red-100 focus:bg-red-200 transition" :href="route('logout')" method="post">
                    <font-awesome-icon icon="right-from-bracket" class="mr-2" /> Logout
                  </Link>
                </li>
              </ul>
            </transition>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Sidebar & Overlay -->
    <transition name="fade">
      <div v-if="toggle" class="fixed inset-0 z-40 bg-black bg-opacity-40 md:hidden" @click="toggle = false"></div>
    </transition>
    <transition name="slide">
      <aside v-if="toggle" class="fixed top-0 right-0 h-full w-64 bg-white shadow-2xl rounded-l-2xl z-50 flex flex-col md:hidden transition-transform duration-300 animate-slide-up">
        <div class="flex items-center justify-between px-4 h-16 border-b">
          <span class="font-bold text-green-700 text-lg flex items-center gap-2">
            <font-awesome-icon icon="book" class="text-xl" /> eBaryo
          </span>
          <button @click="toggle = false" class="text-green-700 hover:text-green-900 text-2xl focus:outline-none">
            &times;
          </button>
        </div>
        <ul class="flex flex-col gap-2 p-4">
          <li>
            <form class="flex max-w-md w-full mb-2" @submit.prevent="submitSearch">
              <input
                v-model="searchQuery"
                type="search"
                placeholder="Search books..."
                class="flex-grow border border-green-500 rounded-l px-3 py-1 focus:outline-none focus:ring focus:ring-green-300 bg-white text-gray-900"
              />
              <button
                type="submit"
                class="bg-green-700 text-white px-3 py-1 rounded-r hover:bg-green-800 transition animate-pulse-cta focus:outline-none focus:ring-2 focus:ring-green-400"
              >
                <font-awesome-icon icon="search" />
              </button>
            </form>
          </li>
          <li>
            <Link @click="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 transition flex items-center gap-2" :href="route('user.profile.index')">
              <font-awesome-icon icon="user" class="mr-2" /> Profile
            </Link>
          </li>
          <li>
            <Link @click="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 transition flex items-center gap-2" :href="route('books.index')">
              <font-awesome-icon icon="journal-whills" class="mr-2" /> All Books
            </Link>
          </li>
          <li>
            <Link @click="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 transition flex items-center gap-2" :href="route('books.saved')">
              <font-awesome-icon icon="bookmark" class="mr-2" /> Saved Books
            </Link>
          </li>
          <li>
            <Link @click="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 transition flex items-center gap-2" :href="route('feedback.create')">
              <font-awesome-icon icon="comment-dots" class="mr-2" /> Feedback
            </Link>
          </li>
          <li>
            <Link @click="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 transition flex items-center gap-2" :href="route('feedback.my')">
              <font-awesome-icon icon="list-check" class="mr-2" /> My Feedback
            </Link>
          </li>
          <li>
            <Link @click="toggle = false" class="text-red-600 px-3 py-2 rounded-md hover:bg-red-100 focus:bg-red-200 transition flex items-center gap-2" :href="route('logout')" method="post">
              <font-awesome-icon icon="right-from-bracket" class="mr-2" /> Logout
            </Link>
          </li>
        </ul>
      </aside>
    </transition>

    <!-- Spacer for fixed navbar (matches navbar height) -->
    <div class="h-16 md:h-16"></div>

    <!-- Page Content -->
    <main class="flex-grow px-4 py-6 max-w-7xl mx-auto w-full animate-slide-up bg-white bg-opacity-90 rounded-2xl shadow-lg mt-4 mb-4">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-green-600 text-white text-center py-3 mt-auto shadow-sm border-t border-green-700">
      <small><font-awesome-icon icon="copyright" /> {{ new Date().getFullYear() }} eBaryo Library</small>
    </footer>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.animate-fade-in {
  animation: fadeIn 0.2s;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.max-w-\[7rem\] {
  max-width: 7rem;
}
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s;
}
.slide-enter-from, .slide-leave-to {
  transform: translateX(100%);
}
@keyframes gradient-move {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
.animate-gradient-move {
  background-size: 200% 200%;
  animation: gradient-move 8s ease-in-out infinite;
}
@keyframes slide-up {
  from { transform: translateY(30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.animate-slide-up {
  animation: slide-up 0.7s cubic-bezier(0.4,0,0.2,1) both;
}
@keyframes slide-down {
  from { transform: translateY(-30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.animate-slide-down {
  animation: slide-down 0.7s cubic-bezier(0.4,0,0.2,1) both;
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
