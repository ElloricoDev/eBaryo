<script setup>
const props = defineProps({
    rating: Number,
    max: { type: Number, default: 5 },
    highlight: Boolean,
});
</script>

<template>
    <span
        :class="[
            'flex items-center',
            highlight ? 'text-yellow-400' : 'text-yellow-500',
        ]"
    >
        <template v-for="i in max">
            <svg
                v-if="i <= Math.round(rating)"
                :key="'star-full-' + i"
                :class="highlight ? 'w-5 h-5' : 'w-4 h-4'"
                class="fill-current"
                viewBox="0 0 20 20"
            >
                <polygon
                    points="10,1 12.59,6.99 19,7.64 14,12.26 15.18,18.51 10,15.27 4.82,18.51 6,12.26 1,7.64 7.41,6.99"
                />
            </svg>
            <svg
                v-else
                :key="'star-empty-' + i"
                :class="highlight ? 'w-5 h-5' : 'w-4 h-4'"
                class="text-gray-300"
                viewBox="0 0 20 20"
            >
                <polygon
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1"
                    points="10,1 12.59,6.99 19,7.64 14,12.26 15.18,18.51 10,15.27 4.82,18.51 6,12.26 1,7.64 7.41,6.99"
                />
            </svg>
        </template>
    </span>
</template>
