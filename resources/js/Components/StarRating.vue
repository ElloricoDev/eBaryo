<script setup>
const props = defineProps({
    rating: {
        type: Number,
        default: 0
    },
    max: { 
        type: Number, 
        default: 5 
    },
    size: {
        type: String,
        default: 'md', // sm, md, lg, xl
        validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    interactive: {
        type: Boolean,
        default: false
    },
    showRating: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:rating']);

const sizeClasses = {
    sm: 'w-3 h-3',
    md: 'w-4 h-4',
    lg: 'w-5 h-5',
    xl: 'w-6 h-6'
};

const handleStarClick = (starIndex) => {
    if (props.interactive) {
        emit('update:rating', starIndex);
    }
};
</script>

<template>
    <div class="flex items-center gap-1">
        <span
            :class="[
                'flex items-center gap-0.5',
                interactive ? 'cursor-pointer' : ''
            ]"
        >
            <template v-for="i in max" :key="i">
                <svg
                    v-if="i <= Math.round(rating)"
                    :class="[
                        sizeClasses[size],
                        'fill-current text-yellow-500 transition-colors duration-200',
                        interactive ? 'hover:text-yellow-600' : ''
                    ]"
                    @click="handleStarClick(i)"
                    viewBox="0 0 20 20"
                >
                    <polygon
                        points="10,1 12.59,6.99 19,7.64 14,12.26 15.18,18.51 10,15.27 4.82,18.51 6,12.26 1,7.64 7.41,6.99"
                    />
                </svg>
                <svg
                    v-else
                    :class="[
                        sizeClasses[size],
                        'text-slate-300 transition-colors duration-200',
                        interactive ? 'hover:text-yellow-400 cursor-pointer' : ''
                    ]"
                    @click="handleStarClick(i)"
                    viewBox="0 0 20 20"
                >
                    <polygon
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        points="10,1 12.59,6.99 19,7.64 14,12.26 15.18,18.51 10,15.27 4.82,18.51 6,12.26 1,7.64 7.41,6.99"
                    />
                </svg>
            </template>
        </span>
        
        <!-- Rating Text -->
        <span 
            v-if="showRating" 
            class="ml-2 text-sm font-medium text-slate-600"
        >
            {{ rating.toFixed(1) }}/{{ max }}
        </span>
    </div>
</template>
