<template>
    <div class="relative flex items-center gap-3">

        <!-- CART ICON -->
        <button ref="cartButton" @click="toggleCart" aria-label="Toggle Cart"
            class="relative px-2 py-1 rounded-full hover:bg-white/20 transition">
            <ShoppingCartIcon class="w-6 h-6" />

            <span v-if="shop.cartCount" class="absolute -top-2 -right-1 bg-primary text-white text-xs
          rounded-full min-w-[20px] h-5 px-1 flex items-center justify-center">
                {{ shop.cartCount }}
            </span>
        </button>

        <!-- WISHLIST ICON -->
        <button ref="wishlistButton" @click="toggleWishlist" aria-label="Toggle Wishlist"
            class="relative px-2 py-1 rounded-full hover:bg-white/20 transition">
            <Heart class="w-6 h-6" />

            <span v-if="shop.wishlistCount" class="absolute -top-2 -right-1 bg-primary text-white text-xs
          rounded-full min-w-[20px] h-5 px-1 flex items-center justify-center">
                {{ shop.wishlistCount }}
            </span>
        </button>

        <!-- CART POPUP -->
        <transition name="fade">
            <div ref="cartPopup" v-if="showCart" class="absolute right-0 top-full mt-3 w-80
          rounded-xl bg-neutral-900/95 backdrop-blur border border-neutral-700 shadow-2xl z-50">

                <div class="p-4 border-b border-neutral-700 text-white font-semibold">
                    Your Cart
                </div>

                <div class="max-h-72 overflow-y-auto p-4 space-y-3">
                    <p v-if="!shop.cart.length" class="text-neutral-400 text-sm">Cart is empty</p>

                    <div v-for="item in shop.cart" :key="item.id" class="flex items-center gap-3">
                        <img :src="item.image" class="w-12 h-12 rounded-lg object-cover" />
                        <div class="flex-1">
                            <p class="text-white text-sm font-medium">{{ item.name }}</p>
                            <p class="text-primary text-sm">Rs {{ item.price }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <button @click="decreaseQty(item)"
                                    class="w-6 h-6 rounded border border-neutral-600 text-white flex items-center justify-center hover:bg-white/10 transition">−</button>
                                <span class="text-white text-sm">{{ item.quantity }}</span>
                                <button @click="increaseQty(item)"
                                    class="w-6 h-6 rounded border border-neutral-600 text-white flex items-center justify-center hover:bg-white/10 transition">+</button>
                            </div>
                        </div>
                        <button @click="removeItem(item)" aria-label="Remove item"
                            class="text-red-400 hover:text-red-500 transition">
                            <TrashIcon class="w-6 h-6" />
                        </button>
                    </div>
                </div>

                <div class="p-4 border-t border-neutral-700 space-y-2">
                    <div class="flex justify-between text-white font-semibold">
                        <span>Total:</span>
                        <span>Rs {{ cartTotal }}</span>
                    </div>

                    <button @click="goToCheckout"
                        class="w-full bg-primary text-white py-2 rounded-lg font-semibold hover:bg-primary/80 transition">
                        Checkout
                    </button>
                </div>
            </div>
        </transition>

        <!-- WISHLIST POPUP -->
        <transition name="fade">
            <div ref="wishlistPopup" v-if="showWishlist" class="absolute right-0 top-full mt-3 w-80
          rounded-xl bg-neutral-900/95 backdrop-blur border border-neutral-700 shadow-2xl z-50">
                <div class="p-4 border-b border-neutral-700 text-white font-semibold">Wishlist</div>

                <div class="max-h-72 overflow-y-auto p-4 space-y-3">
                    <p v-if="!shop.wishlist.length" class="text-neutral-400 text-sm">Wishlist is empty</p>

                    <div v-for="item in shop.wishlist" :key="item.id" class="flex items-center gap-3">
                        <img :src="item.image" class="w-12 h-12 rounded-lg object-cover" />
                        <div class="flex-1">
                            <p class="text-white text-sm font-medium">{{ item.name }}</p>
                            <p class="text-primary text-sm">Rs {{ item.price }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-neutral-700">
                    <button
                        class="w-full border border-neutral-600 text-white py-2 rounded-lg hover:bg-white/10 transition">
                        View Wishlist
                    </button>
                </div>
            </div>
        </transition>

    </div>
</template>
<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useShopStore } from '@/stores/useShopStore';
import { ShoppingCartIcon, Heart, TrashIcon } from 'lucide-vue-next'

// Props for showing popups
const props = defineProps<{
    showCart: boolean
    showWishlist: boolean
}>()

const emit = defineEmits<{
    (e: 'toggle-cart', value?: boolean): void
    (e: 'toggle-wishlist', value?: boolean): void
    (e: 'checkout'): void
}>()

// Use Pinia store
const shop = useShopStore()

// Computed values
const cartCount = computed(() => shop.cartCount)
const wishlistCount = computed(() => shop.wishlistCount)
const cartTotal = computed(() => shop.cartTotal)

// Refs for popup elements
const cartPopup = ref<HTMLElement | null>(null)
const wishlistPopup = ref<HTMLElement | null>(null)
const cartButton = ref<HTMLElement | null>(null)
const wishlistButton = ref<HTMLElement | null>(null)
// Methods
function toggleCart() { emit('toggle-cart') }
function toggleWishlist() { emit('toggle-wishlist') }
function goToCheckout() { emit('checkout') }

function handleClickOutside(event: MouseEvent) {
    if (
        props.showCart &&
        cartPopup.value &&
        !cartPopup.value.contains(event.target as Node) &&
        cartButton.value &&
        !cartButton.value.contains(event.target as Node)
    ) {
        emit('toggle-cart', false)
    }

    if (
        props.showWishlist &&
        wishlistPopup.value &&
        !wishlistPopup.value.contains(event.target as Node) &&
        wishlistButton.value &&
        !wishlistButton.value.contains(event.target as Node)
    ) {
        emit('toggle-wishlist', false)
    }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))

// Cart item methods
function increaseQty(item: typeof shop.cart[number]) {
    shop.updateCartItemQuantity(item.id, item.quantity + 1)
}

function decreaseQty(item: typeof shop.cart[number]) {
    if (item.quantity > 1) shop.updateCartItemQuantity(item.id, item.quantity - 1)
}

function removeItem(item: typeof shop.cart[number]) {
    shop.removeFromCart(item.id)
}
</script>
