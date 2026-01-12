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
        cartCount: (state) => state.cart.reduce((sum, i) => sum + i.quantity, 0),
        wishlistCount: (state) => state.wishlist.length,
    },
    actions: {
        async addToCart(product: Product) {
            const existing = this.cart.find(i => i.id === product.id)
            if (existing) existing.quantity++
            else this.cart.push({ ...product, quantity: 1 })

            try {
                await router.post(route('cart.store'), { product_id: product.id, quantity: 1 }, {
                    preserveState: true,
                    onError: () => {
                        if (existing) existing.quantity--
                        else this.cart = this.cart.filter(i => i.id !== product.id)
                    }
                })
            } catch { }
        },

        async removeFromCart(productId: number) {
            const backup = [...this.cart]
            this.cart = this.cart.filter(i => i.id !== productId)
            try {
                await router.delete(route('cart.destroy', productId), { preserveState: true, onError: () => { this.cart = backup } })
            } catch { }
        },

        async updateCartItemQuantity(productId: number, quantity: number) {
            const item = this.cart.find(i => i.id === productId)
            if (!item) return
            const oldQuantity = item.quantity
            item.quantity = quantity

            try {
                await router.put(route('cart.update', productId), { quantity }, {
                    preserveState: true,
                    onError: () => { item.quantity = oldQuantity }
                })
            } catch { item.quantity = oldQuantity }
        },

        async toggleWishlist(product: Product) {
            const exists = this.wishlist.some(i => i.id === product.id)
            if (exists) this.wishlist = this.wishlist.filter(i => i.id !== product.id)
            else this.wishlist.push(product)

            try {
                await router.post(route('wishlist.toggle'), { product_id: product.id }, {
                    preserveState: true,
                    onError: () => {
                        if (exists) this.wishlist.push(product)
                        else this.wishlist = this.wishlist.filter(i => i.id !== product.id)
                    }
                })
            } catch { }
        }
    }
})
