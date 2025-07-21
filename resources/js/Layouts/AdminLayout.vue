<script setup>
import { usePage, Link } from "@inertiajs/vue3";
import { computed, ref, onMounted, onUnmounted } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faTachometerAlt,
    faUsers,
    faBook,
    faTags,
    faComments,
    faUser,
    faCog,
    faSignOutAlt,
    faSearch,
    faBell,
} from "@fortawesome/free-solid-svg-icons";
// import logo from "@/../public/images/default.svg";

const { props, url } = usePage();
const user = props.auth?.user || {};
const pendingFeedbackCount = props.pendingFeedbackCount || 0;
const showDropdown = ref(false);

const dropdownRef = ref(null);

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        showDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});
onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

const isActive = (path) => {
    const currentPath = window.location.pathname.replace(/\/$/, "");
    const normalizedPath = path.replace(/\/$/, "");
    return (
        currentPath === normalizedPath ||
        currentPath.startsWith(normalizedPath + "/")
    );
};

const pendingNotifications = computed(() => pendingFeedbackCount); // Example: use feedback count as notifications

library.add(
    faTachometerAlt,
    faUsers,
    faBook,
    faTags,
    faComments,
    faUser,
    faCog,
    faSignOutAlt,
    faSearch,
    faBell
);
</script>

<template>
    <div class="flex">
        <nav
            class="bg-gradient-to-b from-green-700 to-green-500 text-white w-[260px] h-screen p-0 fixed top-0 left-0 z-50 shadow-2xl flex flex-col"
        >
            <div class="flex items-center gap-3 px-6 py-6 border-b border-green-400 mb-2">
                <img src="/images/default.svg" alt="Logo" class="w-10 h-10 rounded-full shadow" />
                <span class="text-2xl font-bold tracking-wide">eBaryo</span>
            </div>
            <h5
                class="text-white text-lg font-semibold mb-4 px-6 flex items-center gap-2 opacity-80"
            >
                <font-awesome-icon icon="tachometer-alt" class="w-5 h-5" />
                Admin Panel
            </h5>
            <ul class="space-y-1 px-2 flex-1">
                <li>
                    <Link
                        class="block px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 hover:bg-green-600/80 hover:scale-[1.03]"
                        :class="{
                            'bg-green-800/80 font-semibold shadow':
                                isActive('/dashboard'),
                        }"
                        :href="route('dashboard')"
                    >
                        <font-awesome-icon icon="tachometer-alt" /> Dashboard
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 hover:bg-green-600/80 hover:scale-[1.03]"
                        :class="{
                            'bg-green-800/80 font-semibold shadow':
                                isActive('/admin/users'),
                        }"
                        :href="route('admin.users.index')"
                    >
                        <font-awesome-icon icon="users" /> Users
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 hover:bg-green-600/80 hover:scale-[1.03]"
                        :class="{
                            'bg-green-800/80 font-semibold shadow':
                                isActive('/admin/books'),
                        }"
                        :href="route('admin.books.index')"
                    >
                        <font-awesome-icon icon="book" /> Books
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 hover:bg-green-600/80 hover:scale-[1.03]"
                        :class="{
                            'bg-green-800/80 font-semibold shadow':
                                isActive('/admin/category'),
                        }"
                        :href="route('admin.categories.index')"
                    >
                        <font-awesome-icon icon="tags" /> Category
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 relative hover:bg-green-600/80 hover:scale-[1.03]"
                        :class="{
                            'bg-green-800/80 font-semibold shadow':
                                isActive('/admin/feedback'),
                        }"
                        :href="route('admin.feedback.index')"
                    >
                        <font-awesome-icon icon="comments" /> Feedback
                        <span
                            v-if="pendingFeedbackCount > 0"
                            class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full animate-pulse"
                        >
                            {{ pendingFeedbackCount }}
                        </span>
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 hover:bg-green-600/80 hover:scale-[1.03]"
                        :class="{
                            'bg-green-800/80 font-semibold shadow':
                                isActive('/testimonials'),
                        }"
                        :href="route('admin.testimonials.index')"
                    >
                        <font-awesome-icon icon="comments" /> Testimonials
                    </Link>
                </li>
            </ul>
            <div class="px-6 py-4 border-t border-green-400 text-xs text-green-100 opacity-70">
                &copy; {{ new Date().getFullYear() }} eBaryo Admin
            </div>
        </nav>

        <div class="ml-[260px] w-full min-h-screen flex flex-col">
            <header
                class="bg-white flex justify-between items-center px-8 h-[70px] shadow-md border-b sticky top-0 z-40"
            >
                <div class="w-1/2 flex items-center gap-2">
                    <div class="relative w-full max-w-md">
                        <input
                            type="text"
                            placeholder="Search..."
                            class="w-full border border-green-400 rounded-full pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-300 shadow-sm transition"
                        />
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-green-500">
                            <font-awesome-icon icon="search" />
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <button class="relative focus:outline-none group">
                        <font-awesome-icon icon="bell" class="text-green-600 w-6 h-6" />
                        <span v-if="pendingFeedbackCount > 0" class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 animate-bounce shadow">
                            {{ pendingFeedbackCount }}
                        </span>
                        <span class="sr-only">Notifications</span>
                    </button>
                    <div class="relative" ref="dropdownRef">
                        <button
                            @click="toggleDropdown"
                            class="flex items-center bg-green-600 text-white px-4 py-2 rounded-full hover:bg-green-700 transition relative shadow"
                        >
                            <img
                                v-if="user.avatar"
                                :src="user.avatar"
                                class="w-9 h-9 rounded-full object-cover mr-2 border-2 border-white shadow"
                                alt="Avatar"
                            />
                            <img
                                v-else
                                src="https://ui-avatars.com/api/?name=N%2FA&background=ddd&color=555"
                                class="w-9 h-9 rounded-full object-cover mr-2 border-2 border-white shadow"
                                alt="Avatar"
                            />
                            <span class="font-semibold">{{ user.user_name || "Admin" }}</span>
                            <svg
                                class="ml-2 w-4 h-4 fill-current transition-transform duration-200"
                                :class="{ 'rotate-180': showDropdown }"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    d="M5.516 7.548a.75.75 0 011.06 0L10 10.971l3.424-3.423a.75.75 0 111.06 1.06l-4 4a.75.75 0 01-1.06 0l-4-4a.75.75 0 010-1.06z"
                                />
                            </svg>
                        </button>
                        <transition name="fade-slide">
                            <div
                                v-if="showDropdown"
                                class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg border z-50 animate-fade-in"
                            >
                                <Link
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center gap-2 rounded-t-lg"
                                    :href="route('admin.profile.index')"
                                >
                                    <font-awesome-icon icon="user" /> Profile
                                </Link>
                                <Link
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center gap-2"
                                    :href="route('admin.settings.index')"
                                >
                                    <font-awesome-icon icon="cog" /> Settings
                                </Link>
                                <hr class="my-1" />
                                <Link
                                    class="block px-4 py-2 text-red-600 hover:bg-red-100 flex items-center gap-2 rounded-b-lg"
                                    :href="route('logout')"
                                    method="post"
                                >
                                    <font-awesome-icon icon="sign-out-alt" /> Logout
                                </Link>
                            </div>
                        </transition>
                    </div>
                </div>
            </header>

            <main class="p-8 flex-grow bg-gray-50 min-h-[calc(100vh-70px)]">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: opacity 0.2s, transform 0.2s;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
.fade-slide-enter-to,
.fade-slide-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>

<script>
export default {
    components: {
        FontAwesomeIcon,
    },
};
</script>
