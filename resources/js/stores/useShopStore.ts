import { defineStore } from 'pinia'
import { router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'

/* ------------------ TYPES ------------------ */

export interface CartItem {
    id: number
    productId: number
    name: string
    price: number
    image: string
    quantity: number
}

export interface Product {
    id: number
    name: string
    price: number
    image?: string
}

export interface ShopState {
    cart: CartItem[]
    wishlist: Product[]
}

/* ------------------ STORE ------------------ */

export const useShopStore = defineStore('shop', {
    state: (): ShopState => ({
        cart: [],
        wishlist: [],
    }),

    getters: {
        cartCount: (state) =>
            state.cart.reduce((sum, item) => sum + item.quantity, 0),

        cartTotal: (state) =>
            state.cart.reduce((sum, item) => sum + item.price * item.quantity, 0),

        wishlistCount: (state) => state.wishlist.length,
    },

    actions: {
        checkAuth(): boolean {
            const page = usePage()
            if (!(page.props.auth as any)?.user) {
                toast.error('Please login to continue')
                return false
            }
            return true
        },

        /* ------------------ CART ------------------ */

        async addToCart(product: Product) {
            if (!this.checkAuth()) return

            router.post(
                route('cart.store'),
                { product_id: product.id, quantity: 1 },
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (page) => {
                        // 🔥 Always sync cart from backend
                        this.setCartFromServer(
                            (page.props as any).shop.cart
                        )
                        toast.success('Product added to cart')
                    },
                    onError: (errors: any) => {
                        const msg = Object.values(errors)[0] as string
                        toast.error(msg || 'Could not add to cart')
                    },
                }
            )
        },

        async removeFromCart(itemId: number) {
            if (!this.checkAuth()) return

            router.delete(route('cart.destroy', itemId), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (page) => {
                    this.setCartFromServer(
                        (page.props as any).shop.cart
                    )
                    toast.success('Product removed')
                },
                onError: () => {
                    toast.error('Remove failed')
                },
            })
        },

        async updateCartItemQuantity(itemId: number, quantity: number) {
            if (!this.checkAuth()) return

            if (quantity < 1) {
                await this.removeFromCart(itemId)
                return
            }

            router.put(
                route('cart.updateQty', { itemId }),
                { quantity },
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (page) => {
                        this.setCartFromServer(
                            (page.props as any).shop.cart
                        )
                        toast.success('Quantity updated')
                    },
                    onError: (errors: any) => {
                        const msg = Object.values(errors)[0] as string
                        toast.error(msg || 'Update failed')
                    },
                }
            )
        },

        /* ------------------ WISHLIST ------------------ */

        async toggleWishlist(product: Product) {
            if (!this.checkAuth()) return

            router.post(
                route('wishlist.toggle'),
                { product_id: product.id },
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (page) => {
                        this.setWishlistFromServer(
                            (page.props as any).shop.wishlist
                        )
                        toast.success('Wishlist updated')
                    },
                    onError: () => {
                        toast.error('Wishlist update failed')
                    },
                }
            )
        },

        setCartFromServer(cartItems: CartItem[]) {
            this.cart = cartItems
        },

        setWishlistFromServer(products: Product[]) {
            this.wishlist = products
        },
    },
})
