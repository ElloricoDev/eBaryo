<script setup>
import { computed } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faTag } from "@fortawesome/free-solid-svg-icons";

library.add(faTag);

const props = defineProps({
    book: Object,
    isSaved: Boolean,
    auth: Object,
});

const avatarUrl = computed(() =>
    props.book.cover_image && props.book.cover_image !== ""
        ? props.book.cover_image
        : "/images/default.svg"
);

defineOptions({ inheritAttrs: false });
</script>

<template>
    <Link
        :href="route('books.view', { id: book.id })"
        v-bind="$attrs"
        :data-book-id="book.id"
        class="book-image-link group relative flex flex-col items-center justify-center cursor-pointer focus:outline-none"
        tabindex="0"
        aria-label="Book card for {{ book.title }}"
        @click="
            () => {
                if (typeof window !== 'undefined' && window.sessionStorage)
                    window.sessionStorage.setItem(
                        'lastPage',
                        window.location.pathname + window.location.search
                    );
            }
        "
    >
        <img
            :src="avatarUrl"
            class="book-image-main"
            alt="Book Cover"
            @error="event.target.src = '/images/default.svg'"
        />
        <h4
            class="text-xs sm:text-sm font-medium text-center mt-2 line-clamp-2 break-words max-w-[120px] mx-auto"
        >
            {{ book.title }}
        </h4>
    </Link>
</template>

<style scoped>
.book-image-link {
    background: none;
    border: none;
    box-shadow: none;
    min-width: 0;
    min-height: 0;
    padding: 0;
    overflow: visible;
    margin: 0 6px;
}
.book-image-main {
    width: 140px;
    height: 200px;
    object-fit: cover;
    border-radius: 1rem;
    display: block;
    margin: 0 auto;
    box-shadow: 0 2px 8px 0 rgba(16, 185, 129, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    background: #f3f4f6;
}
.group:hover .book-image-main {
    transform: scale(1.04);
    box-shadow: 0 4px 16px 0 rgba(16, 185, 129, 0.18);
}
</style>
