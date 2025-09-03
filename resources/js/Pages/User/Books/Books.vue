<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import BookSection from "@/Components/BookSection.vue";
import { usePage, router, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faStar,
    faChartLine,
    faTrophy,
    faChevronRight,
    faChevronLeft,
} from "@fortawesome/free-solid-svg-icons";

library.add(
    faStar,
    faChartLine,
    faTrophy,
    faChevronRight,
    faChevronLeft
);

const { props } = usePage();
const newBooks = props.newBooks || [];
const mostReadBooks = props.mostReadBooks || [];
const highestRatedBooks = props.highestRatedBooks || [];
const categories = props.categories || [];
const selectedCategory = props.selectedCategory || null;
const q = props.q || "";
const savedBookIds = ref(
    Array.isArray(props.saved_books) ? props.saved_books : []
);
const auth = props.auth || {};

const handleCategoryChange = (e) => {
    const categoryId = e.target.value;
    const params = {};
    if (categoryId) params.category = categoryId;
    if (q && q.length > 0) params.q = q;
    router.visit(route("books.index", params));
};

const saveBook = (book) => {
    router.post(
        route("books.save", { id: book.id }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                if (!savedBookIds.value.includes(book.id)) {
                    savedBookIds.value.push(book.id);
                }
            },
        }
    );
};
const unsaveBook = (book) => {
    router.post(
        route("books.unsave", { id: book.id }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                const index = savedBookIds.value.indexOf(book.id);
                if (index > -1) {
                    savedBookIds.value.splice(index, 1);
                }
            },
        }
    );
};
</script>

<template>
    <Head title="Browse Books" />
    <UserLayout>
        <!-- Hero Header Section -->
        <div class="mb-12 bg-gradient-to-br from-blue-50 via-white to-indigo-50 rounded-3xl p-8 border border-blue-100 shadow-sm">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4">
                    Discover Amazing Books 📚
                </h1>
                <p class="text-xl text-slate-600 mb-8 leading-relaxed">
                    Explore our collection of carefully curated books across all genres
                </p>
                
                <!-- Enhanced Search and Filter Controls -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <div class="relative">
                        <label for="category" class="block text-sm font-medium text-slate-700 mb-2 text-left">
                            Category Filter
                        </label>
                        <select
                            id="category"
                            class="appearance-none bg-white border border-blue-300 rounded-2xl px-6 py-3 pr-10 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm hover:shadow-md"
                            @change="handleCategoryChange"
                            :value="selectedCategory?.id || ''"
                        >
                            <option value="">All Categories</option>
                            <option
                                v-for="cat in categories"
                                :key="cat.id"
                                :value="cat.id"
                            >
                                {{ cat.name }}
                            </option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div v-if="q" class="bg-amber-50 border border-amber-200 rounded-2xl px-6 py-3">
                        <span class="text-amber-800 font-medium">Search: "{{ q }}"</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Book Sections with Enhanced Spacing -->
        <div class="space-y-16">
            <BookSection
                title="New Books"
                icon="star"
                :books="newBooks"
                sectionType="new"
                :savedBookIds="savedBookIds"
                :auth="auth"
                emptyMessage="There is no new book for this month."
                emptyIcon="star"
                badgeText="Fresh"
                badgeColor="bg-gradient-to-r from-blue-400 to-indigo-400"
                borderColor="border-blue-300"
                bgColor="bg-gradient-to-br from-blue-50 to-indigo-50"
                iconColor="text-blue-600"
                titleColor="text-slate-800"
                @save="saveBook"
                @unsave="unsaveBook"
            />
            
            <BookSection
                title="Most Read Books"
                icon="chart-line"
                :books="mostReadBooks"
                sectionType="mostread"
                :savedBookIds="savedBookIds"
                :auth="auth"
                emptyMessage="No reading statistics available yet. Be the first to read a book!"
                emptyIcon="chart-line"
                badgeText="Popular"
                badgeColor="bg-gradient-to-r from-purple-400 to-pink-400"
                borderColor="border-purple-300"
                bgColor="bg-gradient-to-br from-purple-50 to-pink-50"
                iconColor="text-purple-600"
                titleColor="text-slate-800"
                @save="saveBook"
                @unsave="unsaveBook"
            />
            
            <BookSection
                title="Highest Ratings"
                icon="trophy"
                :books="highestRatedBooks"
                sectionType="highestrated"
                :savedBookIds="savedBookIds"
                :auth="auth"
                emptyMessage="No highly rated books yet."
                emptyIcon="trophy"
                badgeText="Top Rated"
                badgeColor="bg-gradient-to-r from-amber-400 to-yellow-400"
                borderColor="border-amber-300"
                bgColor="bg-gradient-to-br from-amber-50 to-yellow-50"
                iconColor="text-amber-600"
                titleColor="text-slate-800"
                @save="saveBook"
                @unsave="unsaveBook"
            />
        </div>
    </UserLayout>
</template>
