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
            class="bg-white border-r border-slate-200 w-[280px] h-screen p-0 fixed top-0 left-0 z-50 shadow-xl flex flex-col">
            <div class="flex items-center gap-3 px-8 py-8 border-b border-slate-100">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <img src="/favicon1.png" alt="Logo" class="w-8 h-8 rounded-lg" />
                </div>
                <div>
                    <span class="text-xl font-bold text-slate-800">eBaryo</span>
                    <p class="text-xs text-slate-500 font-medium">Admin Portal</p>
                </div>
            </div>
            <div class="px-6 py-4">
                <h5 class="text-slate-600 text-sm font-semibold uppercase tracking-wider mb-4 flex items-center gap-2">
                    <font-awesome-icon icon="tachometer-alt" class="w-4 h-4" />
                    Dashboard
                </h5>
            <ul class="space-y-2 flex-1">
                <li>
                    <Link class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-slate-50" :class="{
                        'bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 shadow-sm':
                            isActive('/dashboard'),
                        'text-slate-600 hover:text-slate-800': !isActive('/dashboard')
                    }" :href="route('dashboard')">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors" :class="{
                            'bg-green-100 text-green-600': isActive('/dashboard'),
                            'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-600': !isActive('/dashboard')
                        }">
                            <font-awesome-icon icon="tachometer-alt" class="w-4 h-4" />
                        </div>
                        <span class="font-medium">Dashboard</span>
                    </Link>
                </li>
                <li>
                    <Link class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-slate-50" :class="{
                        'bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 shadow-sm':
                            isActive('/admin/users'),
                        'text-slate-600 hover:text-slate-800': !isActive('/admin/users')
                    }" :href="route('admin.users.index')">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors" :class="{
                            'bg-green-100 text-green-600': isActive('/admin/users'),
                            'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-600': !isActive('/admin/users')
                        }">
                            <font-awesome-icon icon="users" class="w-4 h-4" />
                        </div>
                        <span class="font-medium">Users</span>
                    </Link>
                </li>
                <li>
                    <Link class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-slate-50" :class="{
                        'bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 shadow-sm':
                            isActive('/admin/books'),
                        'text-slate-600 hover:text-slate-800': !isActive('/admin/books')
                    }" :href="route('admin.books.index')">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors" :class="{
                            'bg-green-100 text-green-600': isActive('/admin/books'),
                            'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-600': !isActive('/admin/books')
                        }">
                            <font-awesome-icon icon="book" class="w-4 h-4" />
                        </div>
                        <span class="font-medium">Books</span>
                    </Link>
                </li>
                <li>
                    <Link class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-slate-50" :class="{
                        'bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 shadow-sm':
                            isActive('/admin/category'),
                        'text-slate-600 hover:text-slate-800': !isActive('/admin/category')
                    }" :href="route('admin.categories.index')">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors" :class="{
                            'bg-green-100 text-green-600': isActive('/admin/category'),
                            'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-600': !isActive('/admin/category')
                        }">
                            <font-awesome-icon icon="tags" class="w-4 h-4" />
                        </div>
                        <span class="font-medium">Categories</span>
                    </Link>
                </li>
                <li>
                    <Link class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-slate-50 relative" :class="{
                        'bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 shadow-sm':
                            isActive('/admin/feedback'),
                        'text-slate-600 hover:text-slate-800': !isActive('/admin/feedback')
                    }" :href="route('admin.feedback.index')">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors" :class="{
                            'bg-green-100 text-green-600': isActive('/admin/feedback'),
                            'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-600': !isActive('/admin/feedback')
                        }">
                            <font-awesome-icon icon="comments" class="w-4 h-4" />
                        </div>
                        <span class="font-medium">Feedback</span>
                        <span v-if="pendingFeedbackCount > 0"
                            class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full shadow-sm">
                            {{ pendingFeedbackCount }}
                        </span>
                    </Link>
                </li>

                <li class="mt-8 pt-4 border-t border-slate-100">
                    <h6 class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-3 px-4">Account</h6>
                    <Link class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-slate-50 text-slate-600 hover:text-slate-800"
                        :href="route('admin.profile.index')">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-600 flex items-center justify-center transition-colors">
                            <font-awesome-icon icon="user" class="w-4 h-4" />
                        </div>
                        <span class="font-medium">Profile</span>
                    </Link>
                </li>
                <li>
                    <Link class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-slate-50 text-slate-600 hover:text-slate-800"
                        :href="route('admin.settings.index')">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-600 flex items-center justify-center transition-colors">
                            <font-awesome-icon icon="cog" class="w-4 h-4" />
                        </div>
                        <span class="font-medium">Settings</span>
                    </Link>
                </li>
                <li>
                    <Link class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-red-50 text-red-600 hover:text-red-700"
                        :href="route('logout')" method="post">
                        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-500 group-hover:bg-red-200 group-hover:text-red-600 flex items-center justify-center transition-colors">
                            <font-awesome-icon icon="sign-out-alt" class="w-4 h-4" />
                        </div>
                        <span class="font-medium">Logout</span>
                    </Link>
                </li>
            </ul>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 text-xs text-slate-400">
                &copy; {{ new Date().getFullYear() }} eBaryo Admin
            </div>
        </nav>

        <div class="ml-[280px] w-full min-h-screen flex flex-col bg-slate-50">
            <header
                class="bg-white/80 backdrop-blur-sm flex justify-between items-center px-8 h-[80px] shadow-sm border-b border-slate-200 sticky top-0 z-40">
                <div class="w-1/2 flex items-center gap-2">
                    <div class="relative w-full max-w-md">
                        <input type="text" placeholder="Search users, books, categories..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-12 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all duration-200"
                            v-model="searchQuery" @keydown="handleSearchKeydown" />
                        <button @click="performSearch"
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer transition-colors"
                            :disabled="isSearching">
                            <font-awesome-icon icon="search" class="w-4 h-4" />
                        </button>
                        <button v-if="searchQuery.trim()" @click="searchQuery = ''"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer transition-colors">
                            <font-awesome-icon icon="times" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative" ref="notificationDropdownRef">
                        <button @click="toggleNotificationDropdown"
                            class="relative focus:outline-none group p-3 rounded-2xl hover:bg-slate-100 transition-colors">
                            <font-awesome-icon icon="bell" class="text-slate-600 w-5 h-5 group-hover:text-slate-800" />
                            <span v-if="pendingFeedbackCount > 0"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-2 py-1 shadow-lg font-semibold">
                                {{ pendingFeedbackCount }}
                            </span>
                            <span class="sr-only">Notifications</span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div v-if="showNotificationDropdown"
                            class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-slate-200 z-50 overflow-hidden">
                            <div class="p-6 border-b border-slate-100">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-slate-800">Notifications</h3>
                                    <button @click="showNotificationDropdown = false"
                                        class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-100">
                                        <font-awesome-icon icon="times" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>

                            <div class="max-h-64 overflow-y-auto">
                                <div v-if="pendingFeedbackCount > 0" class="p-6">
                                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-2xl hover:from-red-100 hover:to-pink-100 cursor-pointer transition-all duration-200"
                                        @click="handleNotificationClick">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                                <font-awesome-icon icon="envelope" class="text-red-600 w-5 h-5" />
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-slate-800">
                                                New Feedback Pending
                                            </p>
                                            <p class="text-xs text-slate-600 mt-1">
                                                You have {{ pendingFeedbackCount }} pending feedback{{
                                                pendingFeedbackCount > 1 ? 's' : '' }} to review
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span
                                                class="inline-flex items-center justify-center px-3 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full shadow-sm">
                                                {{ pendingFeedbackCount }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div v-else class="p-6 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <font-awesome-icon icon="bell" class="text-slate-400 w-6 h-6" />
                                    </div>
                                    <p class="text-slate-500 text-sm font-medium">No new notifications</p>
                                </div>
                            </div>

                            <div v-if="pendingFeedbackCount > 0" class="p-6 border-t border-slate-100 bg-slate-50">
                                <button @click="handleNotificationClick"
                                    class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 px-4 rounded-2xl hover:from-green-700 hover:to-emerald-700 text-sm font-semibold transition-all duration-200 shadow-sm">
                                    View All Feedback
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <main class="p-8 flex-grow min-h-[calc(100vh-80px)]">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped></style>

<script>
export default {
    components: {
        FontAwesomeIcon,
    },
};
</script>
