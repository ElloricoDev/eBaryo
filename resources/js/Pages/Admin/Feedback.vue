<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faComments, faUser, faInfoCircle, faClock, faReply, faGear, faHourglassHalf, faCheckCircle, faSave, faCheckSquare, faSquare, faTrash
} from '@fortawesome/free-solid-svg-icons'

library.add(faComments, faUser, faInfoCircle, faClock, faReply, faGear, faHourglassHalf, faCheckCircle, faSave, faCheckSquare, faSquare, faTrash)

defineOptions({ layout: AdminLayout });

const props = defineProps({ feedbacks: Array });

const responseInputs = ref({});
const responseErrors = ref({});
const selectedFeedbacks = ref([]);
const selectAll = ref(false);

function saveResponse(id) {
  const response = responseInputs.value[id]?.trim();

  if (!response) {
    responseErrors.value[id] = "Response is required.";
    return;
  }

  responseErrors.value[id] = ""; // clear error

  router.put(
    route("admin.feedback.update", id),
    { response },
    {
      onSuccess: () => {
        Swal.fire({
          icon: "success",
          title: "Response saved!",
          timer: 1500,
          showConfirmButton: false,
        });
        responseInputs.value[id] = "";
      },
    }
  );
}

function markResponded(id) {
  router.put(route("admin.feedback.respond", id));
}

function toggleApprove(id) {
  router.patch(route("admin.feedback.toggle-approve", id));
}

function updateType(id, type) {
  router.patch(route("admin.feedback.update-type", id), { type });
}



const toggleSelectAll = () => {
  selectAll.value = !selectAll.value;

  if (selectAll.value) {
    selectedFeedbacks.value = props.feedbacks.map(fb => fb.id);
  } else {
    selectedFeedbacks.value = [];
  }
};


const toggleSelectFeedback = (feedbackId) => {
  const index = selectedFeedbacks.value.indexOf(feedbackId);
  if (index > -1) {
    selectedFeedbacks.value.splice(index, 1);
  } else {
    selectedFeedbacks.value.push(feedbackId);
  }
  selectAll.value = selectedFeedbacks.value.length === (props.feedbacks?.length || 0);
};

