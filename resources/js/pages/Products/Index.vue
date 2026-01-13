<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue'
import DarkProductCard from '@/components/DarkProductCard.vue'
import { usePage } from '@inertiajs/vue3'
import type { Product } from '@/types/product'
import { mapProducts } from '@/types/product'

const page = usePage()

const rawProducts = page.props.products?.data ?? []

const products: Product[] = mapProducts(rawProducts)
</script>

<template>
    <PublicLayout hero-title="Discover Your Next Adventure" url="/bg/auth-bg.jpg"
        hero-subtitle="Browse our wide range of products and find something you love." :hero-buttons="[
            { label: 'Shop Now', href: '/products', primary: true },
            { label: 'Contact Us', href: '/contact' }
        ]">
        <!-- product listing -->
        <div class="container mx-auto py-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <DarkProductCard v-for="product in products" :key="product.id" :product="product" />
        </div>
    </PublicLayout>
</template>
