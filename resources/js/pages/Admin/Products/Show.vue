<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head :title="product.name" />

        <main class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-[0.8fr_1fr] gap-16">
            <!-- LEFT: IMAGE GALLERY -->
            <section class="space-y-4">
                <!-- Main Image -->
                <div class="relative w-[35vw] h-[34vw] border-2 border-primary overflow-hidden rounded-xl cursor-zoom-in"
                    ref="mainImageContainer" @mousemove="onMouseMove" @mouseleave="resetZoom">
                    <img :src="selectedImage" class="w-full h-full object-cover" />

                    <!-- Zoom lens -->
                    <div v-if="isZooming"
                        class="absolute rounded-full border-2 border-gray-300 w-32 h-auto pointer-events-none z-10"
                        :style="lensStyle"></div>
                </div>

                <!-- Thumbnails -->
                <div class="flex items-center gap-2 mt-2 bg-neutral-50 p-2 rounded-xl">
                    <button v-if="product.images.length > 4" @click="scrollThumbnails('left')"
                        class="p-2 bg-white rounded-full shadow hover:bg-gray-100">
                        <ChevronLeftIcon class="w-5 h-5 text-gray-700" />
                    </button>

                    <div ref="thumbContainer" class="flex gap-2 overflow-x-auto scrollbar-hide flex-1">
                        <button v-for="(img, index) in product.images" :key="img.id" @click="selectedIndex = index"
                            class="relative rounded-xl overflow-hidden flex-shrink-0 w-20 h-20">
                            <img :src="img.url" class="w-full h-full object-cover" />
                            <!-- Overlay inactive images -->
                            <div v-if="selectedIndex !== index"
                                class="absolute inset-0 bg-black/30 bg-opacity-30 transition"></div>
                        </button>
                    </div>

                    <button v-if="product.images.length > 4" @click="scrollThumbnails('right')"
                        class="p-2 bg-white rounded-full shadow hover:bg-gray-100">
                        <ChevronRightIcon class="w-5 h-5 text-gray-700" />
                    </button>
                </div>
            </section>

            <!-- RIGHT: PRODUCT INFO -->
            <section class="flex flex-col gap-6">
                <h2 class="uppercase tracking-widest text-primary font-bold text-sm">
                    {{ product.category?.name ?? 'Product' }}
                </h2>

                <h1 class="text-4xl lg:text-5xl font-bold text-gray-900">{{ product.name }}</h1>

                <p class="text-gray-600 text-md leading-relaxed">{{ product.description }}</p>

                <!-- Price -->
                <div class="space-y-2">
                    <div class="flex items-center gap-4">
                        <span class="text-3xl font-bold">Rs {{ product.effective_price }}</span>
                        <span v-if="product.discount_percentage"
                            class="bg-primary text-white px-3 py-1 rounded-md font-bold">{{ product.discount_percentage
                            }}%</span>
                    </div>
                    <span v-if="product.discount_percentage" class="text-gray-400 line-through text-lg">Rs {{
                        product.price }}</span>
                </div>

                <!-- Quantity + Cart -->
                <div class="flex flex-col lg:flex-row gap-4 mt-6">
                    <div class="flex items-center justify-between bg-gray-100 rounded-lg px-6 py-4 lg:w-1/3">
                        <button @click="decreaseQty" class="text-primary font-bold">
                            <MinusIcon class="w-6 h-6" />
                        </button>
                        <span class="font-bold text-lg">{{ quantity }}</span>
                        <button @click="increaseQty" class="text-primary font-bold">
                            <PlusIcon class="w-6 h-6" />
                        </button>
                    </div>

                    <button :disabled="!product.is_sellable"
                        class="flex items-center justify-center gap-3 bg-primary hover:bg-primary-dark disabled:bg-gray-300 text-white font-bold py-4 rounded-lg shadow-lg lg:w-2/3 transition">
                        <ShoppingCartIcon class="w-5 h-5" /> Add to cart
                    </button>
                </div>

                <!-- Stock -->
                <div class="font-semibold" :class="product.is_in_stock ? 'text-green-600' : 'text-red-600'">
                    {{ product.is_in_stock ? 'In Stock' : 'Out of Stock' }}
                </div>

                <!-- Attributes -->
                <div class="border-t pt-6 space-y-2">
                    <div v-for="(value, key) in product.attributes" :key="key" class="flex text-sm">
                        <span class="w-32 font-semibold text-gray-600">{{ key }}:</span>
                        <span>{{ value }}</span>
                    </div>
                </div>
            </section>
        </main>

        <!-- Recommended Products -->
        <section class="mt-16">
            <!-- <RecommendedProducts :currentProductId="product.id" /> -->
            <div class="container">
                <NormalCard :product="product" />
                <DarkCard :product="product" />
                <DiscountCard :product="product" />
            </div>
        </section>

        <!-- Product Reviews -->
        <section class="mt-16">
            <ProductReviews :productId="product.id" :initialReviews="reviews" />
            <Reviews />
        </section>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ChevronLeftIcon, ChevronRightIcon, PlusIcon, MinusIcon, ShoppingCartIcon } from 'lucide-vue-next'
