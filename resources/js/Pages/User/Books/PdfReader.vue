<script setup>
import { usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import PdfReader from '@/Components/PdfReader.vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faArrowLeft } from '@fortawesome/free-solid-svg-icons';

library.add(faArrowLeft);

defineOptions({ layout: UserLayout });

const { props } = usePage();
const book = props.book;


const goBack = () => {
  window.history.length > 1
    ? window.history.back()
    : router.visit('/home');
}

const fileUrl = computed(() => book.ebook_file);

// Set --vh CSS variable for mobile viewport height
if (typeof window !== 'undefined') {
  const setVh = () => {
    document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
  };
  setVh();
  window.addEventListener('resize', setVh);
}
</script>

<template>
  <div class="fixed inset-0 bg-white z-50 flex flex-col">
    <div class="flex items-center p-4 bg-green-700 text-white shadow">
      <button @click="goBack" class="flex items-center gap-2 font-semibold hover:underline">
        <font-awesome-icon icon="arrow-left" />
        Back
      </button>
      <span class="ml-4 text-lg font-bold truncate">{{ book.title }}</span>
    </div>
    <div class="flex-1 min-h-0">
      <PdfReader :url="fileUrl" />
    </div>
  </div>
</template>

<style scoped>
.fixed {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  width: 100vw;
  height: calc(var(--vh, 1vh) * 100);
  background: #fff;
  z-index: 100;
  display: flex;
  flex-direction: column;
}
.flex-1 {
  flex: 1 1 0%;
  min-height: 0;
  overflow: auto;
  height: calc(var(--vh, 1vh) * 100 - 56px); /* 56px = header height, adjust if needed */
}
</style> 