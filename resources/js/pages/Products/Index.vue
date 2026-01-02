<script setup lang="ts">
import DarkProductCard from '@/components/DarkProductCard.vue';
const addToCart = (product: any) => {
    console.log('Add to cart:', product)
}

const toggleWishlist = (product: any) => {
    console.log('Toggle wishlist:', product)
}
</script>

<template>
    <!-- product listing -->
    <div class="container mx-auto my-16 grid grid-cols-1  md:grid-cols-2 lg:grid-cols-3 gap-6">
        <DarkProductCard :product="{
            name: 'Nike Air Max 270',
            category: 'Women Shoes',
            price: 16999,
            discountPercentage: 10,
            mainImage: '/img/headphones.png',
            images: ['/img/headphones.png', '/img/headphones.png', '/img/headphones.png'],
            inStock: true
        }" @add-to-cart="addToCart" @toggle-wishlist="toggleWishlist" />
        <DarkProductCard :product="{
            name: 'Nike Air Max 270',
            category: 'Women Shoes',
            price: 16999,
            discountPercentage: 10,
            mainImage: '/img/headphones.png',
            images: ['/img/headphones.png', '/img/headphones.png', '/img/headphones.png'],
            inStock: true
        }" @add-to-cart="addToCart" @toggle-wishlist="toggleWishlist" />
        <DarkProductCard :product="{
            name: 'Nike Air Max 270',
            category: 'Women Shoes',
            price: 16999,
            discountPercentage: 10,
            mainImage: '/img/headphones.png',
            images: ['/img/headphones.png', '/img/headphones.png', '/img/headphones.png'],
            inStock: true
        }" @add-to-cart="addToCart" @toggle-wishlist="toggleWishlist" />
    </div>

</template>