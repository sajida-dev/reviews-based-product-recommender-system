<template>
    <div class="relative overflow-hidden rounded-2xl bg-slate-900 border border-slate-800
           shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
        <!-- Discount Badge -->
        <span v-if="discountPercentage > 0"
            class="absolute top-3 left-3 z-10 rounded-lg bg-orange-500 px-2 py-1 text-xs font-semibold text-white">
            {{ discountPercentage }}% OFF
        </span>

        <!-- Wishlist -->
        <button class="absolute top-3 right-3 z-10 text-orange-400 transition hover:scale-110 hover:text-orange-500"
            aria-label="Toggle wishlist" @click="$emit('toggle-wishlist', product)">
            ❤
    </button>

        <!-- Product Image -->
        <div class="relative h-64 w-full overflow-hidden">
            <img :src="product.image" :alt="product.name"
                class="h-full w-full object-cover transition-transform duration-300 hover:scale-105" />
        </div>

        <!-- Content -->
        <div class="space-y-3 p-4">
            <!-- Colors -->
            <div v-if="product.colors?.length" class="flex gap-2">
                <span v-for="(color, i) in product.colors" :key="i" class="h-5 w-5 rounded-full border border-slate-700"
                    :style="{ backgroundColor: color }" />
            </div>

            <!-- Category -->
            <p class="text-xs font-semibold uppercase tracking-wide text-orange-400">
                {{ product.category }}
            </p>

            <!-- Title -->
            <h3 class="line-clamp-2 text-sm font-semibold text-slate-100">
                {{ product.name }}
            </h3>

            <!-- Price -->
            <div class="flex items-center gap-2">
                <span class="text-lg font-bold text-slate-100">
                    ₹{{ finalPrice }}
                </span>

                <span v-if="discountPercentage > 0" class="text-sm text-slate-400 line-through">
                    ₹{{ product.price }}
                </span>
            </div>

            <!-- Add to Cart -->
            <button class="mt-2 w-full rounded-xl bg-orange-500 py-2 text-sm font-semibold
               text-white transition hover:bg-orange-600
               disabled:cursor-not-allowed disabled:bg-slate-700 disabled:text-slate-400" :disabled="!product.inStock"
                @click="$emit('add-to-cart', product)">
                {{ product.inStock ? 'ADD TO CART' : 'OUT OF STOCK' }}
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Product {
    name: string
    category: string
    price: number
    discountPercentage?: number
    image: string
    colors?: string[]
    inStock: boolean
}

const props = defineProps<{
    product: Product
}>()

defineEmits<{
    (e: 'add-to-cart', product: Product): void
    (e: 'toggle-wishlist', product: Product): void
}>()

const discountPercentage = computed(() => {
    return Number(props.product.discountPercentage || 0)
})

const finalPrice = computed(() => {
    if (discountPercentage.value > 0) {
        return Math.round(
            props.product.price -
            (props.product.price * discountPercentage.value) / 100
        )
    }
    return props.product.price
})
</script>
