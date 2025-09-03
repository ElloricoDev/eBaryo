<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { usePage } from '@inertiajs/vue3'
import { Line, Bar, Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  Filler,
  BarElement,
  ArcElement
} from 'chart.js'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faTachometerAlt, faUsers, faBook, faTags, faChartLine, faUser, faComments } from '@fortawesome/free-solid-svg-icons'
import { library } from '@fortawesome/fontawesome-svg-core'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  Filler,
  BarElement,
  ArcElement
)

library.add(faTachometerAlt, faUsers, faBook, faTags, faChartLine, faUser, faComments)

defineOptions({ layout: AdminLayout })

const { props } = usePage()

const uploadsChartData = {
  labels: Array.isArray(props.ebookUploadsByMonth?.labels) ? props.ebookUploadsByMonth.labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  datasets: [
    {
      label: 'Ebook Uploads',
      data: Array.isArray(props.ebookUploadsByMonth?.data) ? props.ebookUploadsByMonth.data : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
      borderColor: '#16a34a',
      backgroundColor: 'rgba(22, 163, 74, 0.1)',
      tension: 0.4,
      fill: true,
    }
  ]
}

const uploadsChartOptions = {
  responsive: true,
  animation: false,
  plugins: {
    legend: { display: false },
    title: { display: true, text: 'Ebook Uploads Over Time (Last 12 Months)' }
  },
  scales: {
    y: { 
      beginAtZero: true,
      ticks: {
        stepSize: 1
      }
    },
    x: {
      ticks: {
        maxRotation: 45,
        minRotation: 0
      }
    }
  },
  interaction: {
    intersect: false,
    mode: 'index'
  }
}

// Recent Users Chart Data
const recentUsersChartData = {
  labels: props.recentUsers?.map(user => user.user_name) || [],
  datasets: [{
    label: 'Recent Signups',
    data: props.recentUsers?.map((user, index) => index + 1) || [],
    backgroundColor: 'rgba(22, 163, 74, 0.8)',
    borderColor: '#16a34a',
    borderWidth: 1
  }]
}

const recentUsersChartOptions = {
  responsive: true,
  animation: false,
  plugins: {
    legend: { display: false },
    title: { display: true, text: 'Recent User Signups' }
  },
  scales: {
    y: { beginAtZero: true, ticks: { stepSize: 1 } }
  }
}

// Recent Books Chart Data
const recentBooksChartData = {
  labels: props.recentBooks?.map(book => book.title.length > 20 ? book.title.substring(0, 20) + '...' : book.title) || [],
  datasets: [{
    label: 'Recently Added',
    data: props.recentBooks?.map((book, index) => index + 1) || [],
    backgroundColor: 'rgba(59, 130, 246, 0.8)',
    borderColor: '#3b82f6',
    borderWidth: 1
  }]
}

const recentBooksChartOptions = {
  responsive: true,
  animation: false,
  plugins: {
    legend: { display: false },
    title: { display: true, text: 'Recently Added Ebooks' }
  },
  scales: {
    y: { beginAtZero: true, ticks: { stepSize: 1 } }
  }
}

// Most Read Books Chart Data
const mostReadBooksChartData = {
  labels: props.mostReadBooks?.map(item => item.book?.title?.length > 15 ? item.book.title.substring(0, 15) + '...' : item.book?.title || 'Unknown') || [],
  datasets: [{
    label: 'Read Count',
    data: props.mostReadBooks?.map(item => item.read_count) || [],
    backgroundColor: 'rgba(168, 85, 247, 0.8)',
    borderColor: '#a855f7',
    borderWidth: 1
  }]
}

const mostReadBooksChartOptions = {
  responsive: true,
  animation: false,
  plugins: {
    legend: { display: false },
    title: { display: true, text: 'Most Read Ebooks' }
  },
  scales: {
    y: { beginAtZero: true }
  }
}

// Most Active Users Chart Data
const mostActiveUsersChartData = {
  labels: props.mostActiveUsers?.map(item => item.user?.user_name?.length > 15 ? item.user.user_name.substring(0, 15) + '...' : item.user?.user_name || 'Unknown') || [],
  datasets: [{
    label: 'Read Count',
    data: props.mostActiveUsers?.map(item => item.read_count) || [],
    backgroundColor: 'rgba(239, 68, 68, 0.8)',
    borderColor: '#ef4444',
    borderWidth: 1
  }]
}

