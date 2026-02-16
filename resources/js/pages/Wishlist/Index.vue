<template>
    <PublicLayout hero-title="Discover Your Next Adventure" url="'/bg/product.jpeg'"
        hero-subtitle="Browse our wide range of products and find something you love." >
        <Head title="My Wishlist" />
        <div class="min-h-screen bg-neutral-900 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-3xl font-bold text-white">My Wishlist</h1>
                </div>

                <!-- Empty State -->
                <div v-if="!items || items.length === 0"
                    class="text-center py-16 bg-neutral-800/95 backdrop-blur border border-neutral-700 rounded-xl shadow-lg">
                    <svg class="mx-auto h-16 w-16 text-neutral-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-white mb-2">Your wishlist is empty</h3>
                    <p class="text-neutral-400 mb-6">Start adding products you love to your wishlist!</p>
                    <Link href="/products"
                        class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors font-semibold">
                        Browse Products
                    </Link>
                </div>

                <!-- Wishlist Table -->
                <div v-else class="bg-neutral-800/95 backdrop-blur border border-neutral-700 rounded-xl shadow-lg overflow-hidden">
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-neutral-900/50 border-b border-neutral-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">
                                        Product
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">
                                        Unit Price
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">
                                        Stock Status
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-medium text-neutral-400 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-700">
                                <tr v-for="item in items" :key="item.id"
                                    class="hover:bg-neutral-900/50 transition-colors">
                                    <!-- Remove Button & Product Image -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <button @click="removeFromWishlist(item.product_id)"
                                                class="text-neutral-400 hover:text-red-400 transition-colors p-1"
                                                title="Remove from wishlist">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <img :src="item.image" :alt="item.name"
                                                class="w-16 h-16 object-cover rounded-lg border border-neutral-700" />
                                            <div>
                                                <Link :href="route('products.show', item.slug)"
                                                    class="text-white font-medium hover:text-primary transition-colors">
                                                    {{ item.name }}
                                                </Link>
                                                <p v-if="item.category" class="text-sm text-neutral-400 mt-1">
                                                    {{ item.category }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Unit Price -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span v-if="item.discount_percentage > 0"
                                                class="text-sm text-neutral-500 line-through">
                                                Rs {{ item.price.toFixed(2) }}
                                            </span>
                                            <span class="text-lg font-semibold text-white">
                                                Rs {{ item.effective_price.toFixed(2) }}
                                            </span>
                                            <span v-if="item.discount_percentage > 0"
                                                class="text-xs text-primary mt-1 font-semibold">
                                                {{ item.discount_percentage }}% OFF
                                            </span>
                                        </div>
                                    </td>
                                    <!-- Stock Status -->
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="item.is_in_stock
                                                ? 'bg-green-900/30 text-green-400'
                                                : 'bg-red-900/30 text-red-400'">
                                            {{ item.is_in_stock ? 'In Stock' : 'Out of Stock' }}
                                        </span>
                                    </td>
                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-right">
                                        <button @click="addToCart(item)"
                                            :disabled="!item.is_sellable"
                                            class="px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary/80 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                            ADD TO CART
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden divide-y divide-neutral-700">
                        <div v-for="item in items" :key="item.id" class="p-4">
                            <div class="flex gap-4">
                                <!-- Remove Button -->
                                <button @click="removeFromWishlist(item.product_id)"
                                    class="text-neutral-400 hover:text-red-400 transition-colors self-start mt-1"
                                    title="Remove from wishlist">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <!-- Product Image -->
                                <img :src="item.image" :alt="item.name"
                                    class="w-20 h-20 object-cover rounded-lg border border-neutral-700 flex-shrink-0" />
                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <Link :href="route('products.show', item.slug)"
                                        class="text-white font-medium hover:text-primary transition-colors block mb-1">
                                        {{ item.name }}
                                    </Link>
                                    <p v-if="item.category" class="text-sm text-neutral-400 mb-2">
                                        {{ item.category }}
                                    </p>
                                    <!-- Price -->
                                    <div class="mb-2">
                                        <span v-if="item.discount_percentage > 0"
                                            class="text-sm text-neutral-500 line-through mr-2">
                                            Rs {{ item.price.toFixed(2) }}
                                        </span>
                                        <span class="text-lg font-semibold text-white">
                                            Rs {{ item.effective_price.toFixed(2) }}
                                        </span>
                                    </div>
                                    <!-- Stock Status -->
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold mb-3"
                                        :class="item.is_in_stock
                                            ? 'bg-green-900/30 text-green-400'
                                            : 'bg-red-900/30 text-red-400'">
                                        {{ item.is_in_stock ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                    <!-- Add to Cart Button -->
                                    <button @click="addToCart(item)"
                                        :disabled="!item.is_sellable"
                                        class="w-full px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary/80 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                        ADD TO CART
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import PublicLayout from '@/layouts/PublicLayout.vue'
import { useShopStore } from '@/stores/useShopStore'
import { toast } from 'vue3-toastify'
import { computed } from 'vue'

const shop = useShopStore()
const page = usePage()

interface WishlistItem {
    id: number
    product_id: number
    name: string
    slug: string
    price: number
    discount_price: number | null
    effective_price: number
    discount_percentage: number
    stock: number
    is_in_stock: boolean
    is_sellable: boolean
    image: string
    category: string
}

const props = defineProps<{
    items: WishlistItem[]
}>()

const items = computed(() => props.items || [])

function removeFromWishlist(productId: number) {
    router.post(
        route('wishlist.toggle'),
        { product_id: productId },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Product removed from wishlist')
            },
            onError: () => {
                toast.error('Failed to remove product from wishlist')
            },
        }
    )
}

function addToCart(item: WishlistItem) {
    if (!item.is_sellable) {
        toast.error('Product is out of stock')
        return
    }

    shop.addToCart({
        id: item.product_id,
        name: item.name,
        price: item.effective_price,
        image: item.image,
    })
}
</script>

<style scoped>
/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.7);
}
</style>
