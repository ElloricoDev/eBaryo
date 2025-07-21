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
    <div class="max-w-4xl mx-auto py-10 px-4">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
                <font-awesome-icon icon="tags" /> Category Details
            </h1>
            <Link
                :href="route('admin.categories.index')"
                class="inline-flex items-center bg-green-600 text-white font-semibold px-6 py-2 rounded-full shadow-lg hover:bg-green-700 transition-transform duration-200 hover:scale-105 text-base"
            >
                <font-awesome-icon icon="arrow-left" class="mr-2" /> Back to Categories
            </Link>
        </div>

        <!-- Card -->
        <div class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 rounded-2xl shadow-2xl p-8">
            <div class="mb-8 flex items-center gap-3">
                <span class="inline-block px-4 py-1 rounded-full bg-green-200 text-green-800 font-bold text-lg">
                    <font-awesome-icon icon="align-left" class="mr-1" /> {{ category.name }}
                </span>
            </div>
            <div class="mb-4">
                <h2 class="text-green-700 font-semibold flex items-center gap-1 mb-1">
                    <font-awesome-icon icon="list" /> Description:
                </h2>
                <p class="text-gray-700 bg-green-50 rounded-lg p-4">{{ category.description || 'No description' }}</p>
            </div>
        </div>
    </div>
</template>
