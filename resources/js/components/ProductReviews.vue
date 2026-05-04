<template>
    <section class="py-16 max-w-6xl mx-left mr-auto px-20 space-y-10">
        <h3 class="text-2xl mb-5 font-bold text-white">Customer Reviews</h3>

        <!-- Submit Review -->
        <form v-if="isAuthenticated" @submit.prevent="submit"
            class="flex flex-col gap-4 bg-white/20 backdrop-blur-md border-white/30 shadow-md border rounded-xl p-6">

            <h4 class="font-semibold text-white">Write a Review</h4>

            <textarea v-model="form.review" placeholder="Share your thoughts about this product..."
                class="border placeholder:text-white text-white border-gray-300 rounded-lg p-4 resize-none focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition"
                rows="4"></textarea>
            <span v-if="form.errors.review" class="text-red-500 text-sm">{{ form.errors.review }}</span>
            <span v-if="form.errors.product_id" class="text-red-500 text-sm">{{ form.errors.product_id }}</span>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <label class="flex items-center gap-2 text-gray-100">
                    Rating:
                    <select v-model="form.rating"
                        class="border border-gray-200 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
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

            <!-- Show moderation message -->
            <p v-if="moderationMessage" class="text-yellow-300 mt-2">{{ moderationMessage }}</p>
        </form>
        <div
            v-else
            class="rounded-xl border border-white/30 bg-white/10 p-6 text-sm text-gray-100 backdrop-blur-md"
        >
            Please <a href="/login" class="font-semibold text-primary underline">sign in</a> to submit a review.
        </div>

        <!-- Reviews List -->
        <div class="space-y-6">
            <!-- <div v-if="reviews.length === 0 && !page.props.auth.user.is_admin" class="text-center text-gray-300 py-10">
                No reviews yet. Be the first to share your thoughts!
            </div>
            <div v-else-if="reviews.length === 0 && page.props.auth.user.is_admin"
                class="text-center text-gray-300 py-10">
                No reviews yet.
            </div> -->

            <transition-group name="fade" tag="div" class="space-y-6">
                <ReviewCard
                    v-for="r in reviews"
                    :key="r.id"
                    :review="r"
                    :show-moderation="!!page.props.auth?.user?.is_admin"
                    class="hover:scale-[1.01] hover:shadow-lg transition-transform duration-200"
                />
            </transition-group>
        </div>
    </section>
</template>

<script setup lang="ts">
import ReviewCard from '@/components/ReviewCard.vue'
import type { ReviewCardModel } from '@/types/review'
import { ref, Ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'

interface Review extends ReviewCardModel {
    user: NonNullable<ReviewCardModel['user']>
}

const page = usePage<any>()
const props = defineProps<{ productId: number; initialReviews: Review[] }>()
const isAuthenticated = !!page.props.auth?.user

const reviews: Ref<Review[]> = ref(props.initialReviews || [])
const moderationMessage = ref('')

const form = useForm({
    review: '',
    rating: 5,
    product_id: props.productId,
})

const submit = () => {
    const scrollPos = window.scrollY
    moderationMessage.value = ''

    form.post('/reviews', {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            const { props: pageProps } = usePage()
            const newReview = pageProps.newReview as Review | undefined
            if (newReview) {
                if (newReview.spam_flagged) {
                    moderationMessage.value = 'Your review has been submitted for moderation.'
                } else {
                    reviews.value.unshift(newReview)
                }
            }
            form.reset('review', 'rating')
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0]
            if (typeof firstError === 'string') {
                moderationMessage.value = firstError
            }
            console.log('Review submission errors:', errors)
        },
        onFinish: () => window.scrollTo(0, scrollPos),
    })
}
</script>