const mostActiveUsersChartOptions = {
  responsive: true,
  animation: false,
  plugins: {
    legend: { display: false },
    title: { display: true, text: 'Most Active Users' }
  },
  scales: {
    y: { beginAtZero: true }
  }
}
</script>

<template>

  <Head title="Dashboard" />
  <div class="max-w-7xl mx-auto">
    <!-- Hero Header -->
    <div class="mb-8">
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
            <font-awesome-icon icon="tachometer-alt" class="w-8 h-8 text-white" />
          </div>
          <div>
            <h1 class="text-3xl font-bold text-slate-800">Admin Dashboard</h1>
            <p class="text-slate-600 mt-1">Welcome back! Here's what's happening with your library.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-sm">
            <font-awesome-icon icon="users" class="w-6 h-6 text-white" />
          </div>
          <div>
            <p class="text-slate-600 text-sm font-medium">Total Users</p>
            <p class="text-2xl font-bold text-slate-800">{{ props.totalUsers }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-sm">
            <font-awesome-icon icon="book" class="w-6 h-6 text-white" />
          </div>
          <div>
            <p class="text-slate-600 text-sm font-medium">Ebooks Available</p>
            <p class="text-2xl font-bold text-slate-800">{{ props.totalBooks }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-sm">
            <font-awesome-icon icon="book" class="w-6 h-6 text-white" />
          </div>
          <div>
            <p class="text-slate-600 text-sm font-medium">New This Month</p>
            <p class="text-2xl font-bold text-slate-800">{{ props.newBooksThisMonth }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-sm">
            <font-awesome-icon icon="tags" class="w-6 h-6 text-white" />
          </div>
          <div>
            <p class="text-slate-600 text-sm font-medium">Categories</p>
            <p class="text-2xl font-bold text-slate-800">{{ props.totalCategories }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-sm">
            <font-awesome-icon icon="user" class="w-6 h-6 text-white" />
          </div>
          <div>
            <p class="text-slate-600 text-sm font-medium">Active Readers (30d)</p>
            <p class="text-2xl font-bold text-slate-800">{{ props.activeReaders }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-sm">
            <font-awesome-icon icon="comments" class="w-6 h-6 text-white" />
          </div>
          <div>
            <p class="text-slate-600 text-sm font-medium">Total Feedbacks</p>
            <p class="text-2xl font-bold text-slate-800">{{ props.feedbacks }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
            <font-awesome-icon icon="chart-line" class="w-5 h-5 text-white" />
          </div>
          <h3 class="text-lg font-semibold text-slate-800">Recent User Signups</h3>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
          <Bar :data="recentUsersChartData" :options="recentUsersChartOptions" height="200" />
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
            <font-awesome-icon icon="book" class="w-5 h-5 text-white" />
          </div>
          <h3 class="text-lg font-semibold text-slate-800">Recently Added Ebooks</h3>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
          <Bar :data="recentBooksChartData" :options="recentBooksChartOptions" height="200" />
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
            <font-awesome-icon icon="chart-line" class="w-5 h-5 text-white" />
          </div>
          <h3 class="text-lg font-semibold text-slate-800">Most Read Ebooks</h3>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
          <Bar :data="mostReadBooksChartData" :options="mostReadBooksChartOptions" height="200" />
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
            <font-awesome-icon icon="user" class="w-5 h-5 text-white" />
          </div>
          <h3 class="text-lg font-semibold text-slate-800">Most Active Users</h3>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
          <Bar :data="mostActiveUsersChartData" :options="mostActiveUsersChartOptions" height="200" />
        </div>
      </div>
    </div>

    <!-- Main Chart -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
          <font-awesome-icon icon="chart-line" class="w-5 h-5 text-white" />
        </div>
        <h3 class="text-lg font-semibold text-slate-800">Ebook Uploads Over Time</h3>
      </div>
      <div class="bg-slate-50 rounded-xl p-4">
        <Line :data="uploadsChartData" :options="uploadsChartOptions" height="80" />
      </div>
    </div>
  </div>
</template>
