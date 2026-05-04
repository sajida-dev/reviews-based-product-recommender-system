<template>
    <div class="grid grid-cols-1 rounded-2xl transition hover:ring-primary/50 hover:shadow-lg hover:shadow-primary/10">
        <SmartProductCard :product="product" />
        <!-- <div v-if="
            product.because_you_liked ||
            (product.matching_aspects && product.matching_aspects.length) ||
            (product.similarity_score != null && Number.isFinite(product.similarity_score))
        " class="mt-2 space-y-1 rounded-lg border border-primary/30 bg-primary/5 px-3 py-2 text-xs text-gray-200">
            <p v-if="product.because_you_liked" class="font-medium text-primary">
                Because you liked
                <Link :href="`/products/${product.because_you_liked.slug}`"
                    class="underline decoration-dotted hover:text-white">
                    {{ product.because_you_liked.name }}
                </Link>
            </p>
            <p v-if="product.matching_aspects?.length">
                Matching aspects:
                <span class="text-gray-100">{{ product.matching_aspects.join(', ') }}</span>
            </p>
            <p v-if="product.similarity_score != null && Number.isFinite(product.similarity_score)"
                class="text-gray-400">
                Similarity score: {{ product.similarity_score.toFixed(3) }}
            </p>
        </div> -->
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import SmartProductCard from '@/components/SmartProductCard.vue'
import type { RecommendationProduct } from '@/types/recommendation'

defineProps<{
    product: RecommendationProduct
}>()
</script>
