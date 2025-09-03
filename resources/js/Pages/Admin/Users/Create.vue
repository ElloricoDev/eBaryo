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

  <div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
        <div class="flex flex-col md:flex-row md:items-center gap-6">
          <Link
            :href="route('admin.users.index')"
            class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-3 rounded-2xl transition-all duration-200"
          >
            <font-awesome-icon icon="arrow-left" /> Back to Users
          </Link>
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
              <font-awesome-icon icon="person-circle-plus" class="w-8 h-8 text-white" />
            </div>
            <div>
              <h1 class="text-3xl font-bold text-slate-800">Create New User</h1>
              <p class="text-slate-600 mt-1">Add a new user to the system</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
      <form @submit.prevent="submit" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="relative">
            <label class="block text-slate-700 font-semibold mb-2">First Name</label>
            <input v-model="form.first_name" type="text"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.first_name }" 
              placeholder="Enter first name" />
            <p v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">{{ form.errors.first_name }}</p>
          </div>

          <div class="relative">
            <label class="block text-slate-700 font-semibold mb-2">Middle Name</label>
            <input v-model="form.middle_name" type="text"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.middle_name }" 
              placeholder="Enter middle name" />
            <p v-if="form.errors.middle_name" class="text-red-500 text-sm mt-1">{{ form.errors.middle_name }}</p>
          </div>

          <div class="relative">
            <label class="block text-slate-700 font-semibold mb-2">Last Name</label>
            <input v-model="form.last_name" type="text"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.last_name }" 
              placeholder="Enter last name" />
            <p v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">{{ form.errors.last_name }}</p>
          </div>

          <div class="relative">
            <label class="block text-slate-700 font-semibold mb-2">User Name</label>
            <input v-model="form.user_name" type="text"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.user_name }" 
              placeholder="Enter username" />
            <p v-if="form.errors.user_name" class="text-red-500 text-sm mt-1">{{ form.errors.user_name }}</p>
          </div>

          <div class="relative">
            <label class="block text-slate-700 font-semibold mb-2">Contact Number</label>
            <input v-model="form.contact_number" type="text"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200"
              placeholder="Enter contact number" />
          </div>

          <div class="relative">
            <label class="block text-slate-700 font-semibold mb-2">Address</label>
            <input v-model="form.address" type="text"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200"
              placeholder="Enter address" />
          </div>

          <div class="md:col-span-2 relative">
            <label class="block text-slate-700 font-semibold mb-2">Email</label>
            <input v-model="form.email" type="email"
              class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200"
              :class="{ 'border-red-500 focus:ring-red-500/20 focus:border-red-500': form.errors.email }" 
              placeholder="Enter email address" />
            <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
          </div>
        </div>

        <input type="hidden" v-model="form.role" />

        <div class="pt-4">
          <button
            type="submit"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-6 rounded-2xl shadow-sm inline-flex items-center justify-center gap-2 transition-all duration-200"
            :disabled="form.processing"
          >
            <font-awesome-icon icon="person-circle-plus" />
            {{ form.processing ? 'Creating User...' : 'Create User' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
