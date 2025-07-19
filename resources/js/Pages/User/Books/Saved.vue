<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import BookCard from "@/Components/BookCard.vue";
import BookSection from "@/Components/BookSection.vue";
import { usePage, router, Head } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faBookmark, faArrowLeft, faPlayCircle, faCheckCircle } from "@fortawesome/free-solid-svg-icons";

defineOptions({ layout: UserLayout });
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
    <div>
        <button
            @click="goBack"
            class="block sm:hidden mb-4 text-green-700 font-bold flex items-center gap-2"
        >
            <font-awesome-icon icon="arrow-left" class="text-lg" />
            Back
        </button>
        <div class="relative min-h-screen overflow-x-hidden">
            <h1
                class="text-green-700 font-bold text-2xl flex items-center gap-2 mb-6"
            >
                <font-awesome-icon
                    icon="bookmark"
                    class="text-green-600 text-3xl"
                />
                My Library
            </h1>

            <!-- Currently Reading Section -->
            <section class="mb-10">
                <BookSection
                    title="Currently Reading"
                    icon="play-circle"
                    :books="currentlyReadingBooks.map(book => ({ ...book, last_percent: book.progress, progress: book.progress }))"
                    sectionType="continue"
                    :savedBookIds="$page.props.saved_books || []"
                    :auth="$page.props.auth"
                    emptyMessage="No books currently being read. Start reading a book and it will appear here."
                />
            </section>

            <!-- Finished Reading Section -->
            <section class="mb-10">
                <BookSection
                    title="Finished Reading"
                    icon="check-circle"
                    :books="finishedBooks.map(book => ({ ...book, last_percent: book.progress, progress: book.progress }))"
                    sectionType="finished"
                    :savedBookIds="$page.props.saved_books || []"
                    :auth="$page.props.auth"
                    emptyMessage="No finished books yet. Books you finish will appear here."
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
                />
            </section>
        </div>
    </div>
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
