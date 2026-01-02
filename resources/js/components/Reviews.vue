<template>
    <div class="reviews-container max-w-7xl mx-auto bg-neutral-900 text-neutral-200 rounded-xl p-6">

        <!-- Overall Rating -->
        <div class="rating-summary text-center mb-6">
            <h1 class="text-5xl font-bold text-white">{{ overallRating.toFixed(1) }}</h1>
            <div class="stars text-2xl text-primary my-2">{{ stars }}</div>
            <p class="text-neutral-400">{{ totalRatings }} ratings</p>
        </div>

        <!-- Rating Bars -->
        <div class="rating-bars space-y-3 mb-6">
            <div v-for="(bar, index) in ratingBars" :key="index" class="flex items-center gap-3 text-sm">
                <span class="w-6 text-right">{{ bar.label }}</span>
                <div class="flex-1 h-2 bg-neutral-700 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full" :style="{ width: bar.width + '%' }"></div>
                </div>
                <small class="w-10 text-right text-neutral-400">{{ bar.count }}</small>
            </div>
        </div>

        <!-- Tags -->
        <div class="tags flex flex-wrap gap-2 mb-6">
            <span v-for="(tag, index) in tags" :key="index" class="px-3 py-1 rounded-full text-sm font-medium"
                :class="tag.isGreen ? 'bg-green-900 text-green-400' : 'bg-neutral-800 text-neutral-300'">
                {{ tag.text }}
            </span>
        </div>

        <!-- Review Cards -->
        <div class="space-y-6">
            <div v-for="(review, index) in reviews" :key="index" class="review-card border-t border-neutral-700 pt-4">
                <div class="review-header flex justify-between mb-2">
                    <strong class="text-white">{{ review.name }}</strong>
                    <span class="text-primary">{{ review.stars }}</span>
                </div>
                <p class="text-neutral-300">{{ review.comment }}</p>
                <div class="review-images flex gap-2 mt-3">
                    <img v-for="(img, i) in review.images" :key="i" :src="img"
                        class="w-16 h-12 rounded-md object-cover" />
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    overallRating: { type: Number, default: 4.0 },
    totalRatings: { type: String, default: "35k" },
    ratingBars: {
        type: Array,
        default: () => [
            { label: "5.0", width: 90, count: "14k" },
            { label: "4.0", width: 60, count: "6k" },
            { label: "3.0", width: 40, count: "4k" },
            { label: "2.0", width: 20, count: "800" },
            { label: "1.0", width: 50, count: "9k" },
        ],
    },
    tags: {
        type: Array,
        default: () => [
            { text: "4.0 Cleanliness", isGreen: true },
            { text: "4.0 Safety", isGreen: true },
            { text: "4.0 Staff", isGreen: true },
            { text: "3.5 Amenities", isGreen: false },
            { text: "3.0 Location", isGreen: false },
        ],
    },
    reviews: {
        type: Array,
        default: () => [
            {
                name: "Alexander Rity",
                stars: "★★★★★",
                comment:
                    "Easy booking, great value! Cozy rooms at a reasonable price. Highly recommended!",
                images: [
                    "https://picsum.photos/100/80?1",
                    "https://picsum.photos/100/80?2",
                    "https://picsum.photos/100/80?3",
                ],
            },
        ],
    },
});

const stars = computed(() => {
    const full = "★".repeat(Math.floor(props.overallRating));
    const empty = "☆".repeat(5 - Math.floor(props.overallRating));
    return full + empty;
});
</script>
