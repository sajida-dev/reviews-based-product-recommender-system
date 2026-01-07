import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'

export interface CartItem {
    id: number
    name: string
    price: number
    image: string
    quantity: number
}

export interface Product {
    id: number
    name: string
    price: number
    image: string
}

export interface ShopState {
    cart: CartItem[]
    wishlist: Product[]
}

export const useShopStore = defineStore('shop', {
    state: (): ShopState => ({
        cart: [],
        wishlist: [],
    }),
    getters: {
        cartCount: (state): number =>
            state.cart.reduce((sum, i) => sum + i.quantity, 0),
        wishlistCount: (state): number => state.wishlist.length,
    },

    actions: {
        async addToCart(product: {
            id: number
            name: string
            price: number
            image: string
        }) {
            const existing = this.cart.find(i => i.id === product.id)

            // Optimistic UI
            if (existing) {
                existing.quantity++
            } else {
                this.cart.push({ ...product, quantity: 1 })
            }

            try {
                await router.post('/cart', {
                    product_id: product.id,
                    quantity: 1,
                }, {
                    preserveScroll: true,
                    onError: () => {
                        // rollback
                        if (existing) {
                            existing.quantity--
                        } else {
                            this.cart = this.cart.filter(i => i.id !== product.id)
                        }
                    }
                })
            } catch (e) {
                // just in case
            }
        },

        async removeFromCart(productId: number) {
            const backup = [...this.cart]
            this.cart = this.cart.filter(i => i.id !== productId)

            try {
                await router.delete(`/cart/remove/${productId}`, {
                    preserveScroll: true,
                    onError: () => {
                        this.cart = backup
                    }
                })
            } catch (e) { }
        },

        async toggleWishlist(product: {
            id: number
            name: string
            price: number
            image: string
        }) {
            const exists = this.wishlist.some(i => i.id === product.id)

            // Update wishlist
            if (exists) this.wishlist = this.wishlist.filter(i => i.id !== product.id);
            else this.wishlist.push(product);

            try {
                await router.post('/wishlist/toggle', { product_id: product.id }, {
                    preserveScroll: true,
                    onError: () => {
                        if (exists) this.wishlist.push(product);
                        else this.wishlist = this.wishlist.filter(i => i.id !== product.id);
                    }
                });
            } catch (e) { }
        },
        async updateCartItemQuantity(productId: number, quantity: number) {
            const item = this.cart.find(i => i.id === productId)
            if (!item) return

            const oldQuantity = item.quantity
            item.quantity = quantity

            try {
                await router.put(`/cart/update/${productId}`, {
                    quantity: quantity
                }, {
                    preserveScroll: true,
                    onError: () => {

                        item.quantity = oldQuantity
                    }
                })
            } catch (e) {

                item.quantity = oldQuantity
            }
        },
    }
})
