<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faPlus, faEdit, faTrash, faCheckCircle, faTimesCircle, faQuoteLeft } from '@fortawesome/free-solid-svg-icons';
defineOptions({layout:AdminLayout});


const props = defineProps({
    testimonials: Array,
});

function destroy(id) {
    if (confirm('Are you sure you want to delete this testimonial?')) {
        router.delete(route('admin.testimonials.destroy', id));
    }
}

function toggleApproved(testimonial) {
    router.put(route('admin.testimonials.update', testimonial.id), {
        ...testimonial,
        approved: !testimonial.approved,
    }, {
        preserveScroll: true,
        only: ['testimonials'],
    });
}
</script>

<template>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <font-awesome-icon :icon="faQuoteLeft" class="text-green-600" /> Testimonials
            </h1>
            <Link :href="route('admin.testimonials.create')" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center gap-2">
                <font-awesome-icon :icon="faPlus" /> Add Testimonial
            </Link>
        </div>
        <div class="bg-white rounded-xl shadow-lg overflow-x-auto p-4">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Author</th>
                        <th class="px-4 py-2">Role</th>
                        <th class="px-4 py-2">Content</th>
                        <th class="px-4 py-2">Approved</th>
                        <th class="px-4 py-2">Created At</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="testimonial in testimonials" :key="testimonial.id" class="hover:bg-green-50 transition">
                        <td class="border px-4 py-2">{{ testimonial.author_name }}</td>
                        <td class="border px-4 py-2">{{ testimonial.role }}</td>
                        <td class="border px-4 py-2 max-w-xs truncate" :title="testimonial.content">{{ testimonial.content }}</td>
                        <td class="border px-4 py-2">
                            <button
                                @click="toggleApproved(testimonial)"
                                :class="testimonial.approved ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700'"
                                class="px-3 py-1 rounded focus:outline-none focus:ring flex items-center gap-1"
                                :aria-pressed="testimonial.approved"
                            >
                                <font-awesome-icon :icon="testimonial.approved ? faCheckCircle : faTimesCircle" />
                                {{ testimonial.approved ? 'Approved' : 'Not Approved' }}
                            </button>
                        </td>
                        <td class="border px-4 py-2">{{ new Date(testimonial.created_at).toLocaleString() }}</td>
                        <td class="border px-4 py-2 flex gap-2">
                            <Link :href="route('admin.testimonials.edit', testimonial.id)" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                <font-awesome-icon :icon="faEdit" /> Edit
                            </Link>
                            <button @click="destroy(testimonial.id)" class="text-red-600 hover:text-red-800 flex items-center gap-1">
                                <font-awesome-icon :icon="faTrash" /> Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template> 