<script setup>
import { useForm, router, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Swal from 'sweetalert2';

defineOptions({layout:UserLayout})
const props = defineProps({
  genres: Array,
  selected: Array
});

const form = useForm({ genres: [...props.selected] });
const page = usePage();

function submit() {
  form.post(route('user.profile.genres.update'), {
    onSuccess: () => {
      Swal.fire({ icon: 'success', title: 'Interests updated!', timer: 1500, showConfirmButton: false });
      setTimeout(() => router.visit(route('user.profile.index'), { replace: true }), 1500);
    }
  });
}
</script>

<template>
  <div class="max-w-xl mx-auto py-10 px-4">
    <h2 class="text-2xl font-bold text-green-700 mb-4 text-center">Edit Your Interests</h2>
    <form @submit.prevent="submit" class="space-y-4">
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <label v-for="genre in genres" :key="genre.id" class="flex items-center gap-2 p-2 border rounded cursor-pointer hover:bg-green-50">
          <input type="checkbox" :value="genre.id" v-model="form.genres" class="accent-green-600" />
          <span class="text-green-800 font-semibold">{{ genre.name }}</span>
        </label>
      </div>
      <div class="flex justify-end mt-6">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Update Interests</button>
      </div>
    </form>
  </div>
</template> 