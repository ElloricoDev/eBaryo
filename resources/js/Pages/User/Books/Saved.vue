<script setup>
import UserLayout from "@/Layouts/UserLayout.vue";
import BookCard from "@/Components/BookCard.vue";
import { usePage, router, Head } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faBookmark } from "@fortawesome/free-solid-svg-icons";
import { faArrowLeft } from "@fortawesome/free-solid-svg-icons";

defineOptions({ layout: UserLayout });
const { props } = usePage();
const books = props.books || [];

library.add(faBookmark);
library.add(faArrowLeft);

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
                Saved Books
            </h1>

            <div
                class="grid grid-cols-2 gap-3 px-1 pb-6 sm:grid-cols-2 lg:grid-cols-5 sm:gap-6 sm:px-0 mt-4 animate-fade-in"
            >
                <template v-if="books.length > 0">
                    <BookCard
                        v-for="book in books"
                        :key="book.id"
                        :book="{ ...book, from: 'saved' }"
                        :isSaved="true"
                        :auth="$page.props.auth"
                        @unsave="unsaveBook"
                        class="w-full max-w-[180px] mx-auto sm:max-w-[220px] sm:min-w-[200px]"
                    >
                    </BookCard>
                </template>
                <template v-else>
                    <div
                        class="col-span-full flex flex-col items-center justify-center text-center text-gray-500 py-10 animate-fade-in"
                    >
                        <font-awesome-icon
                            icon="bookmark"
                            class="text-green-200 text-6xl mb-4"
                        />
                        <p class="text-lg">
                            No saved books yet.<br /><span
                                class="text-sm text-gray-400"
                                >Books you save will appear here for quick
                                access.</span
                            >
                        </p>
                    </div>
                </template>
            </div>
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
