<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCog } from '@fortawesome/free-solid-svg-icons'

library.add(faCog)

defineOptions({ layout: AdminLayout })
</script>

<template>
  <Head title="Admin Settings" />
  <div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2 mb-6">
      <font-awesome-icon icon="cog" /> Admin Settings
    </h1>

    <div
      class="border-2 border-green-500 rounded-xl shadow-md transition hover:border-green-700 hover:shadow-lg"
    >
      <div class="p-6">
        <p class="text-gray-500 m-0">Settings management coming soon...</p>
      </div>
    </div>
  </div>
</template>
