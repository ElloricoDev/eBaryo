<script setup>
import { ref, watch, onMounted, onBeforeUnmount, computed } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
const props = defineProps({
    show: Boolean,
    book: Object,
    reason: String,
    description: String,
    loading: Boolean,
});
const emit = defineEmits([
    "close",
    "submit",
    "update:reason",
    "update:description",
]);
const localReason = ref(props.reason || "");
const localDescription = ref(props.description || "");

watch(
    () => props.reason,
    (val) => {
        localReason.value = val;
    }
);
watch(
    () => props.description,
    (val) => {
        localDescription.value = val;
    }
);
watch(localReason, (val) => {
    emit("update:reason", val);
});
watch(localDescription, (val) => {
    emit("update:description", val);
});

watch(
    () => props.show,
    (val) => {
        if (val) {
            document.body.classList.add("overflow-hidden");
        } else {
            document.body.classList.remove("overflow-hidden");
        }
    }
);

// Fix isMobile reactivity
const isMobile = ref(typeof window !== 'undefined' ? window.innerWidth <= 640 : false);
const showDropdown = ref(false);
const dropdownRef = ref(null);

function handleOptionClick(option) {
    localReason.value = option;
    showDropdown.value = false;
}

function handleInputClick() {
    if (isMobile.value) showDropdown.value = !showDropdown.value;
}

function handleClickOutside(event) {
    if (
        showDropdown.value &&
        dropdownRef.value &&
        !dropdownRef.value.contains(event.target)
    ) {
        showDropdown.value = false;
    }
}

function handleKeydown(event) {
    if (!showDropdown.value) return;
    if (event.key === "Escape") {
        showDropdown.value = false;
    }
}

function closeAll() {
    showDropdown.value = false;
    emit("close");
}

function handleResize() {
    isMobile.value = window.innerWidth <= 640;
}

onMounted(() => {
    if (props.show) document.body.classList.add("overflow-hidden");
    window.addEventListener("resize", handleResize);
    document.addEventListener("mousedown", handleClickOutside);
    document.addEventListener("keydown", handleKeydown);
});
onBeforeUnmount(() => {
    document.body.classList.remove("overflow-hidden");
    window.removeEventListener("resize", handleResize);
    document.removeEventListener("mousedown", handleClickOutside);
    document.removeEventListener("keydown", handleKeydown);
});

const reasonOptions = [
    "Inappropriate content",
    "Copyright violation",
    "Poor quality",
    "Broken file",
    "Wrong category",
    "Spam",
    "Other",
];
</script>
<template>
    <teleport to="body">
        <div
            v-if="show"
            class="fixed inset-0 z-[9999] flex items-center justify-center"
        >
            <div
                class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm transition-all duration-300 pointer-events-auto"
            ></div>
            <div
                class="relative animate-fade-in bg-white bg-opacity-95 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden z-10 pointer-events-auto"
                role="dialog"
                aria-modal="true"
                :aria-label="`Report Book: ${book?.title || ''}`"
            >
                <div
                    class="absolute inset-0 -z-10 bg-gradient-to-br from-green-50 via-white to-green-100 animate-gradient-move"
                ></div>
                <div
                    class="bg-green-600 text-white px-4 py-3 rounded-t-2xl flex justify-between items-center"
                >
                    <h5 class="font-bold">
                        <font-awesome-icon icon="flag" /> Report Book
                    </h5>
                    <button
                        class="text-white focus:outline-none focus:ring-2 focus:ring-white rounded"
                        @click="closeAll"
                        aria-label="Close report modal"
                    >
                        &times;
                    </button>
                </div>
                <div class="border-t border-green-100"></div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 mb-4">
                        Provide a reason for reporting "<strong>{{
                            book.title
                        }}</strong>":
                    </p>
                    <label class="block text-green-700 font-semibold mb-1"
                        >Reason <span class="text-red-500">*</span></label
                    >
                    <div v-if="isMobile" class="relative mb-3">
                        <div
                            class="w-full border border-green-300 rounded shadow-sm px-3 py-2 bg-white focus:border-green-600 focus:ring-2 focus:ring-green-200 transition cursor-pointer"
                            @click="handleInputClick"
                            :aria-expanded="showDropdown"
                            :tabindex="0"
                        >
                            <span :class="{ 'text-gray-400': !localReason }">{{
                                localReason || "Select a reason"
                            }}</span>
                            <span class="float-right">▼</span>
                        </div>
                        <ul
                            v-if="showDropdown"
                            ref="dropdownRef"
                            class="absolute left-0 right-0 z-50 bg-white border border-green-300 rounded shadow-lg mt-1 max-h-60 overflow-auto"
                        >
                            <li
                                v-for="option in reasonOptions"
                                :key="option"
                                @click="handleOptionClick(option)"
                                class="px-4 py-2 hover:bg-green-50 cursor-pointer"
                                :class="{
                                    'bg-green-100 font-bold':
                                        localReason === option,
                                }"
                            >
                                {{ option }}
                            </li>
                        </ul>
                    </div>
                    <select
                        v-else
                        v-model="localReason"
                        class="w-full border border-green-300 rounded shadow-sm mb-3 focus:border-green-600 focus:ring-2 focus:ring-green-200 transition report-select"
                    >
                        <option value="" disabled selected hidden>
                            Select a reason
                        </option>
                        <option
                            v-for="option in reasonOptions"
                            :key="option"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                    <label class="block text-green-700 font-semibold mb-1"
                        >Additional Details</label
                    >
                    <textarea
                        v-model="localDescription"
                        rows="3"
                        class="w-full border border-green-300 rounded shadow-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition"
                        placeholder="Any additional info..."
                        maxlength="1000"
                    ></textarea>
                    <div class="text-sm text-gray-500 mt-1">
                        {{ localDescription.length }}/1000 characters
                    </div>
                </div>
                <div
                    class="px-4 py-3 border-t flex justify-end gap-2 bg-white bg-opacity-80"
                >
                    <button
                        class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-400"
                        @click="closeAll"
                    >
                        <font-awesome-icon icon="xmark-circle" /> Cancel
                    </button>
                    <button
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-400 disabled:opacity-50 flex items-center gap-2"
                        @click="$emit('submit')"
                        :disabled="loading || !localReason.trim()"
                    >
                        <font-awesome-icon
                            v-if="loading"
                            icon="hourglass-half"
                        />
                        <font-awesome-icon v-else icon="flag" />
                        <span v-if="loading">
                            <svg
                                class="inline w-4 h-4 animate-spin mr-1"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v8z"
                                ></path>
                            </svg>
                            Submitting...
                        </span>
                        <span v-else>Submit Report</span>
                    </button>
                </div>
            </div>
        </div>
    </teleport>
</template>
<style scoped>
.report-select {
    max-width: 100%;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}
@media (max-width: 640px) {
    .report-select {
        font-size: 1rem;
        min-width: 0;
        width: 100%;
    }
}
</style>
