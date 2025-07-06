<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTags, faArrowLeft, faAlignLeft, faList } from '@fortawesome/free-solid-svg-icons'

library.add(faTags, faArrowLeft, faAlignLeft, faList)

defineOptions({ layout: AdminLayout });

const props = defineProps({
    category: Object
});
const category = props.category;
</script>

<template>
    <Head :title="`View Category: ${category.name}`" />
    <div class="max-w-4xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-green-600 flex items-center gap-2">
                <font-awesome-icon icon="tags" /> Category Details
            </h1>
            <Link
                :href="route('admin.categories.index')"
                class="inline-flex items-center bg-green-600 text-white font-semibold px-5 py-2 rounded-full shadow hover:bg-green-700 transition"
            >
                <font-awesome-icon icon="arrow-left" class="mr-2" /> Back to Categories
            </Link>
        </div>

        <!-- Card -->
        <div class="bg-white border border-green-600 rounded-2xl shadow p-6">
            <div class="mb-4">
                <h2 class="text-green-700 font-semibold flex items-center gap-1">
                    <font-awesome-icon icon="align-left" /> Name:
                </h2>
                <p class="text-lg font-semibold text-gray-800">{{ category.name }}</p>
            </div>
            <div class="mb-4">
                <h2 class="text-green-700 font-semibold flex items-center gap-1">
                    <font-awesome-icon icon="list" /> Description:
                </h2>
                <p class="text-gray-600">{{ category.description || 'No description' }}</p>
            </div>
        </div>
    </div>
</template>
