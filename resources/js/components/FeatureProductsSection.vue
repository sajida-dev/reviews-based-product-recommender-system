<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-4xl lg:text-5xl font-extrabold text-white text-center mb-5 drop-shadow-lg">Featured Products
        </h1>
        <hr class="w-50 mx-auto pb-15 ">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <DarkProductCard v-for="product in productsToShow" :key="product.id" :product="product"
                @add-to-cart="addToCart" @toggle-wishlist="toggleWishlist" />
        </div>

        <!-- VIEW ALL BUTTON -->
        <div class="mt-8 text-center" v-if="products.length > productsToShow.length">
            <button @click="viewAll"
                class="bg-gray-900 text-white px-6 py-3 rounded hover:bg-gray-700 transition-colors">
                View All Products
            </button>
        </div>
    </div>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3';
import DarkProductCard from './DarkProductCard.vue';

const page = usePage();
const products = page.props.products || [];

// Show only 6 products on homepage (2 rows of 3 each)
const productsToShow = products.slice(0, 6);

function addToCart(product) {
    console.log('Add to cart', product);
}

function toggleWishlist(product) {
    console.log('Toggle wishlist', product);
}

function viewAll() {
    router.visit('/products'); // Redirect to full product listing page
}
</script>
