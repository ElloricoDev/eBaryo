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

  <div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex items-center gap-4 mb-6">
      <Link
        :href="route('admin.users.index')"
        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-full shadow transition"
      >
        <font-awesome-icon icon="arrow-left" /> Back
      </Link>
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="person-circle-plus" /> Create New User
      </h1>
    </div>

    <div class="bg-white border-2 border-green-500 rounded-2xl shadow-lg p-6">
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="person" /> First Name</label>
            <input v-model="form.first_name" type="text"
              class="w-full border border-green-400 rounded-full px-4 py-2 shadow-sm focus:ring-green-300 focus:outline-none"
              :class="{ 'border-red-500': form.errors.first_name }" />
            <p v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">{{ form.errors.first_name }}</p>
          </div>

          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="person" /> Middle Name</label>
            <input v-model="form.middle_name" type="text"
              class="w-full border border-green-400 rounded-full px-4 py-2 shadow-sm focus:ring-green-300 focus:outline-none"
              :class="{ 'border-red-500': form.errors.middle_name }" />
          </div>

          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="person" /> Last Name</label>
            <input v-model="form.last_name" type="text"
              class="w-full border border-green-400 rounded-full px-4 py-2 shadow-sm focus:ring-green-300 focus:outline-none"
              :class="{ 'border-red-500': form.errors.last_name }" />
            <p v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">{{ form.errors.last_name }}</p>
          </div>

          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="person" /> User Name</label>
            <input v-model="form.user_name" type="text"
              class="w-full border border-green-400 rounded-full px-4 py-2 shadow-sm focus:ring-green-300 focus:outline-none"
              :class="{ 'border-red-500': form.errors.user_name }" />
            <p v-if="form.errors.user_name" class="text-red-500 text-sm mt-1">{{ form.errors.user_name }}</p>
          </div>

          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="phone" /> Contact Number</label>
            <input v-model="form.contact_number" type="text"
              class="w-full border border-green-400 rounded-full px-4 py-2 shadow-sm focus:ring-green-300 focus:outline-none" />
          </div>

          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="map-marker-alt" /> Address</label>
            <input v-model="form.address" type="text"
              class="w-full border border-green-400 rounded-full px-4 py-2 shadow-sm focus:ring-green-300 focus:outline-none" />
          </div>

          <div class="md:col-span-2">
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="envelope" /> Email</label>
            <input v-model="form.email" type="email"
              class="w-full border border-green-400 rounded-full px-4 py-2 shadow-sm focus:ring-green-300 focus:outline-none"
              :class="{ 'border-red-500': form.errors.email }" />
            <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
          </div>
        </div>

        <input type="hidden" v-model="form.role" />

        <div>
          <button
            type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-full shadow transition inline-flex items-center justify-center gap-2"
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
