<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faComments, faUser, faInfoCircle, faClock, faReply, faGear, faHourglassHalf, faCheckCircle, faSave
} from '@fortawesome/free-solid-svg-icons'

library.add(faComments, faUser, faInfoCircle, faClock, faReply, faGear, faHourglassHalf, faCheckCircle, faSave)

defineOptions({ layout: AdminLayout });

const props = defineProps({ feedbacks: Array });

const responseInputs = ref({});
const responseErrors = ref({});

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
</script>

<template>
  <Head title="Feedback Management" />
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-green-700 flex items-center gap-2 mb-6">
      <font-awesome-icon icon="comments" /> Feedback Management
    </h1>

    <div class="bg-white border-2 border-green-500 rounded-xl shadow-md hover:border-green-700 transition">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-green-100 text-green-700">
            <tr>
              <th class="text-left px-4 py-3"><font-awesome-icon icon="user" /> User</th>
              <th class="text-left px-4 py-3"><font-awesome-icon icon="comments" /> Message</th>
              <th class="text-left px-4 py-3"><font-awesome-icon icon="info-circle" /> Status</th>
              <th class="text-left px-4 py-3"><font-awesome-icon icon="clock" /> Responded At</th>
              <th class="text-left px-4 py-3"><font-awesome-icon icon="reply" /> Admin Response</th>
              <th class="text-left px-4 py-3"><font-awesome-icon icon="gear" /> Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="fb in props.feedbacks"
              :key="fb.id"
              class="border-b hover:bg-gray-50 transition"
            >
              <td class="px-4 py-3">{{ fb.user?.user_name || "Unknown" }}</td>
              <td class="px-4 py-3">{{ fb.message }}</td>
              <td class="px-4 py-3">
                <span
                  :class="fb.status === 'pending'
                      ? 'text-yellow-600 font-semibold'
                      : 'text-green-600 font-semibold'"
                >
                  <font-awesome-icon
                    :icon="fb.status === 'pending' ? 'hourglass-half' : 'check-circle'"
                    class="mr-1"
                  />
                  {{ fb.status.charAt(0).toUpperCase() + fb.status.slice(1) }}
                </span>
              </td>
              <td class="px-4 py-3">
                {{
                  fb.responded_at
                    ? new Date(fb.responded_at).toLocaleString()
                    : "-"
                }}
              </td>
              <td class="px-4 py-3">
                <div v-if="fb.status === 'pending'">
                  <textarea
                    v-model="responseInputs[fb.id]"
                    rows="2"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500 mb-2"
                    placeholder="Write a response..."
                  ></textarea>

                  <div v-if="responseErrors[fb.id]" class="text-red-500 text-sm mb-2">
                    {{ responseErrors[fb.id] }}
                  </div>

                  <button
                    @click="saveResponse(fb.id)"
                    :disabled="!responseInputs[fb.id]?.trim()"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1 rounded shadow-sm transition disabled:opacity-50 flex items-center gap-1"
                  >
                    <font-awesome-icon icon="save" /> Save Response
                  </button>
                </div>
                <div v-else>
                  <span v-if="fb.response">
                    <font-awesome-icon icon="reply" /> {{ fb.response }}
                  </span>
                  <span v-else class="text-gray-400">No response</span>
                </div>
              </td>
              <td class="px-4 py-3">
                <button
                  v-if="fb.status === 'pending'"
                  @click="markResponded(fb.id)"
                  class="border border-green-600 text-green-600 hover:bg-green-600 hover:text-white px-3 py-1 text-sm rounded shadow-sm transition flex items-center gap-1"
                >
                  <font-awesome-icon icon="check-circle" /> Mark Responded
                </button>
                <span v-else class="text-gray-400">—</span>
              </td>
            </tr>

            <tr v-if="!props.feedbacks.length">
              <td colspan="6" class="text-center text-gray-400 py-6">
                No feedbacks found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
