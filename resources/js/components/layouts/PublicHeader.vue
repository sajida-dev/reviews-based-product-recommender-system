<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import AppLogo from '@/components/AppLogo.vue'
import { dashboard, login, logout, register } from '@/routes'
import { Link, usePage } from '@inertiajs/vue3'
import MegaMenu from '../navigation/MegaMenu.vue'
import { useShopStore } from '@/stores/useShopStore'
import { storeToRefs } from 'pinia'
import CartWishlistWrapper from '../CartWishlistWrapper.vue'
import type { ShopState } from '@/stores/useShopStore'
import { ShoppingBasket } from 'lucide-vue-next'

const shop = useShopStore()
const { cart, wishlist } = storeToRefs(shop)

const showCart = ref(false)
const showWishlist = ref(false)
const mobileMenuOpen = ref(false)

defineProps<{
    canRegister?: boolean
}>()

const page = usePage()
const user = page.props.auth.user

function toggleCart() {
    showCart.value = !showCart.value
    showWishlist.value = false
}

function toggleWishlist() {
    showWishlist.value = !showWishlist.value
    showCart.value = false
}

function updateQuantityHandler(itemId: number, quantity: number) {
    shop.updateCartItemQuantity(itemId, quantity)
}

function removeItemHandler(itemId: number) {
    shop.removeFromCart(itemId)
}

function goToCheckoutHandler() {
    window.location.href = '/checkout'
}

function toggleMobileMenu() {
    mobileMenuOpen.value = !mobileMenuOpen.value
}

function closeMobileMenu() {
    mobileMenuOpen.value = false
    Object.keys(openSubmenus).forEach(k => openSubmenus[k] = false)
}

onMounted(() => {
    const backendShop = page.props.shop as ShopState | undefined

    if (backendShop?.cart) cart.value = backendShop.cart
    if (backendShop?.wishlist) wishlist.value = backendShop.wishlist
})
const openSubmenus = reactive<Record<string, boolean>>({})

function toggleSubmenu(key: string) {
    if (openSubmenus[key]) {
        openSubmenus[key] = false
    } else {
        Object.keys(openSubmenus).forEach(k => openSubmenus[k] = false)
        openSubmenus[key] = true
    }
}


</script>

