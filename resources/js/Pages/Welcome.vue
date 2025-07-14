<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Link, Head } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faBook,
    faSignInAlt,
    faUserPlus,
    faInfoCircle,
    faStar,
    faUsers,
    faTabletAlt,
    faIdBadge,
    faQuoteLeft,
    faSmile,
} from "@fortawesome/free-solid-svg-icons";
import { faUserCircle } from "@fortawesome/free-solid-svg-icons";

library.add(
    faBook,
    faSignInAlt,
    faUserPlus,
    faInfoCircle,
    faStar,
    faUsers,
    faTabletAlt,
    faIdBadge,
    faQuoteLeft,
    faSmile,
    faUserCircle
);

import { onMounted, onUnmounted, ref } from "vue";
const floatingBooks = ref([
    { id: 1, style: "top-10 left-10", delay: 0 },
    { id: 2, style: "top-24 right-16", delay: 0.5 },
    { id: 3, style: "bottom-20 left-32", delay: 1 },
]);
let animationFrame;
const animateBooks = () => {
    const t = Date.now() / 1000;
    floatingBooks.value.forEach((book, i) => {
        const el = document.getElementById(`floating-book-${book.id}`);
        if (el) {
            el.style.transform = `translateY(${Math.sin(t + i) * 10}px)`;
        }
    });
    animationFrame = requestAnimationFrame(animateBooks);
};

