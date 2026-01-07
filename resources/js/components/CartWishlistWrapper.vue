<template>
    <div class="relative flex items-center gap-3">

        <!-- CART ICON -->
        <button @click="toggleCart" aria-label="Toggle Cart"
            class="relative px-2 py-1 rounded-full hover:bg-white/20 transition">
            <i class="fa-solid fa-cart-shopping text-white text-lg"></i>

            <span v-if="shop.cartCount" class="absolute -top-2 -right-1 bg-primary text-white text-xs
          rounded-full min-w-[20px] h-5 px-1 flex items-center justify-center">
                {{ shop.cartCount }}
            </span>
        </button>

        <!-- WISHLIST ICON -->
        <button @click="toggleWishlist" aria-label="Toggle Wishlist"
            class="relative px-2 py-1 rounded-full hover:bg-white/20 transition">
            <i class="fa-solid fa-heart text-white text-lg"></i>

            <span v-if="shop.wishlistCount" class="absolute -top-2 -right-1 bg-primary text-white text-xs
          rounded-full min-w-[20px] h-5 px-1 flex items-center justify-center">
                {{ shop.wishlistCount }}
            </span>
        </button>

        <!-- CART POPUP -->
        <transition name="fade">
            <div v-if="showCart" class="absolute right-0 top-full mt-3 w-80
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
                            class="text-white hover:text-red-500 transition">
                            <i class="fa-solid fa-trash"></i>
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
            <div v-if="showWishlist" class="absolute right-0 top-full mt-3 w-80
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

<script>
export default {
    props: {
        shop: Object,
        showCart: Boolean,
        showWishlist: Boolean,

    },
    computed: {
        cartCount() {
            return this.shop.cart.reduce((sum, item) => sum + item.quantity, 0);
        },
        wishlistCount() {
            return this.shop.wishlist.length;
        },
        cartTotal() {
            return this.shop.cart.reduce((total, item) => total + item.price * item.quantity, 0);
        }
    },
    methods: {
        toggleCart() {
            this.$emit('toggle-cart');
        },
        toggleWishlist() {
            this.$emit('toggle-wishlist');
        },
        increaseQty(item) {
            const cartItem = this.shop.cart.find(i => i.id === item.id);
            if (cartItem) {
                cartItem.quantity++;
                this.$emit('update-quantity', item.id, cartItem.quantity);
            }
        },
        decreaseQty(item) {
            const cartItem = this.shop.cart.find(i => i.id === item.id);
            if (cartItem && cartItem.quantity > 1) {
                cartItem.quantity--;
                this.$emit('update-quantity', item.id, cartItem.quantity);
            }
        },
        removeItem(item) {
            this.shop.cart = this.shop.cart.filter(i => i.id !== item.id);
            this.$emit('remove-item', item.id);
        },
        goToCheckout() {
            this.$emit('checkout');
        }
    }
}
</script>
