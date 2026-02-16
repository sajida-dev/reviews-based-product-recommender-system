<template>
    <PublicLayout :show-hero="false">
        <div class="min-h-screen bg-neutral-900 py-12 px-4 md:px-6">
            <div class="max-w-4xl mx-auto">
                <!-- Success Card -->
                <div class="bg-neutral-800 rounded-xl border border-neutral-700 p-8 md:p-12 text-center">
                    <!-- Success Icon -->
                    <div class="mx-auto w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mb-6">
                        <CheckCircleIcon class="w-12 h-12 text-green-500" />
                    </div>

                    <h1 class="text-3xl font-bold text-white mb-3">Payment Successful!</h1>
                    <p class="text-neutral-300 text-lg mb-8">
                        Thank you for your purchase. Your order has been confirmed.
                    </p>

                    <!-- Order Details -->
                    <div class="bg-neutral-700/30 rounded-lg p-6 mb-8 text-left">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-neutral-400 text-sm mb-1">Order Number</p>
                                <p class="text-white font-semibold text-lg">{{ order.order_number }}</p>
                            </div>
                            <div>
                                <p class="text-neutral-400 text-sm mb-1">Payment Method</p>
                                <p class="text-white font-semibold capitalize">
                                    {{ order.payment_method?.replace('_', ' ') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-neutral-400 text-sm mb-1">Total Amount</p>
                                <p class="text-primary font-bold text-xl">Rs {{ parseFloat(order.total).toFixed(2) }}</p>
                            </div>
                            <div>
                                <p class="text-neutral-400 text-sm mb-1">Payment Status</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-400">
                                    {{ order.payment_status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items Summary -->
                    <div class="bg-neutral-700/30 rounded-lg p-6 mb-8">
                        <h2 class="text-lg font-semibold text-white mb-4 text-left">Order Items</h2>
                        <div class="space-y-3">
                            <div
                                v-for="item in order.items"
                                :key="item.id"
                                class="flex items-center gap-4 p-3 bg-neutral-800/50 rounded-lg">
                                <img
                                    :src="item.product?.primary_image?.url || item.product?.image || '/images/placeholder.png'"
                                    :alt="item.product?.name"
                                    class="w-16 h-16 rounded-lg object-cover" />
                                <div class="flex-1 text-left">
                                    <p class="text-white font-medium">{{ item.product?.name }}</p>
                                    <p class="text-neutral-400 text-sm">
                                        Quantity: {{ item.quantity }} × Rs {{ parseFloat(item.price).toFixed(2) }}
                                    </p>
                                </div>
                                <p class="text-primary font-semibold">
                                    Rs {{ (parseFloat(item.price) * item.quantity).toFixed(2) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link
                            :href="route('orders.show', order.id)"
                            class="px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90 transition">
                            View Order Details
                        </Link>
                        <Link
                            :href="route('products.index')"
                            class="px-6 py-3 border border-neutral-600 text-white rounded-lg font-semibold hover:bg-neutral-700 transition">
                            Continue Shopping
                        </Link>
                    </div>

                    <!-- Email Notice -->
                    <p class="mt-8 text-neutral-400 text-sm">
                        A confirmation email has been sent to your registered email address.
                    </p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import PublicLayout from '@/layouts/PublicLayout.vue'
import { CheckCircleIcon } from 'lucide-vue-next'
import { useShopStore } from '@/stores/useShopStore'

const shop = useShopStore()
const page = usePage()

onMounted(() => {
    // Clear cart after successful payment
    shop.setCartFromServer([])
})

defineProps<{
    order: {
        id: number
        order_number: string
        total: string | number
        payment_method: string
        payment_status: string
        items: Array<{
            id: number
            quantity: number
            price: string | number
            product?: {
                name: string
                image?: string
                primary_image?: { url: string }
            }
        }>
    }
}>()
</script>
