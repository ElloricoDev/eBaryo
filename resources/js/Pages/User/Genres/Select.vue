<script setup>
import { useForm, router, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

defineOptions({layout:UserLayout})
const props = defineProps({
  genres: Array,
  selected: Array
});

const form = useForm({ genres: [...props.selected] });

function submit() {
  form.post(route('user.genres.store'));
}

function skip() {
  router.post('/user/genres/skip');
}
</script>

<template>
    <Head title="Select genre"/>
  <div class="max-w-xl mx-auto py-10 px-4">
    <h2 class="text-2xl font-bold text-green-700 mb-4 text-center">Select Your Favorite Genres</h2>
    <form @submit.prevent="submit" class="space-y-4">
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <label v-for="genre in genres" :key="genre.id" class="flex items-center gap-2 p-2 border rounded cursor-pointer hover:bg-green-50">
          <input type="checkbox" :value="genre.id" v-model="form.genres" class="accent-green-600" />
          <span class="text-green-800 font-semibold">{{ genre.name }}</span>
        </label>
      </div>
      <div class="flex justify-between mt-6">
        <button type="button" @click="skip" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Skip</button>
        <div class="text-sm text-gray-600">Selected: {{ form.genres.length }} genres</div>
        <button type="submit"
          :disabled="form.genres.length === 0"
          :class="[ 'px-4 py-2 rounded', form.genres.length === 0 ? 'bg-green-300 text-white cursor-not-allowed' : 'bg-green-600 text-white hover:bg-green-700' ]"
        >Save</button>
      </div>
    </form>
  </div>
</template> 