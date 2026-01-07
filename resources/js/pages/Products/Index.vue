<script setup lang="ts">
import DarkProductCard from '@/components/DarkProductCard.vue'
import { usePage } from '@inertiajs/vue3'
import type { Product } from '@/types/product'

const page = usePage()

const products: Product[] = page.props.products?.data ?? []
</script>


<template>
    <PublicLayout hero-title="Discover Your Next Adventure" url="'/bg/auth-bg.jpg'"
        hero-subtitle="Browse our wide range of products and find something you love." :hero-buttons="[
            { label: 'Shop Now', href: '/products', primary: true },
            { label: 'Contact Us', href: '/contact' }
        ]">


        <!-- product listing -->
        <div class="container mx-auto my-16 grid grid-cols-1  md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <DarkProductCard v-for="product in products" :key="product.id" :product="product" />
            </div>
        </div>
    </PublicLayout>
</template>