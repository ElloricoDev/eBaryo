<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { usePage } from '@inertiajs/vue3'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  Filler
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
  Filler
)

library.add(faTachometerAlt, faUsers, faBook, faTags, faChartLine, faUser, faComments)

defineOptions({ layout: AdminLayout })

const { props } = usePage()

const uploadsChartData = {
  labels: Array.isArray(props.ebookUploadsByMonth?.labels) ? props.ebookUploadsByMonth.labels : [],
  datasets: [
    {
      label: 'Ebook Uploads',
      data: props.ebookUploadsByMonth?.data || [],
      borderColor: '#16a34a',
      backgroundColor: 'rgba(22, 163, 74, 0.1)',
      tension: 0.4,
      fill: true,
    }
  ]
}

const uploadsChartOptions = {
  responsive: true,
  plugins: {
    legend: { display: false },
    title: { display: true, text: 'Ebook Uploads Over Time' }
  },
  scales: {
    y: { beginAtZero: true }
  }
}
</script>

<template>
  <Head title="Dashboard" />
  <div class="p-6">
    <h1 class="text-2xl font-bold text-green-600 flex items-center gap-2 mb-6">
      <font-awesome-icon icon="tachometer-alt" class="w-5 h-5" />
      Admin Dashboard
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <div class="bg-white border-2 border-green-600 rounded-xl p-4 shadow-sm hover:shadow-md flex items-center gap-2">
        <font-awesome-icon icon="users" class="text-green-600 text-xl" />
        <div>
          <h5 class="text-green-600 font-semibold mb-1">Total Users</h5>
          <p class="text-2xl font-bold text-green-700">{{ props.totalUsers }}</p>
        </div>
      </div>
      <div class="bg-white border-2 border-green-600 rounded-xl p-4 shadow-sm hover:shadow-md flex items-center gap-2">
        <font-awesome-icon icon="book" class="text-green-600 text-xl" />
        <div>
          <h5 class="text-green-600 font-semibold mb-1">Ebooks Available</h5>
          <p class="text-2xl font-bold text-green-700">{{ props.totalBooks }}</p>
        </div>
      </div>
      <div class="bg-white border-2 border-green-600 rounded-xl p-4 shadow-sm hover:shadow-md flex items-center gap-2">
        <font-awesome-icon icon="book" class="text-blue-500 text-xl" />
        <div>
          <h5 class="text-green-600 font-semibold mb-1">New Ebooks This Month</h5>
          <p class="text-2xl font-bold text-blue-500">{{ props.newBooksThisMonth }}</p>
        </div>
      </div>
      <div class="bg-white border-2 border-green-600 rounded-xl p-4 shadow-sm hover:shadow-md flex items-center gap-2">
        <font-awesome-icon icon="tags" class="text-green-600 text-xl" />
        <div>
          <h5 class="text-green-600 font-semibold mb-1">Categories</h5>
          <p class="text-2xl font-bold text-gray-600">{{ props.totalCategories }}</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div class="bg-white border-2 border-green-600 rounded-xl p-4 shadow-sm hover:shadow-md flex items-center gap-2">
        <font-awesome-icon icon="user" class="text-green-600 text-xl" />
        <div>
          <h5 class="text-green-600 font-semibold mb-3">Active Readers (30d)</h5>
          <p class="text-2xl font-bold text-green-700">{{ props.activeReaders }}</p>
        </div>
      </div>
      <div class="bg-white border-2 border-green-600 rounded-xl p-4 shadow-sm hover:shadow-md flex items-center gap-2">
        <font-awesome-icon icon="comments" class="text-red-500 text-xl" />
        <div>
          <h5 class="text-green-600 font-semibold mb-3">Feedbacks</h5>
          <p class="text-2xl font-bold text-red-500">{{ props.feedbacks }}</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div class="bg-white border-2 border-green-600 rounded-xl p-4 shadow-sm">
        <h5 class="text-green-600 font-semibold mb-3 flex items-center gap-2"><font-awesome-icon icon="chart-line" /> Recent User Signups</h5>
        <table class="w-full text-sm">
          <thead class="bg-green-100 text-left">
            <tr>
              <th class="p-2">Name</th>
              <th class="p-2">Email</th>
              <th class="p-2">Joined</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in props.recentUsers" :key="user.id" class="border-b">
              <td class="p-2">{{ user.user_name }}</td>
              <td class="p-2">{{ user.email }}</td>
              <td class="p-2">{{ new Date(user.created_at).toLocaleDateString() }}</td>
            </tr>
            <tr v-if="!props.recentUsers.length">
              <td colspan="3" class="p-2 text-center text-gray-400">No recent users.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-white border-2 border-green-600 rounded-xl p-4 shadow-sm">
        <h5 class="text-green-600 font-semibold mb-3 flex items-center gap-2"><font-awesome-icon icon="book" /> Recently Added Ebooks</h5>
        <table class="w-full text-sm">
          <thead class="bg-green-100 text-left">
            <tr>
              <th class="p-2">Title</th>
              <th class="p-2">Author</th>
              <th class="p-2">Added</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="book in props.recentBooks" :key="book.id" class="border-b">
              <td class="p-2">{{ book.title }}</td>
              <td class="p-2">{{ book.author }}</td>
              <td class="p-2">{{ new Date(book.created_at).toLocaleDateString() }}</td>
            </tr>
            <tr v-if="!props.recentBooks.length">
              <td colspan="3" class="p-2 text-center text-gray-400">No recent books.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div class="bg-white border-2 border-green-600 rounded-xl p-4 shadow-sm">
        <h5 class="text-green-600 font-semibold mb-3 flex items-center gap-2"><font-awesome-icon icon="chart-line" /> Most Read Ebooks</h5>
        <table class="w-full text-sm">
          <thead class="bg-green-100 text-left">
            <tr>
              <th class="p-2">Title</th>
              <th class="p-2">Reads</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in props.mostReadBooks" :key="item.book_id" class="border-b">
              <td class="p-2">{{ item.book?.title || 'Unknown' }}</td>
              <td class="p-2">{{ item.read_count }}</td>
            </tr>
            <tr v-if="!props.mostReadBooks.length">
              <td colspan="2" class="p-2 text-center text-gray-400">No data.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-white border-2 border-green-600 rounded-xl p-4 shadow-sm">
        <h5 class="text-green-600 font-semibold mb-3 flex items-center gap-2"><font-awesome-icon icon="user" /> Most Active Users</h5>
        <table class="w-full text-sm">
          <thead class="bg-green-100 text-left">
            <tr>
              <th class="p-2">Name</th>
              <th class="p-2">Reads</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in props.mostActiveUsers" :key="item.user_id" class="border-b">
              <td class="p-2">{{ item.user?.user_name || 'Unknown' }}</td>
              <td class="p-2">{{ item.read_count }}</td>
            </tr>
            <tr v-if="!props.mostActiveUsers.length">
              <td colspan="2" class="p-2 text-center text-gray-400">No data.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="bg-white border-2 border-green-600 rounded-xl p-4 shadow-sm">
      <h5 class="text-green-600 font-semibold mb-3 flex items-center gap-2"><font-awesome-icon icon="chart-line" /> Ebook Uploads Over Time</h5>
      <Line :data="uploadsChartData" :options="uploadsChartOptions" height="80" />
    </div>
  </div>
</template>
