<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import BookCard from "@/Components/BookCard.vue";
import BookSection from "@/Components/BookSection.vue";
import { usePage, router, Head } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faBookmark, faArrowLeft, faPlayCircle, faCheckCircle } from "@fortawesome/free-solid-svg-icons";

const { props } = usePage();
const currentlyReadingBooks = props.currentlyReadingBooks || [];
const finishedBooks = props.finishedBooks || [];
const savedBooks = props.books || [];

library.add(faBookmark);
library.add(faArrowLeft);
library.add(faPlayCircle);
library.add(faCheckCircle);

const unsaveBook = (book) => {
    router.post(
        route("books.unsave", { id: book.id }),
        {},
        {
            onSuccess: () => {},
            onError: (errors) => {
                console.error("Error unsaving book:", errors);
            },
        }
    );
};

const goBack = () => {
    window.history.length > 1 ? window.history.back() : Inertia.visit("/home");
};
</script>

<template>
    <Head title="Saved Books" />
    <UserLayout>
        <!-- Back Button for Mobile -->
        <div class="mb-6">
            <button
                @click="goBack"
                class="block sm:hidden text-green-700 font-bold flex items-center gap-2 hover:text-green-800 transition-colors"
            >
                <font-awesome-icon icon="arrow-left" class="text-lg" />
                Back
            </button>
        </div>

        <!-- Hero Header -->
        <div class="mb-12 bg-gradient-to-br from-green-50 via-white to-emerald-50 rounded-3xl p-8 border border-green-100 shadow-sm">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4 flex items-center justify-center gap-3">
                    <font-awesome-icon icon="bookmark" class="text-green-600" />
                    My Personal Library
                </h1>
                <p class="text-xl text-slate-600 leading-relaxed mb-6">
                    Your curated collection of books, reading progress, and completed stories
                </p>
                
                <!-- Stats Cards -->
                <div class="flex flex-wrap justify-center gap-6">
                    <div class="bg-white px-6 py-4 rounded-2xl shadow-sm border border-green-200">
                        <div class="text-2xl font-bold text-green-600">{{ currentlyReadingBooks?.length || 0 }}</div>
                        <div class="text-sm text-slate-600">Currently Reading</div>
                    </div>
                    <div class="bg-white px-6 py-4 rounded-2xl shadow-sm border border-green-200">
                        <div class="text-2xl font-bold text-green-600">{{ finishedBooks?.length || 0 }}</div>
                        <div class="text-sm text-slate-600">Completed</div>
                    </div>
                    <div class="bg-white px-6 py-4 rounded-2xl shadow-sm border border-green-200">
                        <div class="text-2xl font-bold text-green-600">{{ savedBooks?.length || 0 }}</div>
                        <div class="text-sm text-slate-600">Saved Books</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Book Sections with Enhanced Spacing -->
        <div class="space-y-16">
            <!-- Currently Reading Section -->
            <section>
                <BookSection
                    title="Currently Reading"
                    icon="play-circle"
                    :books="currentlyReadingBooks.map(book => ({ ...book, last_percent: book.progress, progress: book.progress }))"
                    sectionType="continue"
                    :savedBookIds="$page.props.saved_books || []"
                    :auth="$page.props.auth"
                    emptyMessage="No books currently being read. Start reading a book and it will appear here."
                    titleColor="text-slate-800"
                    iconColor="text-green-600"
                    bgColor="bg-green-50"
                    borderColor="border-green-200"
                    badgeText="In Progress"
                    badgeColor="bg-gradient-to-r from-green-400 to-emerald-400"
                />
            </section>

            <!-- Finished Reading Section -->
            <section>
                <BookSection
                    title="Finished Reading"
                    icon="check-circle"
                    :books="finishedBooks.map(book => ({ ...book, last_percent: book.progress, progress: book.progress }))"
                    sectionType="finished"
                    :savedBookIds="$page.props.saved_books || []"
                    :auth="$page.props.auth"
                    emptyMessage="No finished books yet. Books you finish will appear here."
                    titleColor="text-slate-800"
                    iconColor="text-emerald-600"
                    bgColor="bg-emerald-50"
                    borderColor="border-emerald-200"
                    badgeText="Completed"
                    badgeColor="bg-gradient-to-r from-emerald-400 to-teal-400"
                />
            </section>

            <!-- Saved Books Section -->
            <section>
                <BookSection
                    title="Saved Books"
                    icon="bookmark"
                    :books="savedBooks"
                    sectionType="saved"
                    :savedBookIds="$page.props.saved_books || []"
                    :auth="$page.props.auth"
                    emptyMessage="No saved books yet. Books you save will appear here for quick access."
                    titleColor="text-slate-800"
                    iconColor="text-blue-600"
                    bgColor="bg-blue-50"
                    borderColor="border-blue-200"
                    badgeText="Saved"
                    badgeColor="bg-gradient-to-r from-blue-400 to-indigo-400"
                />
            </section>
        </div>
    </UserLayout>
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
input:focus,
button:focus,
a:focus {
    outline: 2px solid #34d399;
    outline-offset: 2px;
}
</style>
