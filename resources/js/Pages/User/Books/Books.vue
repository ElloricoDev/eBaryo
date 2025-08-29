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
        <div class="mx-auto px-4">
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4"
            >
                <h1 class="text-2xl font-bold text-green-700">Browse Books</h1>
                <p v-if="q" class="text-sm text-gray-600">Search: "{{ q }}"</p>
                <div>
                    <label
                        for="category"
                        class="mr-2 font-semibold text-green-700"
                        >Category:</label
                    >
                    <select
                        id="category"
                        class="border border-green-300 rounded px-3 py-2 focus:border-green-600 focus:ring-2 focus:ring-green-200"
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
                </div>
            </div>

            <BookSection
                title="New Books"
                icon="star"
                :books="newBooks"
                sectionType="new"
                :savedBookIds="savedBookIds"
                :auth="auth"
                emptyMessage="There is no new book for this month."
                emptyIcon="star"
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
                iconColor="text-blue-600"
                titleColor="text-green-700"
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
                iconColor="text-yellow-600"
                titleColor="text-green-700"
                @save="saveBook"
                @unsave="unsaveBook"
            />
        </div>
    </UserLayout>
</template>
