<script setup>
import { useForm, router } from "@inertiajs/vue3";
import UserLayout from "@/Layouts/UserLayout.vue";

const props = defineProps({
    genres: Array,
    selected: Array,
});

const form = useForm({ genres: [...props.selected] });

function submit() {
    form.post(route("user.genres.store"));
}

function skip() {
    router.post("/user/genres/skip");
}
</script>

<template>
    <Head title="Select genre" />
    <UserLayout>
        <!-- Hero Header -->
        <div class="mb-12 bg-gradient-to-br from-purple-50 via-white to-pink-50 rounded-3xl p-8 border border-purple-100 shadow-sm">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4">
                    Choose Your Reading Preferences 📚
                </h1>
                <p class="text-xl text-slate-600 leading-relaxed">
                    Select your favorite genres to get personalized book recommendations
                </p>
            </div>
        </div>

        <!-- Genre Selection Form -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 hover:shadow-md transition-shadow duration-300">
                <form @submit.prevent="submit" class="space-y-8">
                    <!-- Genre Grid -->
                    <div class="space-y-4">
                        <h2 class="text-2xl font-bold text-slate-800 text-center mb-6">
                            Select Your Favorite Genres
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            <label
                                v-for="genre in genres"
                                :key="genre.id"
                                class="relative cursor-pointer group"
                            >
                                <input
                                    type="checkbox"
                                    :value="genre.id"
                                    v-model="form.genres"
                                    class="sr-only peer"
                                />
                                <div class="p-4 border-2 border-slate-200 rounded-2xl text-center transition-all duration-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:shadow-md hover:border-slate-300 group-hover:scale-105">
                                    <div class="text-sm font-medium text-slate-700 peer-checked:text-purple-700">
                                        {{ genre.name }}
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-6 pt-6 border-t border-slate-200">
                        <button
                            type="button"
                            @click="skip"
                            class="px-8 py-3 bg-slate-100 text-slate-700 rounded-2xl hover:bg-slate-200 transition-all duration-200 font-medium hover:shadow-md"
                        >
                            Skip for Now
                        </button>
                        
                        <div class="text-center sm:text-right">
                            <div class="text-sm text-slate-600 mb-2">
                                Selected: <span class="font-semibold text-purple-600">{{ form.genres.length }}</span> genres
                            </div>
                            <button
                                type="submit"
                                :disabled="form.genres.length === 0"
                                :class="[
                                    'px-8 py-3 rounded-2xl font-semibold transition-all duration-200',
                                    form.genres.length === 0
                                        ? 'bg-slate-300 text-slate-500 cursor-not-allowed'
                                        : 'bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:from-purple-700 hover:to-pink-700 hover:shadow-lg transform hover:scale-105'
                                ]"
                            >
                                Save Preferences
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </UserLayout>
</template>
