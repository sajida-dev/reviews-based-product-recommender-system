<template>
    <div :class="['card', cardStyle]">
        <!-- Wishlist Heart -->
        <span class="heart" @click="$emit('toggle-wishlist', product)">❤</span>

        <!-- Discount Tag -->
        <span v-if="hasValidDiscount" class="tag">{{ product.discount_percentage }}% OFF</span>

        <!-- Product Image -->
        <img v-if="product.images && product.images.length" :src="product.images[0].url" class="main"
            :alt="product.name" />

        <div class="colors" v-if="product.attributes?.Color">
            <div v-if="isHexColor(product.attributes.Color)" :style="{ backgroundColor: product.attributes.Color }"
                class="color-swatch"></div>
            <div v-else v-for="(color, index) in product.attributes.Color.split(',')" :key="index"
                :style="{ backgroundColor: color.trim() }" class="color-swatch"></div>
        </div>

        <div class="category">{{ product.category?.name }}</div>
        <div class="title">{{ product.name }}</div>

        <div class="price">
            ${{ product.effective_price.toFixed(2) }}
            <span v-if="hasValidDiscount">${{ Number(product.price ?? 0).toFixed(2) }}</span>
        </div>

        <button class="btn" :disabled="!product.is_sellable" @click="$emit('add-to-cart', product)">
            {{ product.is_sellable ? 'ADD TO CART' : 'OUT OF STOCK' }}
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    product: { type: Object, required: true },
    cardStyle: { type: String, default: "" }
})

const hasValidDiscount = computed(() => {
    return props.product.discount_percentage && props.product.discount_percentage > 0
})

const isHexColor = (color) => /^#([0-9A-F]{3}){1,2}$/i.test(color)
</script>

<style scoped>
/* Your card CSS here */
</style>
