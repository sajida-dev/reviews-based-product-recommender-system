<template>
    <PublicLayout :show-hero="false">
        <div class="min-h-screen bg-neutral-900 py-12 px-4 md:px-6">
            <div class="max-w-6xl mx-auto">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-sm mb-8">
                    <Link :href="route('cart.index')" class="text-primary hover:text-primary/80 transition">
                        Cart
                    </Link>
                    <span class="text-neutral-500">›</span>
                    <Link :href="route('checkout')" class="text-primary hover:text-primary/80 transition">
                        Shipping
                    </Link>
                    <span class="text-neutral-500">›</span>
                    <span class="text-white font-medium">Payment</span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Payment Form -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Order Summary Card -->
                        <div class="bg-neutral-800 rounded-xl border border-neutral-700 p-6">
                            <h2 class="text-xl font-bold text-white mb-4">Order Summary</h2>
                            <div class="space-y-3">
                                <div class="flex justify-between text-neutral-300">
                                    <span>Order Number</span>
                                    <span class="text-white font-medium">{{ order.order_number }}</span>
                                </div>
                                <div class="flex justify-between text-neutral-300">
                                    <span>Subtotal</span>
                                    <span>Rs {{ parseFloat(order.subtotal).toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-neutral-300">
                                    <span>Tax</span>
                                    <span>Rs {{ parseFloat(order.tax || 0).toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-neutral-300">
                                    <span>Discount</span>
                                    <span class="text-green-400">-Rs {{ parseFloat(order.discount || 0).toFixed(2) }}</span>
                                </div>
                                <div class="pt-3 border-t border-neutral-700 flex justify-between text-white font-bold text-lg">
                                    <span>Total</span>
                                    <span>Rs {{ parseFloat(order.total).toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="bg-neutral-800 rounded-xl border border-neutral-700 p-6 md:p-8">
                            <h2 class="text-xl font-bold text-white mb-6">Payment Method</h2>
                            
                            <form @submit.prevent="onSubmit" class="space-y-6">
                                <!-- Payment Method Options -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label
                                        v-for="method in paymentMethods"
                                        :key="method.value"
                                        class="flex items-center gap-4 p-4 rounded-lg border cursor-pointer transition"
                                        :class="form.payment_method === method.value
                                            ? 'border-primary bg-primary/10'
                                            : 'border-neutral-600 bg-neutral-700/30 hover:border-neutral-500'">
                                        <input
                                            v-model="form.payment_method"
                                            type="radio"
                                            :value="method.value"
                                            class="w-4 h-4 text-primary focus:ring-primary"
                                            required />
                                        <div class="flex-1">
                                            <p class="font-medium text-white">{{ method.label }}</p>
                                            <p class="text-sm text-neutral-400">{{ method.description }}</p>
                                        </div>
                                        <component :is="method.icon" class="w-6 h-6 text-primary" />
                                    </label>
                                </div>

                                <!-- Card Payment Form (shown when credit/debit card selected) -->
                                <div v-if="form.payment_method === 'credit_card' || form.payment_method === 'debit_card'"
                                    class="space-y-5 pt-6 border-t border-neutral-700">
                                    <h3 class="text-lg font-semibold text-white">Card Details</h3>
                                    
                                    <div>
                                        <label class="block text-neutral-300 text-sm font-medium mb-2">
                                            Card Number <span class="text-destructive">*</span>
                                        </label>
                                        <input
                                            v-model="form.card_number"
                                            type="text"
                                            maxlength="19"
                                            placeholder="1234 5678 9012 3456"
                                            @input="formatCardNumber"
                                            class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                                   text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary"
                                            required />
                                        <p v-if="form.errors.card_number" class="mt-1 text-sm text-destructive">
                                            {{ form.errors.card_number }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-neutral-300 text-sm font-medium mb-2">
                                            Card Holder Name <span class="text-destructive">*</span>
                                        </label>
                                        <input
                                            v-model="form.card_holder_name"
                                            type="text"
                                            placeholder="John Doe"
                                            class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                                   text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary"
                                            required />
                                        <p v-if="form.errors.card_holder_name" class="mt-1 text-sm text-destructive">
                                            {{ form.errors.card_holder_name }}
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="col-span-1">
                                            <label class="block text-neutral-300 text-sm font-medium mb-2">
                                                Month <span class="text-destructive">*</span>
                                            </label>
                                            <select
                                                v-model="form.expiry_month"
                                                class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                                       text-white focus:outline-none focus:ring-2 focus:ring-primary"
                                                required>
                                                <option value="">MM</option>
                                                <option v-for="month in 12" :key="month" :value="month">
                                                    {{ String(month).padStart(2, '0') }}
                                                </option>
                                            </select>
                                            <p v-if="form.errors.expiry_month" class="mt-1 text-sm text-destructive">
                                                {{ form.errors.expiry_month }}
                                            </p>
                                        </div>
                                        <div class="col-span-1">
                                            <label class="block text-neutral-300 text-sm font-medium mb-2">
                                                Year <span class="text-destructive">*</span>
                                            </label>
                                            <select
                                                v-model="form.expiry_year"
                                                class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                                       text-white focus:outline-none focus:ring-2 focus:ring-primary"
                                                required>
                                                <option value="">YYYY</option>
                                                <option v-for="year in years" :key="year" :value="year">
                                                    {{ year }}
                                                </option>
                                            </select>
                                            <p v-if="form.errors.expiry_year" class="mt-1 text-sm text-destructive">
                                                {{ form.errors.expiry_year }}
                                            </p>
                                        </div>
                                        <div class="col-span-1">
                                            <label class="block text-neutral-300 text-sm font-medium mb-2">
                                                CVV <span class="text-destructive">*</span>
                                            </label>
                                            <input
                                                v-model="form.cvv"
                                                type="text"
                                                maxlength="4"
                                                placeholder="123"
                                                class="w-full bg-neutral-700/50 border border-neutral-600 rounded-lg px-4 py-3
                                                       text-white placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary"
                                                required />
                                            <p v-if="form.errors.cvv" class="mt-1 text-sm text-destructive">
                                                {{ form.errors.cvv }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- PayPal Info -->
                                <div v-if="form.payment_method === 'paypal'"
                                    class="pt-6 border-t border-neutral-700 bg-primary/5 rounded-lg p-4">
                                    <p class="text-neutral-300 text-sm">
                                        You will be redirected to PayPal to complete your payment securely.
                                    </p>
                                </div>

                                <!-- Bank Transfer Info -->
                                <div v-if="form.payment_method === 'bank_transfer'"
                                    class="pt-6 border-t border-neutral-700 bg-primary/5 rounded-lg p-4">
                                    <p class="text-neutral-300 text-sm mb-2">
                                        Please transfer the amount to the following account:
                                    </p>
                                    <div class="text-sm text-neutral-400 space-y-1">
                                        <p><strong class="text-white">Bank:</strong> Example Bank</p>
                                        <p><strong class="text-white">Account:</strong> 1234567890</p>
                                        <p><strong class="text-white">IFSC:</strong> EXMP0001234</p>
                                    </div>
                                </div>

                                <!-- Cash on Delivery Info -->
                                <div v-if="form.payment_method === 'cash_on_delivery'"
                                    class="pt-6 border-t border-neutral-700 bg-primary/5 rounded-lg p-4">
                                    <p class="text-neutral-300 text-sm">
                                        You will pay cash when your order is delivered. An additional handling fee may apply.
                                    </p>
                                </div>

                                <!-- Security Notice -->
                                <div class="flex items-start gap-3 p-4 bg-neutral-700/30 rounded-lg">
                                    <Lock class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                                    <p class="text-neutral-300 text-sm">
                                        Your payment information is encrypted and secure. We never store your full card details.
                                    </p>
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.payment_method"
                                    class="w-full bg-primary text-white py-4 rounded-lg font-semibold text-lg
                                           hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span v-if="form.processing" class="flex items-center justify-center gap-2">
                                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing Payment...
                                    </span>
                                    <span v-else>
                                        Pay Rs {{ parseFloat(order.total).toFixed(2) }}
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Order Items -->
                    <div class="lg:col-span-1">
                        <div class="bg-neutral-800 rounded-xl border border-neutral-700 p-6 sticky top-24">
                            <h2 class="text-xl font-bold text-white mb-6">Order Items</h2>
                            
                            <div class="space-y-4 max-h-96 overflow-y-auto">
                                <div v-for="item in order.items" :key="item.id" class="flex items-center gap-4">
                                    <div class="relative flex-shrink-0">
                                        <img
                                            :src="item.product?.primary_image?.url || item.product?.image || '/images/placeholder.png'"
                                            :alt="item.product?.name"
                                            class="w-16 h-16 rounded-lg object-cover" />
                                        <span class="absolute -top-1 -right-1 bg-primary text-white text-xs rounded-full
                                                     min-w-[20px] h-5 px-1 flex items-center justify-center">
                                            {{ item.quantity }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white font-medium text-sm truncate">
                                            {{ item.product?.name }}
                                        </p>
                                        <p class="text-primary text-sm">
                                            Rs {{ parseFloat(item.price).toFixed(2) }} × {{ item.quantity }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Address -->
                            <div class="mt-6 pt-6 border-t border-neutral-700">
                                <h3 class="text-sm font-semibold text-white mb-2">Shipping Address</h3>
                                <p class="text-neutral-400 text-sm whitespace-pre-line">
                                    {{ order.shipping_address }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import PublicLayout from '@/layouts/PublicLayout.vue'
import { CreditCard, Wallet, Building2, Banknote, Lock } from 'lucide-vue-next'

const props = defineProps<{
    order: {
        id: number
        order_number: string
        subtotal: string | number
        tax: string | number
        discount: string | number
        total: string | number
        shipping_address: string
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

const paymentMethods = [
    {
        value: 'credit_card',
        label: 'Credit Card',
        description: 'Visa, Mastercard, Amex',
        icon: CreditCard,
    },
    {
        value: 'debit_card',
        label: 'Debit Card',
        description: 'Visa, Mastercard',
        icon: CreditCard,
    },
    {
        value: 'paypal',
        label: 'PayPal',
        description: 'Pay with PayPal',
        icon: Wallet,
    },
    {
        value: 'bank_transfer',
        label: 'Bank Transfer',
        description: 'Direct bank transfer',
        icon: Building2,
    },
    {
        value: 'cash_on_delivery',
        label: 'Cash on Delivery',
        description: 'Pay when delivered',
        icon: Banknote,
    },
]

const currentYear = new Date().getFullYear()
const years = Array.from({ length: 10 }, (_, i) => currentYear + i)

const form = useForm({
    order_id: props.order.id,
    payment_method: '',
    card_number: '',
    card_holder_name: '',
    expiry_month: '',
    expiry_year: '',
    cvv: '',
})

function formatCardNumber(event: Event) {
    const target = event.target as HTMLInputElement
    let value = target.value.replace(/\s/g, '')
    value = value.replace(/\D/g, '')
    
    // Add spaces every 4 digits
    const formatted = value.match(/.{1,4}/g)?.join(' ') || value
    form.card_number = formatted.substring(0, 19)
}

function onSubmit() {
    form.post(route('payment.process'), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Payment errors:', errors)
        },
    })
}
</script>
