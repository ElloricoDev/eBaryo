<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faQuoteLeft, faSave, faTimes } from '@fortawesome/free-solid-svg-icons';
import Swal from 'sweetalert2';
defineOptions({layout:AdminLayout});


const props = defineProps({ testimonial: Object });

const form = useForm({
    author_name: props.testimonial.author_name,
    role: props.testimonial.role,
    content: props.testimonial.content,
    approved: props.testimonial.approved,
});

function submit() {
    form.put(route('admin.testimonials.update', props.testimonial.id), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Testimonial updated!',
                text: 'The testimonial has been updated successfully.',
                timer: 2000,
                showConfirmButton: false,
            });
        }
    });
}
</script>

<template>
    <Head title="Edit Testimonials"/>
    <div class="p-6 max-w-xl mx-auto">
        <Link :href="route('admin.testimonials.index')" class="inline-flex items-center gap-2 text-gray-600 hover:text-green-700 mb-4">
            <font-awesome-icon icon="fa-arrow-left" /> Back
        </Link>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-4 flex items-center gap-2">
                <font-awesome-icon :icon="faQuoteLeft" class="text-green-600" /> Edit Testimonial
            </h1>
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block font-semibold mb-1">Author Name</label>
                    <input v-model="form.author_name" type="text" class="w-full border rounded px-3 py-2 focus:ring focus:ring-green-200" />
                    <div v-if="form.errors.author_name" class="text-red-600 text-sm">{{ form.errors.author_name }}</div>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Role</label>
                    <input v-model="form.role" type="text" class="w-full border rounded px-3 py-2 focus:ring focus:ring-green-200" placeholder="e.g. Parent, Student, Teacher" />
                    <div v-if="form.errors.role" class="text-red-600 text-sm">{{ form.errors.role }}</div>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Content</label>
                    <textarea v-model="form.content" class="w-full border rounded px-3 py-2 focus:ring focus:ring-green-200" rows="4"></textarea>
                    <div v-if="form.errors.content" class="text-red-600 text-sm">{{ form.errors.content }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <input v-model="form.approved" type="checkbox" id="approved" />
                    <label for="approved">Approved</label>
                </div>
                <div class="flex gap-4 mt-4">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center gap-2">
                        <font-awesome-icon :icon="faSave" /> Update
                    </button>
                    <Link :href="route('admin.testimonials.index')" class="text-gray-600 hover:underline flex items-center gap-2">
                        <font-awesome-icon :icon="faTimes" /> Cancel
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template> 