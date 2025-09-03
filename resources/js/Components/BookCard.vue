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
        <div class="relative">
            <img
                :src="avatarUrl"
                class="book-image-main"
                alt="Book Cover"
                @error="(e) => { const t = e?.target; if (t && t.tagName) t.src = '/images/default.svg'; }"
            />
            <div class="pointer-events-none absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-gradient-to-t from-black/10 to-transparent"></div>
            <span class="sr-only">Open {{ book.title }}</span>
        </div>
        <h4
            class="book-title"
            :title="book.title"
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
.book-image-link:focus-visible .book-image-main {
    outline: 3px solid rgba(16, 185, 129, 0.6);
    outline-offset: 3px;
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
.book-title {
    text-align: center;
    margin-top: 0.5rem;
    font-weight: 600;
    font-size: 0.85rem;
    line-height: 1.2;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
    max-width: 140px;
}
@media (min-width: 640px) {
    .book-image-main { width: 160px; height: 220px; }
    .book-title { max-width: 160px; font-size: 0.95rem; }
}
</style>
