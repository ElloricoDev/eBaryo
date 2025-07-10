<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBook, faList, faSearch, faCaretDown, faUser, faUserCircle, faUserEdit, faUserCheck, faUserTie, faUserPlus, faUserCog, faUserAlt, faUserFriends, faUserShield, faUserGraduate, faUserTag, faUserNurse, faUserMd, faUserSecret, faUserAstronaut, faUserInjured, faUserClock, faUserSlash, faUserLock, faUserMinus, faUserTimes, faUserAltSlash, faJournalWhills, faBookmark, faCommentDots, faListCheck, faRightFromBracket, faCopyright, faHouse } from '@fortawesome/free-solid-svg-icons'

library.add(faBook, faList, faSearch, faCaretDown, faUser, faUserCircle, faUserEdit, faUserCheck, faUserTie, faUserPlus, faUserCog, faUserAlt, faUserFriends, faUserShield, faUserGraduate, faUserTag, faUserNurse, faUserMd, faUserSecret, faUserAstronaut, faUserInjured, faUserClock, faUserSlash, faUserLock, faUserMinus, faUserTimes, faUserAltSlash, faJournalWhills, faBookmark, faCommentDots, faListCheck, faRightFromBracket, faCopyright, faHouse)

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

const closeDropdown = () => {
  toggleDropdown.value = false
}
</script>

<template>
  <div class="min-h-screen flex flex-col relative overflow-hidden">
    <!-- Mobile Sidebar & Overlay (moved above navbar for full coverage) -->
    <div v-if="toggle" class="fixed inset-0 z-50 bg-black bg-opacity-40 backdrop-blur-sm md:hidden" @click="toggle = false"></div>
    <!-- Animated Gradient Background -->
    <!-- Navbar -->
    <nav class="bg-green-600 shadow-lg border-b border-green-700 fixed top-0 left-0 w-full z-40">
      <div class=" mx-auto px-4 flex flex-wrap items-center justify-between h-16">
        <!-- Brand -->
        <Link class="text-white font-bold flex items-center gap-2 text-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-300" :href="route('home')">
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
        <div class="hidden md:flex md:items-center md:justify-between md:w-auto mt-4 md:mt-0 w-full">
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
              class="bg-green-700 text-white px-3 py-1 rounded-r hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-400"
            >
              <font-awesome-icon icon="search" />
            </button>
          </form>
          <!-- Dropdown -->
          <div class="relative md:ml-4" ref="dropdownRef">
            <button
              @mousedown.stop="toggleDropdown = !toggleDropdown"
              class="flex items-center gap-2 text-white px-4 py-2 bg-green-700 rounded hover:bg-green-800 w-full md:w-auto focus:outline-none focus:ring-2 focus:ring-green-300"
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
            <ul
              v-show="toggleDropdown"
              class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg border z-50 overflow-hidden"
            >
              <li><Link class="block px-4 py-2 text-gray-700 hover:bg-green-100 focus:bg-green-200" :href="route('home')"><font-awesome-icon icon="house" class="mr-2" /> Home</Link></li>
              <li><Link @click="closeDropdown" class="block px-4 py-2 text-gray-700 hover:bg-green-100 focus:bg-green-200" :href="route('user.profile.index')"><font-awesome-icon icon="user" class="mr-2" /> Profile</Link></li>
              <li><hr /></li>
              <li><Link @click="closeDropdown" class="block px-4 py-2 text-gray-700 hover:bg-green-100 focus:bg-green-200" :href="route('books.index')"><font-awesome-icon icon="journal-whills" class="mr-2" /> All Books</Link></li>
              <li><Link @click="closeDropdown" class="block px-4 py-2 text-gray-700 hover:bg-green-100 focus:bg-green-200" :href="route('books.saved')"><font-awesome-icon icon="bookmark" class="mr-2" /> Saved Books</Link></li>
              <li><hr /></li>
              <li><Link @click="closeDropdown" class="block px-4 py-2 text-gray-700 hover:bg-green-100 focus:bg-green-200" :href="route('feedback.create')"><font-awesome-icon icon="comment-dots" class="mr-2" /> Feedback</Link></li>
              <li><Link @click="closeDropdown" class="block px-4 py-2 text-gray-700 hover:bg-green-100 focus:bg-green-200" :href="route('feedback.my')"><font-awesome-icon icon="list-check" class="mr-2" /> My Feedback</Link></li>
              <li><hr /></li>
              <li>
                <Link @click="closeDropdown" class="block px-4 py-2 text-red-600 hover:bg-red-100 focus:bg-red-200" :href="route('logout')" method="post">
                  <font-awesome-icon icon="right-from-bracket" class="mr-2" /> Logout
                </Link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>
    <transition name="sidebar-slide">
      <aside v-if="toggle" class="fixed top-0 right-0 h-full w-64 bg-white shadow-2xl rounded-l-2xl z-50 flex flex-col md:hidden">
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
                class="bg-green-700 text-white px-3 py-1 rounded-r hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-400"
              >
                <font-awesome-icon icon="search" />
              </button>
            </form>
            <Link @mousedown="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 flex items-center gap-2" :href="route('home')">
              <font-awesome-icon icon="house" class="mr-2" /> Home
            </Link>
          </li>
          <li>
          </li>
          <li>
            <Link @mousedown="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 flex items-center gap-2" :href="route('user.profile.index')">
              <font-awesome-icon icon="user" class="mr-2" /> Profile
            </Link>
          </li>
          <li>
            <Link @mousedown="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 flex items-center gap-2" :href="route('books.index')">
              <font-awesome-icon icon="journal-whills" class="mr-2" /> All Books
            </Link>
          </li>
          <li>
            <Link @mousedown="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 flex items-center gap-2" :href="route('books.saved')">
              <font-awesome-icon icon="bookmark" class="mr-2" /> Saved Books
            </Link>
          </li>
          <li>
            <Link @mousedown="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 flex items-center gap-2" :href="route('feedback.create')">
              <font-awesome-icon icon="comment-dots" class="mr-2" /> Feedback
            </Link>
          </li>
          <li>
            <Link @mousedown="toggle = false" class="text-green-700 px-3 py-2 rounded-md hover:bg-green-100 focus:bg-green-200 flex items-center gap-2" :href="route('feedback.my')">
              <font-awesome-icon icon="list-check" class="mr-2" /> My Feedback
            </Link>
          </li>
          <li>
            <Link @mousedown="toggle = false" class="text-red-700 px-3 py-2 rounded-md hover:bg-red-100 focus:bg-red-200 flex items-center gap-2" :href="route('logout')" method="post">
              <font-awesome-icon icon="right-from-bracket" class="mr-2" /> Logout
            </Link>
          </li>
        </ul>
      </aside>
    </transition>
    <!-- Spacer for fixed navbar (matches navbar height) -->
    <div class="h-16 md:h-16"></div>
    <!-- Page Content -->
    <main class="flex-grow px-4 py-6">
      <slot />
    </main>
    <!-- Footer -->
    <footer class="bg-green-600 text-white text-center py-3 mt-auto shadow-sm border-t border-green-700">
      <small><font-awesome-icon icon="copyright" /> {{ new Date().getFullYear() }} eBaryo Library</small>
    </footer>
  </div>
</template>

<style scoped>
.sidebar-slide-enter-active, .sidebar-slide-leave-active {
  transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
}
.sidebar-slide-enter-from,
.sidebar-slide-leave-to {
  transform: translateX(100%);
}
.sidebar-slide-enter-to,
.sidebar-slide-leave-from {
  transform: translateX(0);
}
</style>
