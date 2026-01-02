<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head :title="product.name" />

        <div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- LEFT: IMAGE GALLERY -->
            <div class="lg:col-span-4">
                <img :src="selectedImage" class="w-full h-96 object-cover rounded-lg border" />

                <div class="flex gap-2 mt-4">
                    <img v-for="img in product.images" :key="img.id" :src="img.image_path"
                        @click="selectedImage = img.image_path"
                        class="w-16 h-16 object-cover rounded border cursor-pointer hover:border-primary-500" />
                </div>
            </div>

            <!-- CENTER: PRODUCT INFO -->
            <div class="lg:col-span-5 space-y-4">

                <h1 class="text-2xl font-bold text-gray-900">
                    {{ product.name }}
                </h1>

                <!-- Rating -->
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    ⭐ {{ product.rating }} / 5
                    <span>({{ product.reviews_count }} reviews)</span>
                </div>

                <!-- Pricing -->
                <div class="space-y-1">
                    <div v-if="product.discount_percentage" class="text-red-600 font-semibold">
                        -{{ product.discount_percentage }}% OFF
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-3xl font-bold text-gray-900">
                            Rs {{ product.effective_price }}
                        </span>

                        <span v-if="product.discount_percentage" class="line-through text-gray-400">
                            Rs {{ product.price }}
                        </span>
                    </div>
                </div>

                <!-- Description -->
                <p class="text-gray-700 leading-relaxed">
                    {{ product.description }}
                </p>

                <!-- Attributes -->
                <div class="border-t pt-4 space-y-2">
                    <div v-for="(value, key) in product.attributes" :key="key" class="flex gap-2 text-sm">
                        <span class="font-medium text-gray-600 w-28">
                            {{ key }}:
                        </span>
                        <span>{{ value }}</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: BUY BOX -->
            <div class="lg:col-span-3 border rounded-lg p-4 space-y-4 h-fit">

                <div class="text-lg font-semibold">
                    Rs {{ product.effective_price }}
                </div>

                <div class="text-sm font-medium" :class="product.is_in_stock ? 'text-green-600' : 'text-red-600'">
                    {{ product.is_in_stock ? 'In Stock' : 'Out of Stock' }}
                </div>

                <div class="flex flex-col gap-2">
                    <Button :disabled="!product.is_sellable" class="w-full">
                        Add to Cart
                    </Button>

                    <Button variant="outline" class="w-full">
                        Add to Wishlist
                    </Button>
                </div>

                <div class="text-xs text-gray-500">
                    Category: {{ product.category?.name }}
                </div>
            </div>

        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'

const page = usePage<any>()
const product = page.props.product

const selectedImage = ref(
    product.images.find((i: any) => i.is_primary)?.image_path
    || product.images[0]?.image_path
)

const breadcrumbs = [
    { title: 'Products', href: '/products' },
    { title: product.name, href: `/products/${product.slug}` },
]
</script>
