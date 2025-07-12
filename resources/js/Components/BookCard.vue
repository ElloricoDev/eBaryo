<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTag } from '@fortawesome/free-solid-svg-icons'

library.add(faTag)

const props = defineProps({
  book: Object,
  isSaved: Boolean,
  auth: Object
});

const avatarUrl = computed(() => props.book.cover_image && props.book.cover_image !== '' ? props.book.cover_image : '/images/default.svg');

defineOptions({ inheritAttrs: false })
</script>

<template>
  <Link
    :href="route('books.view', { id: book.id })"
    v-bind="$attrs"
    :data-book-id="book.id"
    class="book-image-link group relative flex flex-col items-center justify-center cursor-pointer focus:outline-none"
    tabindex="0"
    aria-label="Book card for {{ book.title }}"
  >
    <img
      :src="avatarUrl"
      class="book-image-main"
      alt="Book Cover"
      @error="event.target.src = '/images/default.svg'"
    />
    <span class="book-hover-title">{{ book.title }}</span>
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
  box-shadow: 0 2px 8px 0 rgba(16,185,129,0.10);
  transition: transform 0.2s, box-shadow 0.2s;
  background: #f3f4f6;
}
.group:hover .book-image-main {
  transform: scale(1.04);
  box-shadow: 0 4px 16px 0 rgba(16,185,129,0.18);
}
.book-hover-title {
  display: none;
  position: absolute;
  bottom: 8px;
  left: 0;
  right: 0;
  text-align: center;
  color: #fff;
  background: rgba(0,0,0,0.6);
  font-weight: bold;
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
  padding: 4px 0;
  font-size: 0.95em;
  pointer-events: none;
}
.group:hover .book-hover-title {
  display: block;
}
</style>
