<template>
    <section class="mt-16 space-y-6">
        <h3 class="text-xl font-bold">Customer Reviews</h3>

        <!-- Submit Review -->
        <form @submit.prevent="submitReview" class="flex flex-col gap-4 border p-4 rounded-lg">
            <textarea v-model="reviewText" placeholder="Write your review..." class="border rounded-lg p-3"></textarea>
            <div class="flex gap-4 items-center">
                <label class="flex items-center gap-2">
                    Rating:
                    <select v-model="rating" class="border rounded-lg p-1">
                        <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
                    </select>
                </label>
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg font-bold">Submit</button>
            </div>
        </form>

        <!-- List Reviews -->
        <div class="space-y-4">
            <div v-for="r in reviews" :key="r.id" class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-bold">{{ r.user_name }}</span>
                    <span class="text-yellow-400">{{ '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating) }}</span>
                </div>
                <p class="text-gray-700">{{ r.review }}</p>
                <span class="text-gray-400 text-sm">{{ new Date(r.created_at).toLocaleDateString() }}</span>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps<{ productId: number }>()

const reviews = ref<any[]>([])
const reviewText = ref('')
const rating = ref(5)

// Fetch reviews
const fetchReviews = async () => {
    try {
        const res = await axios.get(`/api/products/${props.productId}/reviews`)
        reviews.value = res.data
    } catch (err) {
        console.error(err)
    }
}

// Submit review
const submitReview = async () => {
    if (!reviewText.value) return alert('Review cannot be empty')
    try {
        const res = await axios.post(`/api/products/${props.productId}/reviews`, {
            review: reviewText.value,
            rating: rating.value,
        })
        reviews.value.unshift(res.data) // add new review to top
        reviewText.value = ''
        rating.value = 5
    } catch (err) {
        console.error(err)
        alert('Failed to submit review')
    }
}

onMounted(fetchReviews)
</script>
