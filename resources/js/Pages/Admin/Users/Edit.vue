<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from "sweetalert2";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowLeft, faUser, faUserEdit, faPhone, faMapMarkerAlt, faEnvelope } from '@fortawesome/free-solid-svg-icons'

library.add(faArrowLeft, faUser, faUserEdit, faPhone, faMapMarkerAlt, faEnvelope)

defineOptions({ layout: AdminLayout })

const props = defineProps({
  user: Object
})

const form = useForm({
  user_name: props.user.user_name || '',
  first_name: props.user.first_name || '',
  middle_name: props.user.middle_name || '',
  last_name: props.user.last_name || '',
  contact_number: props.user.contact_number || '',
  address: props.user.address || '',
  role: props.user.role || 'user',
  email: props.user.email || '',
})

const submit = () => {
  form.put(route('admin.users.update', { id: props.user.id }), {
    onSuccess: () => {
      Swal.fire({
        icon: "success",
        title: "Success",
        text: "User updated successfully!",
        timer: 2000,
        showConfirmButton: false,
      });
    }
  });
}
</script>

<template>
  <Head title="Edit User" />

  <div class="max-w-6xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
      <Link
        :href="route('admin.users.index')"
        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-full shadow"
      >
        <font-awesome-icon icon="arrow-left" /> Back
      </Link>
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="user-edit" /> Edit User
      </h1>
    </div>

    <!-- Form Card -->
    <div class="bg-white border-2 border-green-500 shadow-md rounded-2xl p-6">
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid md:grid-cols-3 gap-4">
          <!-- First Name -->
          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="user" /> First Name</label>
            <input
              v-model="form.first_name"
              type="text"
              class="w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:ring focus:ring-green-200"
              :class="{ 'border-red-500': form.errors.first_name }"
            />
            <p v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">{{ form.errors.first_name }}</p>
          </div>

          <!-- Middle Name -->
          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="user" /> Middle Name</label>
            <input
              v-model="form.middle_name"
              type="text"
              class="w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:ring focus:ring-green-200"
              :class="{ 'border-red-500': form.errors.middle_name }"
            />
            <p v-if="form.errors.middle_name" class="text-red-500 text-sm mt-1">{{ form.errors.middle_name }}</p>
          </div>

          <!-- Last Name -->
          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="user" /> Last Name</label>
            <input
              v-model="form.last_name"
              type="text"
              class="w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:ring focus:ring-green-200"
              :class="{ 'border-red-500': form.errors.last_name }"
            />
            <p v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">{{ form.errors.last_name }}</p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <!-- User Name -->
          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="user" /> User Name</label>
            <input
              v-model="form.user_name"
              type="text"
              class="w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:ring focus:ring-green-200"
              :class="{ 'border-red-500': form.errors.user_name }"
            />
            <p v-if="form.errors.user_name" class="text-red-500 text-sm mt-1">{{ form.errors.user_name }}</p>
          </div>

          <!-- Contact Number -->
          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="phone" /> Contact Number</label>
            <input
              v-model="form.contact_number"
              type="text"
              class="w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:ring focus:ring-green-200"
            />
          </div>

          <!-- Address -->
          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="map-marker-alt" /> Address</label>
            <input
              v-model="form.address"
              type="text"
              class="w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:ring focus:ring-green-200"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-green-700 font-semibold mb-1"><font-awesome-icon icon="envelope" /> Email</label>
            <input
              v-model="form.email"
              type="email"
              class="w-full border border-green-500 rounded-full px-4 py-2 shadow-sm focus:ring focus:ring-green-200"
              :class="{ 'border-red-500': form.errors.email }"
            />
            <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
          </div>
        </div>

        <!-- Hidden Role -->
        <input type="hidden" v-model="form.role" />

        <!-- Submit Button -->
        <div class="pt-4">
          <button
            type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-full shadow transition inline-flex items-center gap-2 justify-center"
            :disabled="form.processing"
          >
            <font-awesome-icon icon="user-edit" /> {{ form.processing ? 'Saving...' : 'Update User' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
