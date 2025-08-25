<script setup>
import { usePage, Link, router } from "@inertiajs/vue3";
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
    faTimes,
    faEnvelope,
} from "@fortawesome/free-solid-svg-icons";

const { props, url } = usePage();
const user = props.auth?.user || {};
const pendingFeedbackCount = props.pendingFeedbackCount || 0;
const showDropdown = ref(false);
const showNotificationDropdown = ref(false);

const dropdownRef = ref(null);
const notificationDropdownRef = ref(null);

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        showDropdown.value = false;
    }
    if (notificationDropdownRef.value && !notificationDropdownRef.value.contains(event.target)) {
        showNotificationDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
    
    // Set up real-time notifications for admin
    if (window.Echo) {
        window.Echo.private('admin-notifications').listen(
            'FeedbackUpdated',
            (e) => {
                if (e.type === 'admin') {
                    // Reload the page to update notification count
                    window.Inertia.reload({ only: ['pendingFeedbackCount'] });
                }
            }
        );
    }
});
onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
    showNotificationDropdown.value = false;
};

const toggleNotificationDropdown = async () => {
    showNotificationDropdown.value = !showNotificationDropdown.value;
    showDropdown.value = false;
    
    // If opening the dropdown and there are pending notifications, mark them as read
    if (showNotificationDropdown.value && pendingFeedbackCount > 0) {
        try {
            await fetch(route('admin.feedback.mark-as-read'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            });
        } catch (error) {
            console.error('Error marking notifications as read:', error);
        }
    }
};

const handleNotificationClick = async () => {
    showNotificationDropdown.value = false;
    
    // Mark notifications as read
    try {
        await fetch(route('admin.feedback.mark-as-read'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        });
    } catch (error) {
        console.error('Error marking notifications as read:', error);
    }
    
    router.visit(route('admin.feedback.index'));
};

const isActive = (path) => {
    const currentPath = window.location.pathname.replace(/\/$/, "");
    const normalizedPath = path.replace(/\/$/, "");
    return (
        currentPath === normalizedPath ||
        currentPath.startsWith(normalizedPath + "/")
    );
};

const pendingNotifications = computed(() => pendingFeedbackCount);

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
    faBell,
    faTimes,
    faEnvelope
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
                    <div class="relative" ref="notificationDropdownRef">
                        <button 
                            @click="toggleNotificationDropdown"
                            class="relative focus:outline-none group p-2 rounded-full hover:bg-gray-100 transition-colors"
                        >
                            <font-awesome-icon icon="bell" class="text-green-600 w-6 h-6" />
                            <span v-if="pendingFeedbackCount > 0" class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 animate-bounce shadow">
                                {{ pendingFeedbackCount }}
                            </span>
                            <span class="sr-only">Notifications</span>
                        </button>
                        
                        <!-- Notification Dropdown -->
                        <transition name="fade-slide">
                            <div
                                v-if="showNotificationDropdown"
                                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-50 animate-fade-in"
                            >
                                <div class="p-4 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
                                        <button 
                                            @click="showNotificationDropdown = false"
                                            class="text-gray-400 hover:text-gray-600 transition-colors"
                                        >
                                            <font-awesome-icon icon="times" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="max-h-64 overflow-y-auto">
                                    <div v-if="pendingFeedbackCount > 0" class="p-4">
                                        <div class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors cursor-pointer"
                                             @click="handleNotificationClick">
                                            <div class="flex-shrink-0">
                                                <font-awesome-icon icon="envelope" class="text-red-600 w-5 h-5" />
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    New Feedback Pending
                                                </p>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    You have {{ pendingFeedbackCount }} pending feedback{{ pendingFeedbackCount > 1 ? 's' : '' }} to review
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                                    {{ pendingFeedbackCount }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div v-else class="p-4 text-center">
                                        <font-awesome-icon icon="bell" class="text-gray-400 w-8 h-8 mx-auto mb-2" />
                                        <p class="text-gray-500 text-sm">No new notifications</p>
                                    </div>
                                </div>
                                
                                <div v-if="pendingFeedbackCount > 0" class="p-3 border-t border-gray-200">
                                    <button
                                        @click="handleNotificationClick"
                                        class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-sm font-medium"
                                    >
                                        View All Feedback
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>
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

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(-25%);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: none;
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}

.animate-bounce {
    animation: bounce 1s infinite;
}
</style>

<script>
export default {
    components: {
        FontAwesomeIcon,
    },
};
</script>
