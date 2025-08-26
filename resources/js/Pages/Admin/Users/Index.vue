<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { usePage, router, Link, Head } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref } from "vue";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faPerson, faPlus, faEnvelope, faIdBadge, faCalendar, faEye, faPencilAlt, faTrash } from '@fortawesome/free-solid-svg-icons'

library.add(faPerson, faPlus, faEnvelope, faIdBadge, faCalendar, faEye, faPencilAlt, faTrash)

defineOptions({ layout: AdminLayout });

const { props } = usePage();
const usersList = ref([...props.users]);

const confirmDelete = (id) => {
  Swal.fire({
    title: "Are you sure?",
    text: "This action cannot be undone!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route("admin.users.destroy", id), {
        preserveScroll: true,
        onSuccess: () => {
          usersList.value = usersList.value.filter((user) => user.id !== id);
          Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "User has been deleted successfully.",
            timer: 2000,
            showConfirmButton: false,
          });
        },
      });
    }
  });
};
</script>

<template>
  <Head title="Users" />
  <div class="max-w-7xl mx-auto px-4">
    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-xl shadow mb-8 px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2">
        <font-awesome-icon icon="person" /> Users
      </h1>
      <Link
        :href="route('admin.users.create')"
        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-full shadow-lg text-base"
      >
        <font-awesome-icon icon="plus" /> Create User
      </Link>
    </div>

    <div class="bg-white border-2 border-green-500 rounded-2xl shadow-lg overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-green-100 text-green-800 sticky top-0 z-10">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-semibold">#</th>
            <th class="px-4 py-3 text-left text-sm font-semibold">
              <font-awesome-icon icon="person" /> Name
            </th>
            <th class="px-4 py-3 text-left text-sm font-semibold">
              <font-awesome-icon icon="envelope" /> Email
            </th>
            <th class="px-4 py-3 text-left text-sm font-semibold">
              <font-awesome-icon icon="id-badge" /> Role
            </th>
            <th class="px-4 py-3 text-left text-sm font-semibold">
              <font-awesome-icon icon="calendar" /> Registered At
            </th>
            <th class="px-4 py-3 text-left text-sm font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="(user, index) in usersList" :key="user.id" class="even:bg-green-50 hover:bg-green-100">
            <td class="px-4 py-3 font-semibold">{{ index + 1 }}</td>
            <td class="px-4 py-3">{{ user.user_name }}</td>
            <td class="px-4 py-3">{{ user.email }}</td>
            <td class="px-4 py-3">
              <span
                :class="[
                  'inline-block px-3 py-1 rounded-full text-xs font-bold',
                  user.role === 'admin' ? 'bg-green-200 text-green-800' :
                  user.role === 'user' ? 'bg-blue-100 text-blue-700' :
                  'bg-gray-200 text-gray-700'
                ]"
              >
                {{ user.role.charAt(0).toUpperCase() + user.role.slice(1) }}
              </span>
            </td>
            <td class="px-4 py-3">{{ new Date(user.created_at).toLocaleDateString() }}</td>
            <td class="px-4 py-3 space-x-1">
              <Link
                :href="route('admin.users.show', user.id)"
                class="inline-flex items-center gap-1 text-white bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded-full text-sm shadow"
              >
                <font-awesome-icon icon="eye" /> View
              </Link>
              <Link
                :href="route('admin.users.edit', user.id)"
                class="inline-flex items-center gap-1 text-white bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded-full text-sm shadow"
              >
                <font-awesome-icon icon="pencil-alt" /> Edit
              </Link>
              <button
                @click="confirmDelete(user.id)"
                class="inline-flex items-center gap-1 text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded-full text-sm shadow"
              >
                <font-awesome-icon icon="trash" /> Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
