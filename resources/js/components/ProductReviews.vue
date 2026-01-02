<template>
    <section class="mt-16 space-y-8 max-w-3xl mx-auto">
        <!-- Header -->
        <h3 class="text-2xl font-bold text-gray-900">Customer Reviews</h3>

        <!-- Submit Review -->
        <form @submit.prevent="submit"
            class="flex flex-col gap-4 bg-white shadow-md border border-gray-200 rounded-xl p-6">
            <h4 class="font-semibold text-gray-800">Write a Review</h4>

            <textarea v-model="form.review" placeholder="Share your thoughts about this product..."
                class="border border-gray-300 rounded-lg p-4 resize-none focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition"
                rows="4"></textarea>
            <span v-if="form.errors.review" class="text-red-500 text-sm">{{ form.errors.review }}</span>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <label class="flex items-center gap-2 text-gray-700">
                    Rating:
                    <select v-model="form.rating"
                        class="border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                        <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
                    </select>
                </label>
                <span v-if="form.errors.rating" class="text-red-500 text-sm">{{ form.errors.rating }}</span>

                <button type="submit"
                    class="bg-primary text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-dark transition"
                    :disabled="form.processing">
                    {{ form.processing ? 'Submitting...' : 'Submit' }}
                </button>
            </div>
        </form>

        <!-- Reviews List -->
        <div class="space-y-6">
            <div v-for="r in reviews" :key="r.id" class="bg-white shadow-sm border border-gray-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <img :src="r.user.avatar_url || 'https://i.pravatar.cc/40?img=' + r.id" alt="User avatar"
                            class="w-10 h-10 rounded-full object-cover" />
                        <span class="font-semibold text-gray-800">{{ r.user.name }}</span>
                    </div>
                    <div class="text-yellow-400 font-medium">
                        {{ '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating) }}
                    </div>
                </div>

                <p class="text-gray-700 mb-2">{{ r.review }}</p>
                <span class="text-gray-400 text-sm">{{ new Date(r.created_at).toLocaleDateString() }}</span>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue'

const props = defineProps<{ productId: number; initialReviews: any[] }>()

const reviews = ref(props.initialReviews)

// Inertia form
const form = useForm({
    review: '',
    rating: 5,
    product_id: props.productId,
})

// Submit review
const submit = () => {
    form.post('/reviews', {
        onSuccess: (page) => {
            reviews.value.unshift(page.props.newReview)
            form.reset('review', 'rating')
        },
        onError: () => {
            console.log('Validation failed')
        },
        onFinish: () => form.processing = false
    })
}
</script>

<style>
.bg-primary {
    background-color: #4f46e5;
}

.bg-primary-dark {
    background-color: #4338ca;
}
</style>
