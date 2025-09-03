<script setup>
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faBook,
    faHouse,
    faInfoCircle,
    faStar,
    faCommentDots,
    faRightToBracket,
    faCopyright,
    faUserCircle,
    faXmark,
} from "@fortawesome/free-solid-svg-icons";
import {
    faFacebook,
    faTwitter,
    faInstagram,
} from "@fortawesome/free-brands-svg-icons";

const toggle = ref(false);

watch(toggle, (val) => {
    if (val) {
        document.body.classList.add("overflow-hidden");
    } else {
        document.body.classList.remove("overflow-hidden");
    }
});

const smoothScroll = (targetId) => {
    const isWelcome =
        window.location.pathname === "/" ||
        window.location.pathname === "/welcome";
    if (isWelcome) {
        const target = document.querySelector(targetId);
        if (target) {
            const navbarHeight = 93;
            const elementPosition =
                target.getBoundingClientRect().top + window.pageYOffset;
            const offsetPosition = elementPosition - navbarHeight;
            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth",
            });
        }
    } else {
        router.visit(`/${targetId}`);
    }
};

library.add(
    faBook,
    faHouse,
    faInfoCircle,
    faStar,
    faCommentDots,
    faRightToBracket,
    faCopyright,
    faUserCircle,
    faXmark,
    faFacebook,
    faTwitter,
    faInstagram
);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-slate-50 via-white to-green-50">
        <nav
            class="bg-white/80 backdrop-blur-md shadow-sm border-b border-green-100 fixed w-full z-40 h-16 flex items-center">
            <div class="container mx-auto px-6 flex flex-wrap items-center justify-between h-16">
                <Link
                    class="text-green-700 font-semibold flex items-center gap-3 text-xl transition-all hover:scale-105 focus:scale-105 duration-200"
                    :href="route('welcome')">
                <img src="/favicon1.png" alt="" class="w-8 h-8 md:w-9 md:h-9" />
                <span class="hidden sm:block">eBaryo Library</span>
                </Link>
                <button class="text-green-700 focus:outline-none md:hidden relative z-50 group" @click="toggle = !toggle"
                    :aria-expanded="toggle.toString()" aria-label="Toggle navigation menu">
                    <span :class="[
                        'block w-6 h-0.5 bg-green-700 transition-transform duration-300',
                        toggle ? 'rotate-45 translate-y-2' : '',
                    ]"></span>
                    <span :class="[
                        'block w-6 h-0.5 bg-green-700 my-1 transition-opacity duration-300',
                        toggle ? 'opacity-0' : '',
                    ]"></span>
                    <span :class="[
                        'block w-6 h-0.5 bg-green-700 transition-transform duration-300',
                        toggle ? '-rotate-45 -translate-y-2' : '',
                    ]"></span>
                </button>

                <ul class="hidden md:flex gap-1 items-center">
                    <li>
                        <button class="text-slate-600 px-4 py-2 rounded-lg hover:bg-green-50 hover:text-green-700 transition-all duration-200 text-sm font-medium"
                            @click="smoothScroll('#home')">
                            Home
                        </button>
                    </li>
                    <li>
                        <button @click="smoothScroll('#about')"
                            class="text-slate-600 px-4 py-2 rounded-lg hover:bg-green-50 hover:text-green-700 transition-all duration-200 text-sm font-medium">
                            About
                        </button>
                    </li>
                    <li>
                        <button @click="smoothScroll('#features')"
                            class="text-slate-600 px-4 py-2 rounded-lg hover:bg-green-50 hover:text-green-700 transition-all duration-200 text-sm font-medium">
                            Features
                        </button>
                    </li>
                    <li>
                        <button @click="smoothScroll('#testimonials')"
                            class="text-slate-600 px-4 py-2 rounded-lg hover:bg-green-50 hover:text-green-700 transition-all duration-200 text-sm font-medium">
                            Testimonials
                        </button>
                    </li>
                    <li class="ml-4">
                        <Link class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-all duration-200 text-sm font-medium shadow-sm"
                            :href="route('login')">
                        Login
                        </Link>
                    </li>
                </ul>
            </div>
        </nav>

        <transition name="fade">
            <div v-if="toggle"
                class="fixed inset-0 z-50 bg-black bg-opacity-40 backdrop-blur-sm transition-all duration-300 md:hidden"
                @click="toggle = false"></div>
        </transition>
        <transition name="slide">
            <aside v-if="toggle"
                class="fixed top-0 right-0 h-full w-80 bg-white/95 backdrop-blur-md shadow-xl z-60 flex flex-col md:hidden transition-transform duration-300"
                style="z-index: 60">
                <div
                    class="flex flex-col items-center gap-3 px-6 h-24 border-b border-slate-200 justify-center bg-white">
                    <span class="font-semibold text-slate-700 text-lg">Navigation</span>
                    <button @click="toggle = false"
                        class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-xl focus:outline-none transition-colors"
                        aria-label="Close sidebar">
                        <font-awesome-icon icon="xmark" />
                    </button>
                </div>
                <ul class="flex flex-col gap-1 p-6">
                    <li>
                        <button @click="
                            () => {
                                smoothScroll('#home');
                                toggle = false;
                            }
                        "
                            class="text-slate-600 px-4 py-3 rounded-lg hover:bg-green-50 hover:text-green-700 transition-all duration-200 w-full text-left font-medium">
                            Home
                        </button>
                    </li>
                    <li>
                        <button @click="
                            () => {
                                smoothScroll('#about');
                                toggle = false;
                            }
                        "
                            class="text-slate-600 px-4 py-3 rounded-lg hover:bg-green-50 hover:text-green-700 transition-all duration-200 w-full text-left font-medium">
                            About
                        </button>
                    </li>
                    <li>
                        <button @click="
                            () => {
                                smoothScroll('#features');
                                toggle = false;
                            }
                        "
                            class="text-slate-600 px-4 py-3 rounded-lg hover:bg-green-50 hover:text-green-700 transition-all duration-200 w-full text-left font-medium">
                            Features
                        </button>
                    </li>
                    <li>
                        <button @click="
                            () => {
                                smoothScroll('#testimonials');
                                toggle = false;
                            }
                        "
                            class="text-slate-600 px-4 py-3 rounded-lg hover:bg-green-50 hover:text-green-700 transition-all duration-200 w-full text-left font-medium">
                            Testimonials
                        </button>
                    </li>
                    <li class="mt-4">
                        <Link @click="toggle = false"
                            class="bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-all duration-200 w-full text-center font-medium block"
                            :href="route('login')">
                        Login
                        </Link>
                    </li>
                </ul>
            </aside>
        </transition>

        <div class="h-16"></div>

        <main class="flex-grow">
            <slot />
        </main>

        <footer class="bg-white border-t border-slate-200 text-center py-8 mt-auto">
            <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4 px-6">
                <small class="flex items-center gap-2 text-slate-500 text-sm font-medium">
                    <font-awesome-icon icon="copyright" />
                    {{ new Date().getFullYear() }} eBaryo Library. All rights reserved.
                </small>
                <div class="flex gap-6">
                    <a href="#" class="text-slate-400 hover:text-green-600 transition-colors duration-200"
                        aria-label="Facebook"><font-awesome-icon :icon="['fab', 'facebook']" /></a>
                    <a href="#" class="text-slate-400 hover:text-green-600 transition-colors duration-200"
                        aria-label="Twitter"><font-awesome-icon :icon="['fab', 'twitter']" /></a>
                    <a href="#" class="text-slate-400 hover:text-green-600 transition-colors duration-200"
                        aria-label="Instagram"><font-awesome-icon :icon="['fab', 'instagram']" /></a>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
html,
body {
    height: 100%;
    font-family: "Inter", sans-serif;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(100%);
}

button:focus,
a:focus {
    outline: 2px solid #34d399;
    outline-offset: 2px;
}
</style>
