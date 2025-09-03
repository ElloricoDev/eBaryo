<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCog, faUsers, faBook, faServer, faInfoCircle } from '@fortawesome/free-solid-svg-icons'

library.add(faCog, faUsers, faBook, faServer, faInfoCircle)

defineOptions({ layout: AdminLayout })
</script>

<template>
  <Head title="Admin Settings" />
  
  <div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Hero Header -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mb-8">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-gradient-to-br from-slate-500 to-slate-600 rounded-2xl flex items-center justify-center">
          <font-awesome-icon icon="cog" class="w-8 h-8 text-white" />
        </div>
        <div>
          <h1 class="text-3xl font-bold text-slate-800">Admin Settings</h1>
          <p class="text-slate-600 mt-1">Configure system settings and application preferences</p>
        </div>
      </div>
    </div>

    <!-- Settings Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- General Settings -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
            <font-awesome-icon icon="cog" class="w-5 h-5 text-blue-600" />
          </div>
          <div>
            <h3 class="text-lg font-semibold text-slate-800">General Settings</h3>
            <p class="text-slate-600 text-sm">Basic application configuration</p>
          </div>
        </div>
        
        <div class="space-y-4">
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">Application Name</h4>
            <p class="text-slate-600 text-sm">Configure the application display name</p>
          </div>
          
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">Maintenance Mode</h4>
            <p class="text-slate-600 text-sm">Enable or disable maintenance mode</p>
          </div>
          
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">Default Language</h4>
            <p class="text-slate-600 text-sm">Set the default application language</p>
          </div>
        </div>
      </div>

      <!-- User Management Settings -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
            <font-awesome-icon icon="users" class="w-5 h-5 text-green-600" />
          </div>
          <div>
            <h3 class="text-lg font-semibold text-slate-800">User Management</h3>
            <p class="text-slate-600 text-sm">User registration and access settings</p>
          </div>
        </div>
        
        <div class="space-y-4">
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">Registration</h4>
            <p class="text-slate-600 text-sm">Allow or restrict new user registrations</p>
          </div>
          
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">Email Verification</h4>
            <p class="text-slate-600 text-sm">Require email verification for new users</p>
          </div>
          
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">Password Policy</h4>
            <p class="text-slate-600 text-sm">Configure password requirements</p>
          </div>
        </div>
      </div>

      <!-- Content Settings -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
            <font-awesome-icon icon="book" class="w-5 h-5 text-orange-600" />
          </div>
          <div>
            <h3 class="text-lg font-semibold text-slate-800">Content Settings</h3>
            <p class="text-slate-600 text-sm">Book and content management options</p>
          </div>
        </div>
        
        <div class="space-y-4">
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">File Upload Limits</h4>
            <p class="text-slate-600 text-sm">Configure maximum file sizes for uploads</p>
          </div>
          
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">Content Moderation</h4>
            <p class="text-slate-600 text-sm">Enable automatic content filtering</p>
          </div>
          
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">Categories</h4>
            <p class="text-slate-600 text-sm">Manage book categories and genres</p>
          </div>
        </div>
      </div>

      <!-- System Settings -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
            <font-awesome-icon icon="server" class="w-5 h-5 text-red-600" />
          </div>
          <div>
            <h3 class="text-lg font-semibold text-slate-800">System Settings</h3>
            <p class="text-slate-600 text-sm">Server and performance configuration</p>
          </div>
        </div>
        
        <div class="space-y-4">
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">Cache Management</h4>
            <p class="text-slate-600 text-sm">Clear and manage application cache</p>
          </div>
          
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">Backup Settings</h4>
            <p class="text-slate-600 text-sm">Configure automatic backups</p>
          </div>
          
          <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="font-semibold text-slate-700 mb-2">Log Management</h4>
            <p class="text-slate-600 text-sm">View and manage system logs</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Coming Soon Notice -->
    <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-8">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
          <font-awesome-icon icon="info-circle" class="w-6 h-6 text-blue-600" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-blue-800">Settings Management Coming Soon</h3>
          <p class="text-blue-700 mt-1">
            We're working on implementing comprehensive settings management. 
            This will include all the configuration options shown above and more.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
