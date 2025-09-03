<script setup>
import { usePage } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import { ref, watch, onMounted, onBeforeUnmount } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faBook,
    faList,
    faSearch,
    faCaretDown,
    faUser,
    faUserCircle,
    faUserEdit,
    faUserCheck,
    faUserTie,
    faUserPlus,
    faUserCog,
    faUserAlt,
    faUserFriends,
    faUserShield,
    faUserGraduate,
    faUserTag,
    faUserNurse,
    faUserMd,
    faUserSecret,
    faUserAstronaut,
    faUserInjured,
    faUserClock,
    faUserSlash,
    faUserLock,
    faUserMinus,
    faUserTimes,
    faUserAltSlash,
    faJournalWhills,
    faBookmark,
    faCommentDots,
    faListCheck,
    faRightFromBracket,
    faCopyright,
    faHouse,
    faBookOpen,
} from "@fortawesome/free-solid-svg-icons";
import { faFacebook, faTwitter, faInstagram } from "@fortawesome/free-brands-svg-icons";

library.add(
    faBook,
    faList,
    faSearch,
    faCaretDown,
    faUser,
    faUserCircle,
    faUserEdit,
    faUserCheck,
    faUserTie,
    faUserPlus,
    faUserCog,
    faUserAlt,
    faUserFriends,
    faUserShield,
    faUserGraduate,
    faUserTag,
    faUserNurse,
    faUserMd,
    faUserSecret,
    faUserAstronaut,
    faUserInjured,
    faUserClock,
    faUserSlash,
    faUserLock,
    faUserMinus,
    faUserTimes,
    faUserAltSlash,
    faJournalWhills,
    faBookmark,
    faCommentDots,
    faListCheck,
    faRightFromBracket,
    faCopyright,
    faHouse,
    faBookOpen,
    faFacebook,
    faTwitter,
    faInstagram
);

const { props } = usePage();
const user = props.auth?.user || {};
const searchQuery = ref("");
const newResponsesCount = props.newResponsesCount || 0;
const categories = props.categories || [];
const browseDropdown = ref(false);

const submitSearch = () => {
    try {
        // Prefer server-side search on the Books listing
        const q = (searchQuery.value || "").trim();
        if (q.length > 0) {
            Inertia.visit(route('books.index', { q }));
            return;
        }
        Inertia.visit(route('books.index'));
    } catch (e) {
        // Fallback to client event if route helper is unavailable
        window.dispatchEvent(new CustomEvent('user-search', { detail: searchQuery.value }));
    }
};

let searchDebounceId = null;
watch(searchQuery, (val) => {
    if (val === "") {
        if (searchDebounceId) {
            clearTimeout(searchDebounceId);
            searchDebounceId = null;
        }
        window.dispatchEvent(new CustomEvent("user-search", { detail: "" }));
        return;
    }
    if (searchDebounceId) clearTimeout(searchDebounceId);
    searchDebounceId = setTimeout(() => {
        window.dispatchEvent(
            new CustomEvent("user-search", { detail: val })
        );
    }, 300);
});

const toggle = ref(false);
const toggleDropdown = ref(false);

const dropdownRef = ref(null);

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        toggleDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener("mousedown", handleClickOutside);
    if (window.Echo && user.id) {
        window.Echo.private("user-feedback." + user.id).listen(
            "FeedbackUpdated",
            (e) => {
                Inertia.reload({ only: ["newResponsesCount"] });
            }
        );
    }
});

onBeforeUnmount(() => {
    document.removeEventListener("mousedown", handleClickOutside);
});

watch(toggle, (val) => {
    if (val) {
        document.body.classList.add("overflow-hidden");
    } else {
        document.body.classList.remove("overflow-hidden");
    }
});

const closeDropdown = () => {
    toggleDropdown.value = false;
};

const closeBrowseDropdown = () => {
    browseDropdown.value = false;
};
</script>