const showBackToTop = ref(false);
const handleScroll = () => {
    showBackToTop.value = window.scrollY > 100;
};
function scrollToTop() {
    if (typeof window !== "undefined" && window.scrollTo) {
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
}
onMounted(() => {
    animationFrame = requestAnimationFrame(animateBooks);
    window.addEventListener("scroll", handleScroll);
    handleScroll();

    if (window.location.hash) {
        const target = document.querySelector(window.location.hash);
        if (target) {
            setTimeout(() => {
                const navbarHeight = 64;
                const elementPosition =
                    target.getBoundingClientRect().top + window.pageYOffset;
                const offsetPosition = elementPosition - navbarHeight;
                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth",
                });
            }, 100);
        }
    }
});
onUnmounted(() => {
    cancelAnimationFrame(animationFrame);
    window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
    <Head title="Welcome" />
    <GuestLayout>
        <section class="px-4 py-2 relative" id="home">
            <div class="pointer-events-none select-none">
                <font-awesome-icon
                    v-for="book in floatingBooks"
                    :key="book.id"
                    :id="`floating-book-${book.id}`"
                    icon="book"
                    class="text-green-200 text-5xl opacity-60 absolute"
                    :class="book.style"
                    :style="{ transition: 'transform 0.3s', zIndex: 0 }"
                />
            </div>
            <div class="text-center mb-12 animate-fade-in relative">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 text-green-700">
                    <span
                        class="inline-block bg-green-100 px-4 py-3 rounded-xl shadow-sm animate-slide-down"
                    >
                        <font-awesome-icon
                            icon="book"
                            class="mr-2 text-green-700"
                        />Welcome to eBaryo Library
                    </span>
                </h1>
                <p
                    class="text-lg text-gray-600 max-w-xl mx-auto mb-6 animate-fade-in delay-150"
                >
                    Discover, read, and access books for your community —
                    anytime, anywhere.
                </p>
                <div
                    class="flex flex-col md:flex-row justify-center gap-4 animate-fade-in delay-300"
                >
                    <Link
                        :href="route('login')"
                        class="w-full md:w-auto bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700 focus:bg-green-800 transition flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-green-400 scale-100 hover:scale-105 active:scale-95 animate-pulse-cta"
                        aria-label="Login"
                    >
                        <font-awesome-icon icon="sign-in-alt" /> Login
                    </Link>
                    <Link
                        :href="route('register')"
                        class="w-full md:w-auto border border-green-600 text-green-600 px-6 py-2 rounded shadow hover:bg-green-50 focus:bg-green-100 transition flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-green-400 scale-100 hover:scale-105 active:scale-95 animate-pulse-cta"
                        aria-label="Register"
                    >
                        <font-awesome-icon icon="user-plus" /> Register
                    </Link>
                </div>
                <div
                    class="absolute left-1/2 -translate-x-1/2 bottom-0 mt-8 flex flex-col items-center animate-bounce-scroll z-10"
                >
                    <span class="text-green-400 text-3xl">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </span>
                    <span class="text-xs text-green-500 mt-1">Scroll down</span>
                </div>
            </div>

            <div class="my-12 border-t border-green-200"></div>

            <section id="about" class="mt-16 animate-fade-in">
                <div class="max-w-2xl mx-auto text-center">
                    <h2 class="text-2xl font-semibold text-green-700 mb-3">
                        <font-awesome-icon icon="info-circle" /> About eBaryo
                        Library
                    </h2>
                    <p class="text-gray-600">
                        eBaryo Library is a digital platform designed to bring
                        books and educational resources to your community. Our
                        mission is to make reading accessible, enjoyable, and
                        convenient for everyone.
                    </p>
                </div>
            </section>

            <div class="my-12 border-t border-green-200"></div>

            <section id="features" class="mt-16 animate-fade-in">
                <h2
                    class="text-2xl font-semibold text-green-700 mb-6 text-center"
                >
                    <font-awesome-icon icon="star" /> Features
                </h2>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        class="bg-white border-t-4 border-green-600 rounded-2xl shadow transition hover:scale-105 hover:shadow-md p-5 animate-slide-up ripple-card"
                        tabindex="0"
                    >
                        <h3 class="text-lg font-semibold text-green-700 mb-2">
                            <font-awesome-icon icon="users" /> Community Access
                        </h3>
                        <p class="text-gray-600">
                            Connect with your local library and access a wide
                            range of books curated for your community.
                        </p>
                    </div>
                    <div
                        class="bg-white border-t-4 border-green-600 rounded-2xl shadow transition hover:scale-105 hover:shadow-md p-5 animate-slide-up delay-150 ripple-card"
                        tabindex="0"
                    >
                        <h3 class="text-lg font-semibold text-green-700 mb-2">
                            <font-awesome-icon icon="tablet-alt" /> Digital
                            Library
                        </h3>
                        <p class="text-gray-600">
                            Browse and read books online, anytime and anywhere,
                            from any device.
                        </p>
                    </div>
                    <div
                        class="bg-white border-t-4 border-green-600 rounded-2xl shadow transition hover:scale-105 hover:shadow-md p-5 animate-slide-up delay-300 ripple-card"
                        tabindex="0"
                    >
                        <h3 class="text-lg font-semibold text-green-700 mb-2">
                            <font-awesome-icon icon="id-badge" /> User Profiles
                        </h3>
                        <p class="text-gray-600">
                            Create your own profile, track your reading
                            progress, and manage your favorite books.
                        </p>
                    </div>
                </div>
            </section>

            <div class="my-12 border-t border-green-200"></div>

            <section id="testimonials" class="mt-16 animate-fade-in">
                <h2
                    class="text-2xl font-semibold text-green-700 mb-6 text-center"
                >
                    <font-awesome-icon icon="quote-left" /> What Our Users Say
                </h2>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        class="bg-green-100 border-l-4 border-green-600 rounded-2xl text-center p-6 shadow animate-slide-up"
                    >
                        <div class="flex justify-center mb-2">
                            <font-awesome-icon
                                icon="user-circle"
                                class="text-3xl text-green-400"
                            />
                        </div>
                        <blockquote>
                            <p class="text-green-700 mb-2">
                                <font-awesome-icon icon="smile" /> "eBaryo
                                Library made it so easy for me to find books for
                                my kids!"
                            </p>
                            <footer class="text-green-600">
                                Maria, Parent
                            </footer>
                        </blockquote>
                    </div>
                    <div
                        class="bg-green-100 border-l-4 border-green-600 rounded-2xl text-center p-6 shadow animate-slide-up delay-150"
                    >
                        <div class="flex justify-center mb-2">
                            <font-awesome-icon
                                icon="user-circle"
                                class="text-3xl text-green-400"
                            />
                        </div>
                        <blockquote>
                            <p class="text-green-700 mb-2">
                                <font-awesome-icon icon="smile" /> "I love being
                                able to read anywhere, anytime. Highly
                                recommended!"
                            </p>
                            <footer class="text-green-600">
                                John, Student
                            </footer>
                        </blockquote>
                    </div>
                    <div
                        class="bg-green-100 border-l-4 border-green-600 rounded-2xl text-center p-6 shadow animate-slide-up delay-300"
                    >
                        <div class="flex justify-center mb-2">
                            <font-awesome-icon
                                icon="user-circle"
                                class="text-3xl text-green-400"
                            />
                        </div>
                        <blockquote>
                            <p class="text-green-700 mb-2">
                                <font-awesome-icon icon="smile" /> "A wonderful
                                resource for our whole community."
                            </p>
                            <footer class="text-green-600">
                                Liza, Teacher
                            </footer>
                        </blockquote>
                    </div>
                </div>
            </section>

            <div class="my-12 border-t border-green-200"></div>

            <section class="mt-16 text-center animate-fade-in">
                <div class="max-w-xl mx-auto mb-6">
                    <h2 class="text-xl font-semibold text-green-700 mb-2">
                        ...and more to come!
                    </h2>
                    <p class="text-gray-600">
                        Stay tuned for new features and updates as we continue
                        to improve your library experience.
                    </p>
                </div>
            </section>

            <transition name="fade-btt">
                <button
                    v-show="showBackToTop"
                    @click="scrollToTop"
                    class="fixed bottom-6 right-6 bg-green-600 text-white p-3 rounded-full shadow-lg hover:bg-green-700 focus:bg-green-800 transition z-50 focus:outline-none focus:ring-2 focus:ring-green-400 animate-btt"
                    aria-label="Back to top"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 15l7-7 7 7"
                        />
                    </svg>
                </button>
            </transition>
        </section>
    </GuestLayout>
</template>

<style scoped>
@keyframes gradient-move {
    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}

.animate-gradient-move {
    background-size: 200% 200%;
    animation: gradient-move 8s ease-in-out infinite;
}

@keyframes fade-in {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.animate-fade-in {
    animation: fade-in 1s both;
}

.animate-fade-in.delay-150 {
    animation-delay: 0.15s;
}

.animate-fade-in.delay-300 {
    animation-delay: 0.3s;
}

@keyframes slide-down {
    from {
        transform: translateY(-30px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-slide-down {
    animation: slide-down 0.7s cubic-bezier(0.4, 0, 0.2, 1) both;
}

@keyframes slide-up {
    from {
        transform: translateY(30px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-slide-up {
    animation: slide-up 0.7s cubic-bezier(0.4, 0, 0.2, 1) both;
}

.animate-slide-up.delay-150 {
    animation-delay: 0.15s;
}

.animate-slide-up.delay-300 {
    animation-delay: 0.3s;
}

@keyframes pulse-cta {
    0%,
    100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
    }

    50% {
        box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
    }
}

.animate-pulse-cta {
    animation: pulse-cta 2s infinite;
}

.ripple-card {
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.ripple-card:focus {
    outline: 2px solid #34d399;
    outline-offset: 2px;
}

.ripple-card::after {
    content: "";
    position: absolute;
    left: 50%;
    top: 50%;
    width: 0;
    height: 0;
    background: rgba(16, 185, 129, 0.15);
    border-radius: 100%;
    transform: translate(-50%, -50%);
    transition: width 0.4s ease, height 0.4s ease;
    pointer-events: none;
}

.ripple-card:hover::after,
.ripple-card:focus::after {
    width: 200%;
    height: 200%;
}

@keyframes bounce-scroll {
    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(12px);
    }
}

.animate-bounce-scroll {
    animation: bounce-scroll 1.5s infinite;
}

@keyframes btt-fade-in {
    from {
        opacity: 0;
        transform: scale(0.8);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes btt-fade-out {
    from {
        opacity: 1;
        transform: scale(1);
    }

    to {
        opacity: 0;
        transform: scale(0.8);
    }
}

.fade-btt-enter-active {
    animation: btt-fade-in 0.4s;
}

.fade-btt-leave-active {
    animation: btt-fade-out 0.4s;
}
</style>