const confirmBulkDelete = () => {
  if (selectedFeedbacks.value.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "No Selection",
      text: "Please select feedback to delete.",
      timer: 2000,
      showConfirmButton: false,
    });
    return;
  }

  Swal.fire({
    title: "Are you sure?",
    text: `This will permanently delete ${selectedFeedbacks.value.length} feedback(s). This action cannot be undone!`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete them!",
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route("admin.feedback.bulk-delete"), {
        feedback_ids: selectedFeedbacks.value
      }, {
        preserveScroll: true,
        onSuccess: () => {
          selectedFeedbacks.value = [];
          selectAll.value = false;
          Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "Selected feedback has been deleted successfully.",
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
  <Head title="Feedback Management" />
  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg">
              <font-awesome-icon icon="comments" class="w-8 h-8 text-white" />
            </div>
            <div>
              <h1 class="text-3xl font-bold text-slate-800">Feedback Management</h1>
              <p class="text-slate-600 mt-1">Review and respond to user feedback</p>
            </div>
          </div>
          <button v-if="selectedFeedbacks.length > 0" @click="confirmBulkDelete"
            class="bg-red-500 text-white px-6 py-3 rounded-2xl shadow-sm hover:bg-red-600 flex items-center gap-2 text-base font-semibold transition-all duration-200">
            <font-awesome-icon icon="trash" /> Delete Selected ({{ selectedFeedbacks.length }})
          </button>
        </div>
      </div>
    </div>

    <!-- Feedback Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-6 py-4 text-left">
                <button @click="toggleSelectAll" class="flex items-center gap-2 text-slate-600 hover:text-slate-800 font-medium">
                  <font-awesome-icon :icon="selectAll ? 'check-square' : 'square'" class="w-4 h-4" />
                  Select All
                </button>
              </th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">User</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Type</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Message</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Status</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Responded At</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Admin Response</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="fb in props.feedbacks"
              :key="fb.id"
              class="hover:bg-slate-50 transition-colors"
            >
              <td class="px-6 py-4">
                <button @click="toggleSelectFeedback(fb.id)" class="flex items-center gap-2 text-slate-600 hover:text-slate-800">
                  <font-awesome-icon :icon="selectedFeedbacks.includes(fb.id) ? 'check-square' : 'square'" class="w-4 h-4" />
                </button>
              </td>
              <td class="px-6 py-4">
                <div class="font-medium text-slate-800">{{ fb.user?.user_name || "Unknown" }}</div>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                  {{ (fb.type || 'general').replace('_', ' ') }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-600 max-w-xs">{{ fb.message }}</td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                    fb.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'
                  ]"
                >
                  <font-awesome-icon
                    :icon="fb.status === 'pending' ? 'hourglass-half' : 'check-circle'"
                    class="w-3 h-3 mr-1"
                  />
                  {{ fb.status.charAt(0).toUpperCase() + fb.status.slice(1) }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-600">
                {{
                  fb.responded_at
                    ? new Date(fb.responded_at).toLocaleString()
                    : "-"
                }}
              </td>
              <td class="px-6 py-4">
                <div v-if="fb.status === 'pending'">
                  <textarea
                    v-model="responseInputs[fb.id]"
                    rows="2"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 mb-2 transition-all duration-200"
                    placeholder="Write a response..."
                  ></textarea>

                  <div v-if="responseErrors[fb.id]" class="text-red-500 text-sm mb-2">
                    {{ responseErrors[fb.id] }}
                  </div>

                  <button
                    @click="saveResponse(fb.id)"
                    :disabled="!responseInputs[fb.id]?.trim()"
                    class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm px-4 py-2 rounded-xl shadow-sm disabled:opacity-50 flex items-center gap-1 font-semibold transition-all duration-200"
                  >
                    <font-awesome-icon icon="save" /> Save Response
                  </button>
                </div>
                <div v-else>
                  <span v-if="fb.response" class="text-slate-600">
                    <font-awesome-icon icon="reply" class="w-3 h-3 mr-1" /> {{ fb.response }}
                  </span>
                  <span v-else class="text-slate-400">No response</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2 flex-wrap">
                  <button
                    v-if="fb.status === 'pending'"
                    @click="markResponded(fb.id)"
                    class="border border-green-500 text-green-600 hover:bg-green-500 hover:text-white px-3 py-1.5 text-sm rounded-xl transition-all duration-200 flex items-center gap-1 font-medium"
                  >
                    <font-awesome-icon icon="check-circle" class="w-3 h-3" /> Mark Responded
                  </button>
                  <button
                    @click="toggleApprove(fb.id)"
                    :class="[
                      'px-3 py-1.5 text-sm rounded-xl transition-all duration-200 flex items-center gap-1 font-medium',
                      fb.approved ? 'border border-yellow-500 text-yellow-600 hover:bg-yellow-500 hover:text-white' : 'border border-slate-300 text-slate-600 hover:bg-slate-500 hover:text-white'
                    ]"
                  >
                    {{ fb.approved ? 'Unfeature' : 'Feature as Testimonial' }}
                  </button>
                  <select
                    class="bg-slate-50 border border-slate-200 rounded-xl text-sm px-3 py-1.5 text-slate-700 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all duration-200"
                    :value="fb.type || 'general'"
                    @change="updateType(fb.id, $event.target.value)"
                  >
                    <option value="general">General</option>
                    <option value="testimonial">Testimonial</option>
                    <option value="bug_report">Bug Report</option>
                    <option value="feature_request">Feature Request</option>
                    <option value="report">Report</option>
                  </select>
                </div>
              </td>
            </tr>

            <tr v-if="!props.feedbacks.length">
              <td colspan="8" class="text-center text-slate-500 py-12">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
                    <font-awesome-icon icon="comments" class="w-6 h-6 text-slate-400" />
                  </div>
                  <p class="text-slate-600 font-medium">No feedback found</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
