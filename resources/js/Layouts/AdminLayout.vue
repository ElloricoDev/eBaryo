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
} from "@fortawesome/free-solid-svg-icons";

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

library.add(
    faTachometerAlt,
    faUsers,
    faBook,
    faTags,
    faComments,
    faUser,
    faCog,
    faSignOutAlt,
    faSearch
);
</script>

<template>
    <div class="flex">
        <nav
            class="bg-green-600 text-white w-[250px] h-screen p-5 fixed top-0 left-0 z-50 shadow-lg"
        >
            <h5
                class="text-white text-xl font-semibold mb-6 flex items-center gap-2"
            >
                <font-awesome-icon icon="tachometer-alt" class="w-5 h-5" />
                Admin Panel
            </h5>
            <ul class="space-y-2">
                <li>
                    <Link
                        class="block px-4 py-2 rounded transition-all duration-200 flex items-center gap-2"
                        :class="{
                            'bg-green-700 font-semibold':
                                isActive('/dashboard'),
                        }"
                        :href="route('dashboard')"
                    >
                        <font-awesome-icon icon="tachometer-alt" /> Dashboard
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-4 py-2 rounded transition-all duration-200 flex items-center gap-2"
                        :class="{
                            'bg-green-700 font-semibold':
                                isActive('/admin/users'),
                        }"
                        :href="route('admin.users.index')"
                    >
                        <font-awesome-icon icon="users" /> Users
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-4 py-2 rounded transition-all duration-200 flex items-center gap-2"
                        :class="{
                            'bg-green-700 font-semibold':
                                isActive('/admin/books'),
                        }"
                        :href="route('admin.books.index')"
                    >
                        <font-awesome-icon icon="book" /> Books
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-4 py-2 rounded transition-all duration-200 flex items-center gap-2"
                        :class="{
                            'bg-green-700 font-semibold':
                                isActive('/admin/category'),
                        }"
                        :href="route('admin.categories.index')"
                    >
                        <font-awesome-icon icon="tags" /> Category
                    </Link>
                </li>
                <li>
                    <Link
                        class="block px-4 py-2 rounded transition-all duration-200 flex items-center gap-2 relative"
                        :class="{
                            'bg-green-700 font-semibold':
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
                        class="block px-4 py-2 rounded transition-all duration-200 flex items-center gap-2"
                        :class="{
                            'bg-green-700 font-semibold':
                                isActive('/testimonials'),
                        }"
                        :href="route('admin.testimonials.index')"
                    >
                        <font-awesome-icon icon="comments" /> Testimonials
                    </Link>
                </li>
            </ul>
        </nav>

        <div class="ml-[250px] w-full min-h-screen flex flex-col">
            <header
                class="bg-white flex justify-between items-center px-6 h-[60px] shadow border-b sticky top-0 z-40"
            >
                <div class="w-1/2 flex">
                    <input
                        type="text"
                        placeholder="Search..."
                        class="w-full border border-green-500 rounded-l-md px-3 py-1 focus:outline-none focus:ring focus:ring-green-300"
                    />
                    <button
                        class="bg-green-600 text-white px-4 rounded-r-md hover:bg-green-700 transition"
                    >
                        <font-awesome-icon icon="search" />
                    </button>
                </div>

                <div class="relative" ref="dropdownRef">
                    <button
                        @click="toggleDropdown"
                        class="flex items-center bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition relative"
                    >
                        <img
                            v-if="user.avatar"
                            :src="user.avatar"
                            class="w-8 h-8 rounded-full object-cover mr-2"
                            alt="Avatar"
                        />
                        <img
                            v-else
                            src="https://ui-avatars.com/api/?name=N%2FA&background=ddd&color=555"
                            class="w-8 h-8 rounded-full object-cover mr-2"
                            alt="Avatar"
                        />
                        <span>{{ user.user_name || "Admin" }}</span>
                        <svg
                            class="ml-2 w-4 h-4 fill-current"
                            viewBox="0 0 20 20"
                        >
                            <path
                                d="M5.516 7.548a.75.75 0 011.06 0L10 10.971l3.424-3.423a.75.75 0 111.06 1.06l-4 4a.75.75 0 01-1.06 0l-4-4a.75.75 0 010-1.06z"
                            />
                        </svg>
                    </button>

                    <div
                        v-if="showDropdown"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border z-50"
                    >
                        <Link
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center gap-2"
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
                            class="block px-4 py-2 text-red-600 hover:bg-red-100 flex items-center gap-2"
                            :href="route('logout')"
                            method="post"
                        >
                            <font-awesome-icon icon="sign-out-alt" /> Logout
                        </Link>
                    </div>
                </div>
            </header>

            <main class="p-6 flex-grow">
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
export default {
    components: {
        FontAwesomeIcon,
    },
};
</script>
