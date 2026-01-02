<template>
    <div class="relative overflow-hidden rounded-2xl bg-neutral-800 border border-neutral-700
           shadow-lg transition-all duration-300 hover:shadow-2xl hover:-tranneutral-y-1">
        <span v-if="discountPercentage > 0"
            class="absolute top-3 left-3 z-10 rounded-lg bg-primary px-2 py-1 text-xs font-semibold text-white">
            {{ discountPercentage }}% Off
        </span>
        <!-- Main Product Image -->
        <div class="p-1 flex justify-center items-center bg-neutral-800 overflow-hidden">
            <img :src="mainImage" class="h-70 object-cover rounded-lg transition-transform duration-300 hover:scale-105"
                alt="product" />
        </div>

        <!-- Thumbnails -->
        <div class="flex gap-2 pt-1 px-2 pb-4 overflow-x-auto">
            <div v-for="(img, index) in images" :key="index" @click="mainImage = img"
                class="cursor-pointer rounded-lg p-0.5 transition border" :class="{
                    'border-primary': mainImage === img,
                    'border-neutral-700': mainImage !== img
                }">
                <img :src="img" class="h-10 w-10 rounded-md object-cover" />
            </div>
        </div>

        <!-- Product Info -->
        <div class="bg-neutral-800 text-white px-4 pt-4 pb-6 rounded-t-3xl">
            <!-- Category -->
            <p class="text-xs text-primary uppercase tracking-wide mb-1">
                {{ category }}
            </p>

            <!-- Title & Price -->
            <div class="flex justify-between items-center mb-3">
                <h2 class="font-semibold text-lg">{{ name }}</h2>
                <div class="flex flex-col">
                    <span v-if="discountPercentage > 0" class="text-xs text-neutral-400 line-through">
                        Rs {{ price }}
                    </span>
                    <span class="font-semibold text-lg">Rs {{ discountedPrice }}</span>
                </div>

            </div>

            <!-- Add to Cart & Wishlist -->
            <div class="flex items-center justify-between gap-2">
                <!-- Add to Cart -->
                <button
                    class="flex-1 bg-primary text-white py-3 rounded-xl font-semibold
                 transition hover:bg-primary disabled:cursor-not-allowed disabled:bg-neutral-700 disabled:text-neutral-400"
                    :disabled="!inStock" @click="$emit('add-to-cart')">
                    {{ inStock ? 'ADD TO CART' : 'OUT OF STOCK' }}
                </button>

                <!-- Wishlist -->
                <button @click="toggleLike" class="bg-neutral-700 p-3 rounded-xl transition hover:bg-neutral-600">
                    <svg v-if="liked" class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09
                 C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5
                 c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                    <svg v-else class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0
                 116.364 6.364L12 21.364l-7.682-7.682a4.5
                 4.5 0 010-6.364z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'

interface Product {
    name: string
    category: string
    price: number
    discountedPrice?: number
    discountPercentage?: number
    images: string[]
    colors?: string[]
    inStock: boolean
    mainImage: string
}
const discountPercentage = computed(() => {
    return Number(props.product.discountPercentage || 0)
})
const props = defineProps<{
    product: Product
}>()
const { name, category, price, images, inStock, discountedPrice } = props.product
const mainImage = ref(props.product.mainImage || (images.length > 0 ? images[0] : ''))

const liked = ref(false)
watch(() => props.product.mainImage, (newVal) => {
    if (newVal) mainImage.value = newVal
})
const toggleLike = () => {
    liked.value = !liked.value
    // Emit event if needed
    if (liked.value) {
        // Added to wishlist
        // $emit('wishlist-added', props)
    } else {
        // Removed from wishlist
        // $emit('wishlist-removed', props)
    }
}
</script>

<style scoped>
/* Smooth scroll for thumbnails */
::-webkit-scrollbar {
    height: 4px;
}

::-webkit-scrollbar-thumb {
    background: #555;
    border-radius: 4px;
}
</style>
