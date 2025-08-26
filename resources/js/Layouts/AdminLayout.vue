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
const showNotificationDropdown = ref(false);
const searchQuery = ref('');
const isSearching = ref(false);

const notificationDropdownRef = ref(null);

const handleClickOutside = (event) => {
    if (notificationDropdownRef.value && !notificationDropdownRef.value.contains(event.target)) {
        showNotificationDropdown.value = false;
    }
};

const performSearch = () => {
    if (!searchQuery.value.trim()) {
        // If search is empty, go to dashboard
        router.visit(route('dashboard'));
        return;
    }
    
    isSearching.value = true;
    
    // Search across all entities
    router.visit(route('admin.search'), {
        data: { query: searchQuery.value.trim() },
        preserveState: false,
        onFinish: () => {
            isSearching.value = false;
        }
    });
};

const handleSearchKeydown = (event) => {
    if (event.key === 'Enter') {
        performSearch();
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

const toggleNotificationDropdown = async () => {
    showNotificationDropdown.value = !showNotificationDropdown.value;
    
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
                <img src="/favicon1.png" alt="Logo" class="w-10 h-10 rounded-full shadow" />
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
                        class="block px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-600/80"
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
                        class="block px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-600/80"
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
                        class="block px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-600/80"
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
                        class="block px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-600/80"
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
                        class="block px-5 py-2 rounded-lg flex items-center gap-2 relative hover:bg-green-600/80"
                        :class="{
                            'bg-green-800/80 font-semibold shadow':
                                isActive('/admin/feedback'),
                        }"
                        :href="route('admin.feedback.index')"
                    >
                        <font-awesome-icon icon="comments" /> Feedback
                        <span
                            v-if="pendingFeedbackCount > 0"
                            class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full"
                        >
                            {{ pendingFeedbackCount }}
                        </span>
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-600/80"
                        :class="{
                            'bg-green-800/80 font-semibold shadow':
                                isActive('/testimonials'),
                        }"
                        :href="route('admin.testimonials.index')"
                    >
                        <font-awesome-icon icon="comments" /> Testimonials
                    </Link>
                </li>
                <li class="mt-4 border-t border-green-400/50 pt-2">
                    <Link
                        class="block px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-600/80"
                        :href="route('admin.profile.index')"
                    >
                        <font-awesome-icon icon="user" /> Profile
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-600/80"
                        :href="route('admin.settings.index')"
                    >
                        <font-awesome-icon icon="cog" /> Settings
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-600/80 text-red-200 hover:text-white"
                        :href="route('logout')"
                        method="post"
                    >
                        <font-awesome-icon icon="sign-out-alt" /> Logout
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
                            class="w-full border border-green-400 rounded-full pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-300 shadow-sm"
                            v-model="searchQuery"
                            @keydown="handleSearchKeydown"
                        />
                        <button 
                            @click="performSearch"
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-green-500 hover:text-green-700 cursor-pointer"
                            :disabled="isSearching"
                        >
                            <font-awesome-icon icon="search" />
                        </button>
                        <button
                            v-if="searchQuery.trim()"
                            @click="searchQuery = ''"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer"
                        >
                            <font-awesome-icon icon="times" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="relative" ref="notificationDropdownRef">
                        <button 
                            @click="toggleNotificationDropdown"
                            class="relative focus:outline-none group p-2 rounded-full hover:bg-gray-100"
                        >
                            <font-awesome-icon icon="bell" class="text-green-600 w-6 h-6" />
                            <span v-if="pendingFeedbackCount > 0" class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 shadow">
                                {{ pendingFeedbackCount }}
                            </span>
                            <span class="sr-only">Notifications</span>
                        </button>
                        
                        <!-- Notification Dropdown -->
                        <div
                            v-if="showNotificationDropdown"
                            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-50"
                        >
                                <div class="p-4 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
                                        <button 
                                            @click="showNotificationDropdown = false"
                                            class="text-gray-400 hover:text-gray-600"
                                        >
                                            <font-awesome-icon icon="times" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="max-h-64 overflow-y-auto">
                                    <div v-if="pendingFeedbackCount > 0" class="p-4">
                                        <div class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 cursor-pointer"
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
                                        class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 text-sm font-medium"
                                    >
                                        View All Feedback
                                    </button>
                                </div>
                        </div>
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
</style>

<script>
export default {
    components: {
        FontAwesomeIcon,
    },
};
</script>
