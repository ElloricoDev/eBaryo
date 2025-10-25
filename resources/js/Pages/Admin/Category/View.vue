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
    <div class=" mx-auto px-4 ">
        <!-- Hero Header -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center">
                        <font-awesome-icon icon="tags" class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800">Category Details</h1>
                        <p class="text-slate-600 mt-1">View category information and details</p>
                    </div>
                </div>
                <Link
                    :href="route('admin.categories.index')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-colors"
                >
                    <font-awesome-icon icon="arrow-left" class="w-4 h-4" /> Back to Categories
                </Link>
            </div>
        </div>

        <!-- Category Details Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <div class="space-y-6">
                <!-- Category Name -->
                <div>
                    <label class="block text-slate-700 font-semibold mb-2">Category Name</label>
                    <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-xl font-semibold">
                        <font-awesome-icon icon="align-left" class="w-4 h-4 mr-2" />
                        {{ category.name }}
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-slate-700 font-semibold mb-2">Description</label>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                        <p class="text-slate-700 leading-relaxed">{{ category.description || 'No description provided' }}</p>
                    </div>
                </div>

                <!-- Category ID -->
                <div>
                    <label class="block text-slate-700 font-semibold mb-2">Category ID</label>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2">
                        <span class="text-slate-600 font-mono">{{ category.id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
