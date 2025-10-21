<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { usePage, router, Link, Head } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref } from "vue";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faPerson, faPlus, faEnvelope, faIdBadge, faCalendar, faEye, faPencilAlt, faTrash, faCheckSquare, faSquare } from '@fortawesome/free-solid-svg-icons'

library.add(faPerson, faPlus, faEnvelope, faIdBadge, faCalendar, faEye, faPencilAlt, faTrash, faCheckSquare, faSquare)

defineOptions({ layout: AdminLayout });

const { props } = usePage();
const users = ref(props.users);
const selectedUsers = ref([]);
const selectAll = ref(false);

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
          if (users.value && users.value.data) {
            users.value.data = users.value.data.filter((user) => user.id !== id);
          }
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

const toggleSelectAll = () => {
  selectAll.value = !selectAll.value;

  if (selectAll.value) {
    selectedUsers.value = users.value.data?.map(user => user.id) || [];
  } else {
    selectedUsers.value = [];
  }
};


const toggleSelectUser = (userId) => {
  const index = selectedUsers.value.indexOf(userId);
  if (index > -1) {
    selectedUsers.value.splice(index, 1);
  } else {
    selectedUsers.value.push(userId);
  }
  selectAll.value = selectedUsers.value.length === (users.value.data?.length || 0);
};

const confirmBulkDelete = () => {
  if (selectedUsers.value.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "No Selection",
      text: "Please select users to delete.",
      timer: 2000,
      showConfirmButton: false,
    });
    return;
  }

  Swal.fire({
    title: "Are you sure?",
    text: `This will permanently delete ${selectedUsers.value.length} user(s). This action cannot be undone!`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete them!",
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route("admin.users.bulk-delete"), {
        user_ids: selectedUsers.value
      }, {
        preserveScroll: true,
        onSuccess: () => {
          selectedUsers.value = [];
          selectAll.value = false;
          Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "Selected users have been deleted successfully.",
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
  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
              <font-awesome-icon icon="person" class="w-8 h-8 text-white" />
            </div>
            <div>
              <h1 class="text-3xl font-bold text-slate-800">Users Management</h1>
              <p class="text-slate-600 mt-1">Manage user accounts and permissions</p>
            </div>
          </div>
          <div class="flex gap-3">
            <button v-if="selectedUsers.length > 0" @click="confirmBulkDelete"
              class="bg-red-500 text-white px-6 py-3 rounded-2xl shadow-sm hover:bg-red-600 flex items-center gap-2 text-base font-semibold transition-all duration-200">
              <font-awesome-icon icon="trash" /> Delete Selected ({{ selectedUsers.length }})
            </button>
            <Link :href="route('admin.users.create')"
              class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-2xl shadow-sm hover:from-blue-700 hover:to-blue-800 flex items-center gap-2 text-base font-semibold transition-all duration-200">
              <font-awesome-icon icon="plus" /> Create User
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-6 py-4 text-left">
              <button @click="toggleSelectAll" class="flex items-center gap-2 text-slate-600 hover:text-slate-800 font-medium">
                <font-awesome-icon :icon="selectAll ? 'check-square' : 'square'" class="w-4 h-4" />
                Select All
              </button>
            </th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">#</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Name</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Email</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Role</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Registered At</th>
            <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="(user, index) in (users?.data || [])" :key="user.id" class="hover:bg-slate-50 transition-colors">
            <td class="px-6 py-4">
              <button @click="toggleSelectUser(user.id)" class="flex items-center gap-2 text-slate-600 hover:text-slate-800">
                <font-awesome-icon :icon="selectedUsers.includes(user.id) ? 'check-square' : 'square'" class="w-4 h-4" />
              </button>
            </td>
            <td class="px-6 py-4 font-semibold text-slate-800">{{ (users?.from || 0) + index }}</td>
            <td class="px-6 py-4">
              <div class="font-medium text-slate-800">{{ user.user_name }}</div>
            </td>
            <td class="px-6 py-4 text-slate-600">{{ user.email }}</td>
            <td class="px-6 py-4">
              <span :class="[
                'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                user.role === 'admin' ? 'bg-green-100 text-green-700' :
                  user.role === 'user' ? 'bg-blue-100 text-blue-700' :
                    'bg-slate-100 text-slate-700'
              ]">
                {{ user.role.charAt(0).toUpperCase() + user.role.slice(1) }}
              </span>
            </td>
            <td class="px-6 py-4 text-slate-600">{{ new Date(user.created_at).toLocaleDateString() }}</td>
            <td class="px-6 py-4 text-right">
              <div class="flex items-center gap-2 justify-end">
                <Link :href="route('admin.users.show', user.id)"
                  class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white rounded-xl text-xs font-medium hover:bg-blue-600 transition-colors">
                  <font-awesome-icon icon="eye" class="w-3 h-3 mr-1" /> View
                </Link>
                <Link :href="route('admin.users.edit', user.id)"
                  class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white rounded-xl text-xs font-medium hover:bg-yellow-600 transition-colors">
                  <font-awesome-icon icon="pencil-alt" class="w-3 h-3 mr-1" /> Edit
                </Link>
                <button @click="confirmDelete(user.id)"
                  class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white rounded-xl text-xs font-medium hover:bg-red-600 transition-colors">
                  <font-awesome-icon icon="trash" class="w-3 h-3 mr-1" /> Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="users && users.links && users.links.length > 3" class="mt-8 flex items-center justify-between">
      <div class="text-sm text-slate-600">
        Showing {{ users.from || 0 }} to {{ users.to || 0 }} of {{ users.total || 0 }} results
      </div>
      <div class="flex space-x-2">
        <template v-for="link in users.links" :key="link.label">
          <Link
            v-if="link.url"
            :href="link.url"
            v-html="link.label"
            :class="[
              'px-4 py-2 text-sm border rounded-xl transition-all duration-200 font-medium',
              link.active
                ? 'text-white bg-gradient-to-r from-blue-600 to-blue-700 border-blue-600 shadow-sm'
                : 'text-slate-700 bg-white border-slate-200 hover:bg-slate-50 hover:border-slate-300'
            ]"
          />
          <span
            v-else
            v-html="link.label"
            class="px-4 py-2 text-sm border rounded-xl text-slate-400 bg-slate-100 border-slate-200 cursor-not-allowed"
          />
        </template>
      </div>
    </div>
  </div>
</template>
