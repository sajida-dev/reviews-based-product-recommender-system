<template>
    <PublicLayout hero-title="Discover Your Next Adventure" url="'/bg/product.jpeg'"
        hero-subtitle="Browse our wide range of products and find something you love.">
        <div class="min-h-screen bg-neutral-900 py-12 px-4 md:px-6">
            <div class="max-w-6xl mx-auto">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-sm mb-8">
                    <Link :href="route('cart.index')" class="text-primary hover:text-primary/80 transition">
                        Cart
                    </Link>
                    <span class="text-neutral-500">›</span>
                    <span class="text-white font-medium">Shipping</span>
                    <span class="text-neutral-500">›</span>
                    <span class="text-neutral-500">Payment</span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Shipping Form -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Shipping Address -->
                        <div class="bg-neutral-800 rounded-xl border border-neutral-700 p-6 md:p-8">
                            <h2 class="text-xl font-bold text-white mb-6">Shipping Address</h2>
                            <form id="checkout-form" @submit.prevent="onSubmit" class="space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-neutral-300 text-sm font-medium mb-2">
                                            First Name <span class="text-destructive">*</span>
                                        </label>
                                        <input v-model="form.first_name" type="text" required
                                            class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                                   text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary"
                                            placeholder="First name" />
                                        <p v-if="form.errors.first_name" class="mt-1 text-sm text-destructive">
                                            {{ form.errors.first_name }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-neutral-300 text-sm font-medium mb-2">
                                            Last Name <span class="text-destructive">*</span>
                                        </label>
                                        <input v-model="form.last_name" type="text" required
                                            class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                                   text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary"
                                            placeholder="Last name" />
                                        <p v-if="form.errors.last_name" class="mt-1 text-sm text-destructive">
                                            {{ form.errors.last_name }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-neutral-300 text-sm font-medium mb-2">
                                        Email <span class="text-destructive">*</span>
                                    </label>
                                    <input v-model="form.email" type="email" required
                                        class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                               text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary"
                                        placeholder="email@example.com" />
                                    <p v-if="form.errors.email" class="mt-1 text-sm text-destructive">
                                        {{ form.errors.email }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-neutral-300 text-sm font-medium mb-2">
                                        Phone number <span class="text-destructive">*</span>
                                    </label>
                                    <div class="flex gap-2">
                                        <select v-model="form.phone_country"
                                            class="w-24 bg-neutral-700/50 border border-neutral-600 rounded-lg px-3 py-3
                                                   text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                            <option value="IND">IND</option>
                                            <option value="USA">USA</option>
                                            <option value="UK">UK</option>
                                            <option value="PAK">PAK</option>
                                        </select>
                                        <input v-model="form.phone" type="tel" required
                                            class="flex-1 bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                                   text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary"
                                            placeholder="+91 1234567890" />
                                    </div>
                                    <p v-if="form.errors.phone" class="mt-1 text-sm text-destructive">
                                        {{ form.errors.phone }}
                                    </p>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div>
                                        <label class="block text-neutral-300 text-sm font-medium mb-2">
                                            City <span class="text-destructive">*</span>
                                        </label>
                                        <input v-model="form.city" type="text" required
                                            class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                                   text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary"
                                            placeholder="City" />
                                        <p v-if="form.errors.city" class="mt-1 text-sm text-destructive">
                                            {{ form.errors.city }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-neutral-300 text-sm font-medium mb-2">
                                            State <span class="text-destructive">*</span>
                                        </label>
                                        <input v-model="form.state" type="text" required
                                            class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                                   text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary"
                                            placeholder="State" />
                                        <p v-if="form.errors.state" class="mt-1 text-sm text-destructive">
                                            {{ form.errors.state }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-neutral-300 text-sm font-medium mb-2">
                                            Zip Code <span class="text-destructive">*</span>
                                        </label>
                                        <input v-model="form.zip_code" type="text" required
                                            class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                                   text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary"
                                            placeholder="560021" />
                                        <p v-if="form.errors.zip_code" class="mt-1 text-sm text-destructive">
                                            {{ form.errors.zip_code }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-neutral-300 text-sm font-medium mb-2">
                                        Address / Description <span class="text-destructive">*</span>
                                    </label>
                                    <textarea v-model="form.description" rows="3" required
                                        class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                               text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                                        placeholder="Enter full address or description..." />
                                    <p v-if="form.errors.description" class="mt-1 text-sm text-destructive">
                                        {{ form.errors.description }}
                                    </p>
                                </div>

                                <!-- Shipping Method -->
                                <div class="pt-6 border-t border-neutral-700">
                                    <h3 class="text-lg font-bold text-white mb-4">Shipping Method</h3>
                                    <div class="flex justify-between space-y-3">
                                        <label
                                            class="flex items-center gap-4 h-25 w-72 p-4 rounded-lg border cursor-pointer transition"
                                            :class="form.shipping_method === 'free'
                                                ? 'border-primary bg-primary/10'
                                                : 'border-neutral-600 bg-neutral-700/30 hover:border-neutral-500'">
                                            <input v-model="form.shipping_method" type="radio" value="free"
                                                class="w-4 h-4 text-primary focus:ring-primary" />
                                            <div class="flex-1">
                                                <p class="font-medium text-white">Free Shipping</p>
                                                <p class="text-sm text-neutral-400">7-20 Days</p>
                                            </div>
                                            <span class="text-primary font-semibold">Rs 0</span>
                                        </label>
                                        <label
                                            class="flex items-center gap-4 h-25 w-72 p-4 rounded-lg border cursor-pointer transition"
                                            :class="form.shipping_method === 'express'
                                                ? 'border-primary bg-primary/10'
                                                : 'border-neutral-600 bg-neutral-700/30 hover:border-neutral-500'">
                                            <input v-model="form.shipping_method" type="radio" value="express"
                                                class="w-4 h-4 text-primary focus:ring-primary" />
                                            <div class="flex-1">
                                                <p class="font-medium text-white">Express Shipping</p>
                                                <p class="text-sm text-neutral-400">1-3 Days</p>
                                            </div>
                                            <span class="text-primary font-semibold">Rs {{ expressShippingFee }}</span>
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Cart Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-neutral-800 rounded-xl border border-neutral-700 p-6 sticky top-24">
                            <h2 class="text-xl font-bold text-white mb-6">Your Cart</h2>

                            <!-- Product list with quantity controls -->
                            <div class="space-y-4 max-h-72 overflow-y-auto">
                                <p v-if="!shop.cart.length" class="text-neutral-400 text-sm">Cart is empty</p>
                                <div v-for="item in shop.cart" :key="item.id"
                                    class="flex items-center gap-4">
                                    <div class="relative flex-shrink-0">
                                        <img :src="item.image || '/images/placeholder.png'" :alt="item.name"
                                            class="w-16 h-16 rounded-lg object-cover" />
                                        <span class="absolute -top-1 -right-1 bg-primary text-white text-xs rounded-full
                                                     min-w-[20px] h-5 px-1 flex items-center justify-center">
                                            {{ item.quantity }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white font-medium text-sm truncate">{{ item.name }}</p>
                                        <p class="text-primary text-sm">Rs {{ (item.price * item.quantity).toFixed(2) }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <button @click="decreaseQty(item)" type="button"
                                                class="w-6 h-6 rounded border border-neutral-600 text-white flex items-center
                                                       justify-center hover:bg-neutral-600 transition text-sm leading-none">−</button>
                                            <span class="text-white text-sm w-6 text-center">{{ item.quantity }}</span>
                                            <button @click="increaseQty(item)" type="button"
                                                class="w-6 h-6 rounded border border-neutral-600 text-white flex items-center
                                                       justify-center hover:bg-neutral-600 transition text-sm leading-none">+</button>
                                        </div>
                                    </div>
                                    <button @click="removeItem(item)" type="button" aria-label="Remove item"
                                        class="text-red-400 hover:text-red-500 transition flex-shrink-0">
                                        <TrashIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Discount code -->
                            <div class="mt-6 pt-6 border-t border-neutral-700">
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <TagIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-500" />
                                        <input v-model="discountCode" type="text"
                                            class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg pl-10 pr-4 py-3
                                                   text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary"
                                            placeholder="Discount code" />
                                    </div>
                                    <button type="button"
                                        class="px-4 py-3 rounded-lg border border-neutral-600 text-white hover:bg-neutral-700 transition font-medium"
                                        @click="applyDiscount">
                                        Apply
                                    </button>
                                </div>
                                <p v-if="discountMessage" class="mt-2 text-sm"
                                    :class="discountApplied ? 'text-green-400' : 'text-neutral-400'">
                                    {{ discountMessage }}
                                </p>
                            </div>

                            <!-- Order totals -->
                            <div class="mt-6 pt-6 border-t border-neutral-700 space-y-2">
                                <div class="flex justify-between text-neutral-300">
                                    <span>Subtotal</span>
                                    <span>Rs {{ subtotal.toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-neutral-300">
                                    <span>Shipping</span>
                                    <span>Rs {{ shippingCost.toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-neutral-300 items-center gap-1">
                                    <span class="flex items-center gap-1">
                                        Estimated taxes
                                        <button type="button" class="text-neutral-500 hover:text-neutral-400"
                                            title="Taxes may vary based on your location">
                                            <InfoIcon class="w-4 h-4" />
                                        </button>
                                    </span>
                                    <span>Rs {{ estimatedTax.toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-white font-bold text-lg pt-2">
                                    <span>Total</span>
                                    <span>Rs {{ total.toFixed(2) }}</span>
                                </div>
                            </div>

                            <!-- Continue to Payment -->
                            <button type="submit" form="checkout-form" :disabled="!shop.cart.length || form.processing"
                                class="w-full mt-6 bg-primary text-white py-3 rounded-lg font-semibold
                                       hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ form.processing ? 'Processing...' : 'Continue to Payment' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import PublicLayout from '@/layouts/PublicLayout.vue'
import { useShopStore } from '@/stores/useShopStore'
import { TrashIcon, TagIcon, InfoIcon } from 'lucide-vue-next'
import type { ShopState } from '@/stores/useShopStore'

const page = usePage()
const props = defineProps<{
    user?: { name: string; email: string }
}>()

const shop = useShopStore()

const nameParts = (props.user?.name ?? '').split(' ')
const firstName = nameParts[0] ?? ''
const lastName = nameParts.slice(1).join(' ') ?? ''
const expressShippingFee = 9
const taxRate = 0.05

const form = useForm({
    first_name: firstName,
    last_name: lastName,
    email: props.user?.email ?? '',
    phone: '',
    phone_country: 'IND',
    city: '',
    state: '',
    zip_code: '',
    description: '',
    shipping_method: 'free',
})

const discountCode = ref('')
const discountMessage = ref('')
const discountApplied = ref(false)

const subtotal = computed(() => shop.cartTotal)
const shippingCost = computed(() =>
    form.shipping_method === 'express' ? expressShippingFee : 0
)
const estimatedTax = computed(() => (subtotal.value + shippingCost.value) * taxRate)
const total = computed(() => subtotal.value + shippingCost.value + estimatedTax.value)

function buildAddressString() {
    const parts = [
        `${form.first_name} ${form.last_name}`,
        form.email,
        `${form.phone_country} ${form.phone}`,
        form.description,
        `${form.city}, ${form.state} ${form.zip_code}`,
    ]
    return parts.filter(Boolean).join(' | ')
}

function onSubmit() {
    form.transform((data) => ({
        shipping_address: buildAddressString(),
        billing_address: buildAddressString(),
    })).post(route('checkout.submit'), {
        preserveScroll: true,
        onSuccess: () => {
            shop.setCartFromServer([])
        },
    })
}

function increaseQty(item: { id: number; quantity: number }) {
    shop.updateCartItemQuantity(item.id, item.quantity + 1)
}

function decreaseQty(item: { id: number; quantity: number }) {
    if (item.quantity > 1) {
        shop.updateCartItemQuantity(item.id, item.quantity - 1)
    } else {
        shop.removeFromCart(item.id)
    }
}

function removeItem(item: { id: number }) {
    shop.removeFromCart(item.id)
}

function applyDiscount() {
    if (!discountCode.value.trim()) {
        discountMessage.value = 'Enter a discount code'
        discountApplied.value = false
        return
    }
    discountMessage.value = 'Discount codes coming soon'
    discountApplied.value = false
}

onMounted(() => {
    const backendShop = (page.props as Record<string, unknown>)?.shop as ShopState | undefined
    if (backendShop?.cart) {
        shop.setCartFromServer(backendShop.cart)
    }
})
</script>