import RecommendedProducts from '@/components/RecommendedProducts.vue'
import ProductReviews from '@/components/ProductReviews.vue'
import NormalCard from '@/components/NormalCard.vue'
import DarkCard from '@/components/DarkCard.vue'
import DiscountCard from '@/components/DiscountCard.vue'
import Reviews from '@/components/Reviews.vue'

const page = usePage<any>()
const product = page.props.product
const reviews = page.props.initialReviews

// Image gallery
const selectedIndex = ref(product.images.findIndex((i: any) => i.is_primary) || 0)
const selectedImage = computed(() => product.images[selectedIndex.value]?.url || '')

// Thumbnail scroll
const thumbContainer = ref<HTMLDivElement | null>(null)
const scrollThumbnails = (direction: 'left' | 'right') => {
    if (!thumbContainer.value) return
    const scrollAmount = 80
    thumbContainer.value.scrollBy({ left: direction === 'right' ? scrollAmount : -scrollAmount, behavior: 'smooth' })
}

// Quantity
const quantity = ref(1)
const increaseQty = () => quantity.value++
const decreaseQty = () => { if (quantity.value > 1) quantity.value-- }

// Breadcrumbs
const breadcrumbs = [
    { title: 'Products', href: '/products' },
    { title: product.name, href: `/products/${product.id}` },
]

// Zoom logic
const mainImageContainer = ref<HTMLDivElement | null>(null)
const isZooming = ref(false)
const lensX = ref(0)
const lensY = ref(0)
const zoomScale = 2 // how much to zoom

const lensStyle = computed(() => {
    if (!mainImageContainer.value) return {}
    const rect = mainImageContainer.value.getBoundingClientRect()
    const lensSize = 200

    return {
        top: lensY.value + 'px',
        left: lensX.value + 'px',
        width: lensSize + 'px',
        height: lensSize + 'px',
        backgroundImage: `url(${selectedImage.value})`,
        backgroundRepeat: 'no-repeat',
        backgroundSize: `${rect.width * zoomScale}px ${rect.height * zoomScale}px`,
        backgroundPosition: `-${lensX.value * zoomScale}px -${lensY.value * zoomScale}px`,
    }
})

const onMouseMove = (e: MouseEvent) => {
    if (!mainImageContainer.value) return
    const rect = mainImageContainer.value.getBoundingClientRect()
    const x = e.clientX - rect.left
    const y = e.clientY - rect.top
    const lensSize = 128

    lensX.value = Math.min(Math.max(x - lensSize / 2, 0), rect.width - lensSize)
    lensY.value = Math.min(Math.max(y - lensSize / 2, 0), rect.height - lensSize)

    isZooming.value = true
}

const resetZoom = () => {
    isZooming.value = false
}
</script>