<template>
    <header class="fixed top-0 left-0 right-0 z-50 mx-2 md:mx-auto mt-4 max-w-6xl
           rounded-xl bg-white/20 backdrop-blur-md border border-white/30
           px-6 py-3 flex items-center justify-between">
        <!-- Left: Logo + Mobile hamburger -->
        <div class="flex items-center gap-2">
            <!-- Mobile Hamburger Button -->
            <button @click="toggleMobileMenu" class="md:hidden p-2 rounded hover:bg-white/30 transition"
                aria-label="Toggle mobile menu">
                <svg v-if="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Logo -->
            <Link href="/" class="flex items-center gap-2">
                <AppLogo />
            </Link>
        </div>

        <!-- Center: Menu - Desktop only -->
        <nav class="hidden md:flex items-center gap-6 text-white/80 font-medium">
            <Link href="/" class="hover:text-white transition">Home</Link>
            <MegaMenu />
            <Link href="/features" class="hover:text-white transition">Features</Link>
            <Link href="/about" class="hover:text-white transition">About</Link>
            <Link href="/contact" class="hover:text-white transition">Contact</Link>
        </nav>

        <!-- Right: Auth + Cart + Wishlist -->
        <div class="flex items-center gap-3 relative">
            <Link v-if="user.is_admin" :href="dashboard()" class="rounded-full hidden md:flex bg-primary backdrop-blur-md
               px-5 py-2 text-sm font-semibold text-white
               hover:bg-white/30 transition">
                Dashboard
            </Link>
            <Link v-else-if="user" :href="logout()" class="rounded-full bg-primary backdrop-blur-md
               px-5 py-2 text-sm font-semibold text-white
               hover:bg-white/30 transition">
                Logout
            </Link>
            <template v-else>
                <Link :href="login()" class="rounded-full bg-primary backdrop-blur-md
                 px-5 py-2 text-sm font-semibold text-white
                 hover:bg-primary/50 transition">
                    Log in
                </Link>

                <Link v-if="canRegister" :href="register()" class="rounded-full border border-white/40
                 px-5 py-2 text-sm font-semibold text-white
                 hover:bg-white/20 transition">
                    Register
                </Link>
            </template>

            <CartWishlistWrapper :shop="shop" :showCart="showCart" :showWishlist="showWishlist"
                @toggle-cart="toggleCart" @toggle-wishlist="toggleWishlist" @update-quantity="updateQuantityHandler"
                @remove-item="removeItemHandler" @checkout="goToCheckoutHandler" />
        </div>
    </header>

    <!-- Mobile Sidebar Menu + Overlay -->
    <transition name="slide-left">
        <div v-if="mobileMenuOpen" class="fixed inset-0 z-51 flex md:hidden" role="dialog" aria-modal="true">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeMobileMenu" aria-hidden="true"></div>

            <!-- Sidebar drawer -->
            <nav
                class="relative bg-white/20 backdrop-blur-md w-72 max-w-full h-full shadow-xl p-6 flex flex-col text-white">

                <div class="flex items-center gap-4 mb-8">
                    <img :src="user.avatar_url" alt="Profile" class="w-12 h-12 rounded-full border-2 border-white/50" />
                    <div>
                        <p class="font-semibold">{{ user.name }}</p>
                        <p class="text-sm text-white/60">{{ user.email }}</p>
                    </div>
                </div>

                <ul class="flex flex-col gap-2 flex-1 overflow-auto">
                    <li>
                        <Link href="/" class="block text-white hover:text-white/90 transition" @click="closeMobileMenu">
                            Home
                        </Link>
                    </li>

                    <li>
                        <Link href="/features" class="block text-white hover:text-white/90 transition"
                            @click="closeMobileMenu">
                            Features
                        </Link>
                    </li>
                    <li>
                        <button @click="toggleSubmenu('shop')"
                            class="flex items-center justify-between w-full gap-3 hover:text-primary transition focus:outline-none">
                            <div class="flex items-center gap-3">
                                <ShoppingBasket class="w-5 h-5" />
                                Shop
                            </div>

                            <svg :class="{ 'rotate-90': openSubmenus['shop'] }"
                                class="h-4 w-4 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 6l6 6-6 6" />
                            </svg>
                        </button>

                        <!-- Submenu -->
                        <transition name="fade-slide">
                            <ul v-show="openSubmenus['shop']"
                                class="pl-5 mt-2 flex flex-col gap-2 text-sm text-white/70">
                                <li class="border-l border-l-amber-50 pl-5">
                                    <a href="#" class="hover:text-primary transition">Overview</a>
                                </li>
                                <li class="border-l border-l-amber-50 pl-5">
                                    <a href="#" class="hover:text-primary transition">Transactions</a>
                                </li>
                            </ul>
                        </transition>
                    </li>
                    <li>
                        <Link href="/about" class="block text-white hover:text-white/90 transition"
                            @click="closeMobileMenu">
                            About
                        </Link>
                    </li>
                    <li>
                        <Link href="/contact" class="block text-white hover:text-white/90 transition"
                            @click="closeMobileMenu">
                            Contact
                        </Link>
                    </li>
                    <!-- Auth links -->
                    <li v-if="user.is_admin">
                        <Link :href="dashboard()"
                            class="block rounded w-full bg-primary px-4 py-2 font-semibold text-white text-center hover:bg-white/30 transition"
                            @click="closeMobileMenu">
                            Dashboard
                        </Link>
                    </li>
                    <li v-if="user">
                        <Link :href="logout()"
                            class="block rounded w-full bg-primary px-4 py-2 font-semibold text-white text-center hover:bg-white/30 transition"
                            @click="closeMobileMenu">
                            Logout
                        </Link>
                    </li>
                    <template v-else>
                        <li>
                            <Link :href="login()"
                                class="block rounded bg-primary px-4 py-2 font-semibold text-white text-center hover:bg-primary/50 transition"
                                @click="closeMobileMenu">
                                Log in
                            </Link>
                        </li>
                        <li v-if="canRegister">
                            <Link :href="register()"
                                class="block rounded border border-white/40 px-4 py-2 font-semibold text-white text-center hover:bg-white/20 transition"
                                @click="closeMobileMenu">
                                Register
                            </Link>
                        </li>
                    </template>
                </ul>

                <!-- Upgrade card (shown only on mobile sidebar) -->
                <div class="mt-auto bg-primary/90 rounded-lg p-4 flex flex-col items-center gap-2 text-white text-center"
                    style="backdrop-filter: blur(10px);">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Upgrade Pro"
                        class="w-14 h-14 rounded-full border-2 border-white" />
                    <div class="text-sm font-semibold">Upgrade to PRO</div>
                    <div class="text-xs opacity-80">
                        One year support, monthly updates for up to 5 team members.
                    </div>
                </div>
            </nav>
        </div>
    </transition>
</template>

<style scoped>
/* Slide animation for mobile sidebar */
.slide-left-enter-active,
.slide-left-leave-active {
    transition: transform 0.3s ease;
}

.slide-left-enter-from,
.slide-left-leave-to {
    transform: translateX(-100%);
}

.slide-left-enter-to,
.slide-left-leave-from {
    transform: translateX(0);
}

/* Fade animation for your existing fade */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
