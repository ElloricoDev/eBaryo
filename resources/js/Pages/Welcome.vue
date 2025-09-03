<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { usePage } from "@inertiajs/vue3";
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
    faChartLine,
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
    faChartLine,
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

const { props } = usePage();
const testimonials = props.testimonials || [];
</script>

<template>

    <Head title="Welcome" />
    <GuestLayout>
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden"
            id="home">
            <!-- Background Hero Image -->
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-gradient-to-br from-green-50 via-white to-slate-50"></div>
                <img 
                    src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" 
                    alt="Library books background" 
                    class="w-full h-full object-cover opacity-50"
                />
                <div class="absolute inset-0 bg-white/60"></div>
            </div>
            
            <!-- Floating Book Icons -->
            <div class="pointer-events-none select-none z-10">
                <font-awesome-icon v-for="book in floatingBooks" :key="book.id" :id="`floating-book-${book.id}`"
                    icon="book" class="text-green-200 text-4xl opacity-60 absolute" :class="book.style"
                    :style="{ transition: 'transform 0.3s', zIndex: 10 }" />
            </div>
            <div class="text-center max-w-4xl mx-auto px-6 animate-fade-in relative z-10">
                <div class="mb-8">
                    <img src="/favicon1.png" alt="eBaryo logo"
                        class="w-16 h-16 md:w-20 md:h-20 mx-auto mb-6" />
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 text-slate-800 leading-tight">
                        Welcome to
                        <span class="text-green-600">eBaryo Library</span>
                    </h1>
                    <p class="text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
                        Discover, read, and access books for your community — anytime, anywhere.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fade-in delay-300">
                    <Link :href="route('login')"
                        class="bg-green-600 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-200 flex items-center justify-center gap-3 hover:bg-green-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2"
                        aria-label="Login">
                    <font-awesome-icon icon="sign-in-alt" /> Login
                    </Link>
                    <Link :href="route('register')"
                        class="border-2 border-green-600 text-green-600 bg-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-green-50 transition-all duration-200 flex items-center justify-center gap-3 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2"
                        aria-label="Register">
                    <font-awesome-icon icon="user-plus" /> Register
                    </Link>
                </div>
                <div
                    class="absolute left-1/2 -translate-x-1/2 bottom-8 flex flex-col items-center animate-bounce-scroll z-10">
                    <span class="text-slate-400 text-2xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                    <span class="text-xs text-slate-500 mt-2">Scroll down</span>
                </div>
            </div>

        </section>

        <section id="about" class="py-20 bg-white">
            <div class="max-w-6xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="order-2 lg:order-1">
                        <h2 class="text-4xl font-bold text-slate-800 mb-6">
                            About eBaryo Library
                        </h2>
                        <p class="text-xl text-slate-600 leading-relaxed mb-6">
                            eBaryo Library is a digital platform designed to bring books and educational resources to your community. Our mission is to make reading accessible, enjoyable, and convenient for everyone.
                        </p>
                        <div class="flex items-center space-x-4">
                            <div class="flex -space-x-2">
                                <img 
                                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80" 
                                    alt="User 1" 
                                    class="w-10 h-10 rounded-full border-2 border-white shadow-md"
                                />
                                <img 
                                    src="https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80" 
                                    alt="User 2" 
                                    class="w-10 h-10 rounded-full border-2 border-white shadow-md"
                                />
                                <img 
                                    src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80" 
                                    alt="User 3" 
                                    class="w-10 h-10 rounded-full border-2 border-white shadow-md"
                                />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-700">Join 10,000+ readers</p>
                                <p class="text-sm text-slate-500">in our community</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-1 lg:order-2">
                        <div class="relative">
                            <img 
                                src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                                alt="People reading books in library" 
                                class="w-full h-96 object-cover rounded-2xl shadow-lg"
                            />
                            <div class="absolute -bottom-6 -right-6 bg-green-600 text-white p-6 rounded-2xl shadow-xl">
                                <div class="text-center">
                                    <div class="text-3xl font-bold">10K+</div>
                                    <div class="text-sm">Books Available</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="py-20 bg-slate-50">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-slate-800 mb-4">
                        Features
                    </h2>
                    <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                        Everything you need for a seamless reading experience
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-all duration-200 border border-slate-200 group"
                        tabindex="0">
                        <div class="relative mb-6">
                            <img 
                                src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                                alt="Reading progress tracking" 
                                class="w-full h-48 object-cover rounded-xl mb-4 group-hover:scale-105 transition-transform duration-200"
                            />
                            <div class="absolute top-4 left-4 w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                                <font-awesome-icon icon="chart-line" class="text-white text-xl" />
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3">
                            Reading Progress
                        </h3>
                        <p class="text-slate-600 leading-relaxed">
                            Track your reading journey, set goals, and monitor your progress with detailed analytics and insights.
                        </p>
                    </div>
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-all duration-200 border border-slate-200 group"
                        tabindex="0">
                        <div class="relative mb-6">
                            <img 
                                src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                                alt="Digital reading" 
                                class="w-full h-48 object-cover rounded-xl mb-4 group-hover:scale-105 transition-transform duration-200"
                            />
                            <div class="absolute top-4 left-4 w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                                <font-awesome-icon icon="tablet-alt" class="text-white text-xl" />
                            </div>
                            <span class="absolute top-4 right-4 px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-medium">New</span>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3">
                            Digital Library
                        </h3>
                        <p class="text-slate-600 leading-relaxed">
                            Browse and read books online, anytime and anywhere, from any device.
                        </p>
                    </div>
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-all duration-200 border border-slate-200 group"
                        tabindex="0">
                        <div class="relative mb-6">
                            <img 
                                src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                                alt="User profile" 
                                class="w-full h-48 object-cover rounded-xl mb-4 group-hover:scale-105 transition-transform duration-200"
                            />
                            <div class="absolute top-4 left-4 w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                                <font-awesome-icon icon="id-badge" class="text-white text-xl" />
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3">
                            User Profiles
                        </h3>
                        <p class="text-slate-600 leading-relaxed">
                            Create your own profile, track your reading progress, and manage your favorite books.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimonials" class="py-20 bg-white">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-slate-800 mb-4">
                        What Our Users Say
                    </h2>
                    <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                        Hear from our community members about their experience
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-if="testimonials.length === 0" class="col-span-3 text-center text-slate-500 py-12">
                        <font-awesome-icon icon="quote-left" class="text-4xl mb-4 text-slate-300" />
                        <p class="text-lg">No testimonials yet.</p>
                    </div>
                    <div v-for="(testimonial, idx) in testimonials" :key="testimonial.id"
                        class="bg-slate-50 rounded-2xl p-8 text-center hover:shadow-md transition-all duration-200 border border-slate-200 relative overflow-hidden">
                        <!-- Background pattern -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-green-100 rounded-full -translate-y-10 translate-x-10 opacity-50"></div>
                        <div class="absolute bottom-0 left-0 w-16 h-16 bg-green-100 rounded-full translate-y-8 -translate-x-8 opacity-30"></div>
                        
                        <div class="relative z-10">
                            <div class="flex justify-center mb-6">
                                <img v-if="testimonial.avatar" :src="testimonial.avatar" alt="Avatar"
                                    class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg" />
                                <div v-else class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center shadow-lg">
                                    <font-awesome-icon icon="user-circle" class="text-4xl text-green-600" />
                                </div>
                            </div>
                            <blockquote>
                                <div class="mb-4">
                                    <font-awesome-icon icon="quote-left" class="text-green-400 text-2xl" />
                                </div>
                                <p class="text-slate-700 mb-6 leading-relaxed text-lg">
                                    "{{ testimonial.content }}"
                                </p>
                                <footer class="text-slate-600 font-medium">
                                    <div class="font-semibold text-slate-800">{{ testimonial.author_name }}</div>
                                    <div v-if="testimonial.role" class="text-sm text-slate-500">{{ testimonial.role }}</div>
                                </footer>
                            </blockquote>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-slate-50">
            <div class="max-w-6xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-4xl font-bold text-slate-800 mb-6">
                            ...and more to come!
                        </h2>
                        <p class="text-xl text-slate-600 leading-relaxed mb-8">
                            Stay tuned for new features and updates as we continue to improve your library experience.
                        </p>
                        <div class="flex items-center space-x-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">24/7</div>
                                <div class="text-sm text-slate-500">Access</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">100%</div>
                                <div class="text-sm text-slate-500">Free</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">∞</div>
                                <div class="text-sm text-slate-500">Books</div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <img 
                            src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                            alt="Future of digital reading" 
                            class="w-full h-96 object-cover rounded-2xl shadow-lg"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-green-600/20 to-transparent rounded-2xl"></div>
                        <div class="absolute bottom-6 left-6 text-white">
                            <div class="text-lg font-semibold">The Future of Reading</div>
                            <div class="text-sm opacity-90">Always evolving, always improving</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
<section>
        <transition name="fade-btt">
            <button v-show="showBackToTop" @click="scrollToTop"
                class="fixed bottom-8 right-8 bg-green-600 text-white p-3 rounded-full shadow-lg hover:bg-green-700 transition-all duration-200 z-50 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2"
                aria-label="Back to top">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
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