<template>
    <div class="min-h-screen flex flex-col relative overflow-hidden bg-gradient-to-br from-slate-50 via-white to-green-50">
        <!-- Mobile Overlay -->
        <div v-if="toggle" class="fixed inset-0 z-50 bg-black bg-opacity-40 backdrop-blur-sm md:hidden"
            @click="toggle = false"></div>
        
        <!-- Enhanced Navigation -->
        <nav class="bg-gradient-to-r from-green-700 via-green-600 to-green-500 shadow-xl border-b border-green-800 fixed top-0 left-0 w-full z-40">
            <div class="mx-auto px-6 flex flex-wrap items-center justify-between h-16">
                <!-- Logo and Browse Section -->
                <div class="flex items-center gap-4">
                    <Link
                        class="text-white font-bold flex items-center gap-3 text-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200"
                        :href="route('home')">
                        <img src="/favicon1.png" alt="" class="w-8 h-8 md:w-10 md:h-10" />
                        <span class="hidden sm:block">eBaryo Library</span>
                    </Link>
                    
                    <!-- Browse Dropdown -->
                    <div class="relative hidden md:block">
                        <button @mousedown.stop="browseDropdown = !browseDropdown"
                            class="flex items-center gap-2 text-white px-4 py-2 bg-green-700/80 backdrop-blur-sm rounded-xl hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200"
                            type="button">
                            <font-awesome-icon icon="journal-whills" class="mr-2" />
                            Browse Books
                            <font-awesome-icon icon="caret-down" class="ml-1 transition-transform duration-200" :class="browseDropdown ? 'rotate-180' : ''" />
                        </button>
                        <ul v-show="browseDropdown"
                            class="absolute left-0 mt-3 w-64 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden"
                            @mouseleave="closeBrowseDropdown">
                            <li v-for="category in categories" :key="category.id">
                                <Link class="block px-4 py-3 text-slate-700 hover:bg-green-50 hover:text-green-700 focus:bg-green-100 transition-all duration-200" :href="route('books.index', { category: category.id })" @click="closeBrowseDropdown">
                                    {{ category.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="toggle = true"
                    class="md:hidden text-white focus:outline-none focus:ring-2 focus:ring-green-300 hover:scale-105 transition-transform duration-200">
                    <font-awesome-icon icon="list" class="text-2xl" />
                </button>

                <!-- Search and User Menu -->
                <div class="hidden md:flex md:items-center md:justify-between md:w-auto mt-4 md:mt-0 w-full">
                    <!-- Enhanced Search Bar -->
                    <form class="flex mx-auto md:mx-0 max-w-md w-full" @submit.prevent="submitSearch">
                        <div class="relative w-full">
                            <input v-model="searchQuery" type="search" placeholder="Search books..."
                                class="w-full pl-12 pr-4 py-2.5 border border-green-500 rounded-2xl focus:outline-none focus:ring-2 focus:ring-green-300 focus:border-transparent bg-white/90 backdrop-blur-sm text-slate-700 placeholder-slate-500 transition-all duration-200" />
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <font-awesome-icon icon="search" class="text-slate-400" />
                            </div>
                        </div>
                        <button type="submit"
                            class="ml-2 bg-green-700 text-white px-4 py-2.5 rounded-2xl hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-400 transition-all duration-200 hover:shadow-lg">
                            <font-awesome-icon icon="search" />
                        </button>
                    </form>

                    <!-- User Dropdown -->
                    <div class="relative md:ml-6" ref="dropdownRef">
                        <button @mousedown.stop="toggleDropdown = !toggleDropdown"
                            class="flex items-center gap-3 text-white px-4 py-2 bg-green-700/80 backdrop-blur-sm rounded-xl hover:bg-green-800 w-full md:w-auto focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200 relative group"
                            type="button">
                            <img v-if="user.avatar" :src="user.avatar" alt="Avatar"
                                class="w-8 h-8 rounded-full object-cover border-2 border-white shadow-sm" />
                            <img v-else src="https://ui-avatars.com/api/?name=N%2FA&background=ddd&color=555"
                                alt="Avatar" class="w-8 h-8 rounded-full object-cover border-2 border-white shadow-sm" />
                            <span class="max-w-[7rem] truncate text-left font-medium" :title="user.user_name || 'User'">
                                {{ user.user_name || "User" }}
                            </span>
                            <font-awesome-icon icon="caret-down" class="ml-1 transition-transform duration-200" :class="toggleDropdown ? 'rotate-180' : ''" />
                            
                            <!-- Notification Badge -->
                            <span v-if="newResponsesCount > 0"
                                class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full ring-2 ring-white animate-pulse">
                                {{ newResponsesCount > 9 ? '9+' : newResponsesCount }}
                            </span>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <ul v-show="toggleDropdown"
                            class="absolute right-0 mt-3 w-64 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden">
                            <li>
                                <Link class="block px-4 py-3 text-slate-700 hover:bg-green-50 hover:text-green-700 focus:bg-green-100 transition-all duration-200"
                                    :href="route('home')">
                                    <font-awesome-icon icon="house" class="mr-3 text-green-600" />
                                    Home
                                </Link>
                            </li>
                            <li>
                                <Link @click="closeDropdown"
                                    class="block px-4 py-3 text-slate-700 hover:bg-green-50 hover:text-green-700 focus:bg-green-100 transition-all duration-200"
                                    :href="route('user.profile.index')">
                                    <font-awesome-icon icon="user" class="mr-3 text-green-600" />
                                    Profile
                                </Link>
                            </li>
                            <li><hr class="border-slate-200" /></li>
                            <li>
                                <Link @click="closeDropdown"
                                    class="block px-4 py-3 text-slate-700 hover:bg-green-50 hover:text-green-700 focus:bg-green-100 transition-all duration-200"
                                    :href="route('books.saved')">
                                    <font-awesome-icon icon="book-open" class="mr-3 text-green-600" />
                                    My Library
                                </Link>
                            </li>
                            <li><hr class="border-slate-200" /></li>
                            <li>
                                <Link @click="closeDropdown"
                                    class="block px-4 py-3 text-slate-700 hover:bg-green-50 hover:text-green-700 focus:bg-green-100 transition-all duration-200"
                                    :href="route('feedback.create')">
                                    <font-awesome-icon icon="comment-dots" class="mr-3 text-green-600" />
                                    Feedback
                                </Link>
                            </li>
                           
                            <li><hr class="border-slate-200" /></li>
                            <li>
                                <Link @click="closeDropdown"
                                    class="block px-4 py-3 text-red-600 hover:bg-red-50 focus:bg-red-100 transition-all duration-200"
                                    :href="route('logout')" method="post">
                                    <font-awesome-icon icon="right-from-bracket" class="mr-3" />
                                    Logout
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Enhanced Mobile Sidebar -->
        <transition name="sidebar-slide">
            <aside v-if="toggle"
                class="fixed top-0 right-0 h-full w-80 bg-white/95 backdrop-blur-md shadow-2xl rounded-l-3xl z-50 flex flex-col md:hidden">
                <div class="flex items-center justify-between px-6 h-20 border-b border-slate-200 bg-gradient-to-r from-green-50 to-emerald-50">
                    <span class="font-bold text-green-700 text-xl flex items-center gap-3">
                        <font-awesome-icon icon="book" class="text-2xl" />
                        eBaryo
                    </span>
                    <button @click="toggle = false"
                        class="text-green-700 hover:text-green-900 text-3xl focus:outline-none hover:scale-110 transition-transform duration-200">
                        &times;
                    </button>
                </div>
                
                <div class="flex flex-col gap-2 p-6 flex-1">
                    <!-- Mobile Search -->
                    <form class="flex max-w-md w-full mb-4" @submit.prevent="submitSearch">
                        <div class="relative w-full">
                            <input v-model="searchQuery" type="search" placeholder="Search books..."
                                class="w-full pl-10 pr-4 py-3 border border-green-500 rounded-2xl focus:outline-none focus:ring-2 focus:ring-green-300 bg-white text-slate-700" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <font-awesome-icon icon="search" class="text-slate-400" />
                            </div>
                        </div>
                        <button type="submit"
                            class="ml-2 bg-green-700 text-white px-4 py-3 rounded-2xl hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-400 transition-all duration-200">
                            <font-awesome-icon icon="search" />
                        </button>
                    </form>

                    <!-- Mobile Browse Dropdown -->
                    <div class="relative mb-4">
                        <button @click="browseDropdown = !browseDropdown"
                            class="w-full flex items-center gap-3 text-green-700 px-4 py-3 rounded-2xl hover:bg-green-100 focus:bg-green-200 bg-green-50 transition-all duration-200"
                            type="button">
                            <font-awesome-icon icon="journal-whills" class="mr-2" />
                            Browse Books
                            <font-awesome-icon icon="caret-down" class="ml-auto transition-transform duration-200" :class="browseDropdown ? 'rotate-180' : ''" />
                        </button>
                        <ul v-show="browseDropdown"
                            class="absolute left-0 mt-2 w-full bg-white rounded-2xl shadow-lg border border-slate-200 z-50 overflow-hidden"
                            @mouseleave="closeBrowseDropdown">
                            <li v-for="category in categories" :key="category.id">
                                <Link class="block px-4 py-3 text-slate-700 hover:bg-green-100 focus:bg-green-200 transition-all duration-200"
                                    :href="route('books.index', { category: category.id })" @click="closeBrowseDropdown; toggle = false;">
                                    {{ category.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Mobile Navigation Links -->
                    <Link @mousedown="toggle = false"
                        class="text-green-700 px-4 py-3 rounded-2xl hover:bg-green-100 focus:bg-green-200 flex items-center gap-3 transition-all duration-200"
                        :href="route('home')">
                        <font-awesome-icon icon="house" class="mr-2" />
                        Home
                    </Link>
                    
                    <Link @mousedown="toggle = false"
                        class="text-green-700 px-4 py-3 rounded-2xl hover:bg-green-100 focus:bg-green-200 flex items-center gap-3 transition-all duration-200"
                        :href="route('user.profile.index')">
                        <font-awesome-icon icon="user" class="mr-2" />
                        Profile
                    </Link>
                    
                    <Link @mousedown="toggle = false"
                        class="text-green-700 px-4 py-3 rounded-2xl hover:bg-green-100 focus:bg-green-200 flex items-center gap-3 transition-all duration-200"
                        :href="route('books.saved')">
                        <font-awesome-icon icon="book-open" class="mr-2" />
                        My Library
                    </Link>

                    <Link @mousedown="toggle = false"
                        class="text-green-700 px-4 py-3 rounded-2xl hover:bg-green-100 focus:bg-green-200 flex items-center gap-3 transition-all duration-200"
                        :href="route('feedback.create')">
                        <font-awesome-icon icon="comment-dots" class="mr-2" />
                        Feedback
                    </Link>
                    
                    
                    
                    <Link @mousedown="toggle = false"
                        class="text-red-700 px-4 py-3 rounded-2xl hover:bg-red-100 focus:bg-red-200 flex items-center gap-3 transition-all duration-200 mt-auto"
                        :href="route('logout')" method="post">
                        <font-awesome-icon icon="right-from-bracket" class="mr-2" />
                        Logout
                    </Link>
                </div>
            </aside>
        </transition>

        <!-- Main Content Area -->
        <div class="h-16 md:h-16"></div>
        <main class="flex-grow px-6 py-8">
            <slot />
        </main>

        <!-- Enhanced Footer -->
        <footer class="bg-gradient-to-r from-green-600 to-green-700 text-white text-center py-6 mt-auto shadow-lg border-t border-green-800">
            <div class="max-w-6xl mx-auto">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <small class="flex items-center gap-2 text-green-100">
                        <font-awesome-icon icon="copyright" />
                        {{ new Date().getFullYear() }} eBaryo Library. All rights reserved.
                    </small>
                    <div class="flex gap-6">
                        <a href="#" class="text-green-200 hover:text-white transition-colors duration-200"
                            aria-label="Facebook">
                            <font-awesome-icon :icon="['fab', 'facebook']" class="text-xl" />
                        </a>
                        <a href="#" class="text-green-200 hover:text-white transition-colors duration-200"
                            aria-label="Twitter">
                            <font-awesome-icon :icon="['fab', 'twitter']" class="text-xl" />
                        </a>
                        <a href="#" class="text-green-200 hover:text-white transition-colors duration-200"
                            aria-label="Instagram">
                            <font-awesome-icon :icon="['fab', 'instagram']" class="text-xl" />
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.sidebar-slide-enter-active,
.sidebar-slide-leave-active {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
