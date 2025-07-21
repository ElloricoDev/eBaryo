<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from "sweetalert2"
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowLeft, faPerson, faPersonCirclePlus, faPhone, faMapMarkerAlt, faEnvelope } from '@fortawesome/free-solid-svg-icons'

library.add(faArrowLeft, faPerson, faPersonCirclePlus, faPhone, faMapMarkerAlt, faEnvelope)

defineOptions({ layout: AdminLayout })

const form = useForm({
  first_name: '',
  middle_name: '',
  last_name: '',
  contact_number: '',
  address: '',
  role: 'user', // default and hidden
  email: '',
  avatar: null,
  user_name: '',
})

const submit = () => {
  form.post(route('admin.users.store'), {
    forceFormData: true,
    onSuccess: () => {
      Swal.fire({
        icon: "success",
        title: "Success",
        text: "User created successfully!",
        timer: 2000,
        showConfirmButton: false,
      });
    }
  })
}
</script>

<template>
  <Head title="Create User" />

  <div class="max-w-6xl mx-auto px-4 py-10">
    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex flex-col md:flex-row md:items-center gap-4">
      <Link
        :href="route('admin.users.index')"
        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-full shadow-lg transition-transform duration-200 hover:scale-105"
      >
        <font-awesome-icon icon="arrow-left" /> Back
      </Link>
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="person-circle-plus" /> Create New User
      </h1>
    </div>

    <div class="bg-gradient-to-br from-white via-green-50 to-green-100 border-2 border-green-400 rounded-2xl shadow-2xl p-8">
      <form @submit.prevent="submit" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="relative">
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="person" /> First Name</label>
            <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="person" /></span>
            <input v-model="form.first_name" type="text"
              class="w-full border border-green-400 rounded-full pl-10 pr-4 py-2 shadow-sm focus:ring-2 focus:ring-green-300 focus:outline-none transition-all duration-150"
              :class="{ 'border-red-500': form.errors.first_name }" />
            <p v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">{{ form.errors.first_name }}</p>
          </div>

          <div class="relative">
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="person" /> Middle Name</label>
            <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="person" /></span>
            <input v-model="form.middle_name" type="text"
              class="w-full border border-green-400 rounded-full pl-10 pr-4 py-2 shadow-sm focus:ring-2 focus:ring-green-300 focus:outline-none transition-all duration-150"
              :class="{ 'border-red-500': form.errors.middle_name }" />
          </div>

          <div class="relative">
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="person" /> Last Name</label>
            <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="person" /></span>
            <input v-model="form.last_name" type="text"
              class="w-full border border-green-400 rounded-full pl-10 pr-4 py-2 shadow-sm focus:ring-2 focus:ring-green-300 focus:outline-none transition-all duration-150"
              :class="{ 'border-red-500': form.errors.last_name }" />
            <p v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">{{ form.errors.last_name }}</p>
          </div>

          <div class="relative">
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="person" /> User Name</label>
            <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="person" /></span>
            <input v-model="form.user_name" type="text"
              class="w-full border border-green-400 rounded-full pl-10 pr-4 py-2 shadow-sm focus:ring-2 focus:ring-green-300 focus:outline-none transition-all duration-150"
              :class="{ 'border-red-500': form.errors.user_name }" />
            <p v-if="form.errors.user_name" class="text-red-500 text-sm mt-1">{{ form.errors.user_name }}</p>
          </div>

          <div class="relative">
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="phone" /> Contact Number</label>
            <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="phone" /></span>
            <input v-model="form.contact_number" type="text"
              class="w-full border border-green-400 rounded-full pl-10 pr-4 py-2 shadow-sm focus:ring-2 focus:ring-green-300 focus:outline-none transition-all duration-150" />
          </div>

          <div class="relative">
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="map-marker-alt" /> Address</label>
            <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="map-marker-alt" /></span>
            <input v-model="form.address" type="text"
              class="w-full border border-green-400 rounded-full pl-10 pr-4 py-2 shadow-sm focus:ring-2 focus:ring-green-300 focus:outline-none transition-all duration-150" />
          </div>

          <div class="md:col-span-2 relative">
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="envelope" /> Email</label>
            <span class="absolute left-3 top-10 text-green-400"><font-awesome-icon icon="envelope" /></span>
            <input v-model="form.email" type="email"
              class="w-full border border-green-400 rounded-full pl-10 pr-4 py-2 shadow-sm focus:ring-2 focus:ring-green-300 focus:outline-none transition-all duration-150"
              :class="{ 'border-red-500': form.errors.email }" />
            <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
          </div>
        </div>

        <input type="hidden" v-model="form.role" />

        <div>
          <button
            type="submit"
            class="w-full bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-bold py-3 px-6 rounded-full shadow-xl transition-transform duration-200 inline-flex items-center justify-center gap-2 hover:scale-105"
            :disabled="form.processing"
          >
            <font-awesome-icon icon="person-circle-plus" />
            {{ form.processing ? 'Saving...' : 'Create User' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
