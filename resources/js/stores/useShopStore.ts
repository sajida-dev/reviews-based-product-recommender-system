import { defineStore } from 'pinia'
import { router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'

/* ------------------ TYPES ------------------ */
// Defining these here to fix the "@import '@/product'" error
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
        cartCount: (state: ShopState): number =>
            state.cart.reduce((sum: number, item: CartItem) => sum + item.quantity, 0),

        cartTotal: (state: ShopState): number =>
            state.cart.reduce((sum: number, item: CartItem) => sum + item.price * item.quantity, 0),

        wishlistCount: (state: ShopState): number => state.wishlist.length,
    },

    actions: {
        checkAuth(): boolean {
            const page = usePage();
            if (!(page.props.auth as any).user) {
                toast.error('Please login to continue');
                return false;
            }
            return true;
        },

        /* ------------------ CART ------------------ */

        async addToCart(product: Product) {
            if (!this.checkAuth()) return;

            let existing = this.cart.find((i: CartItem) => i.productId === product.id)

            if (existing) {
                existing.quantity++
            } else {
                existing = {
                    id: Date.now() * -1,
                    productId: product.id,
                    name: product.name,
                    image: product.image || '',
                    price: product.price,
                    quantity: 1,
                }
                this.cart.push(existing)
            }

            router.post(
                route('cart.store'),
                { product_id: product.id, quantity: 1 },
                {
                    preserveState: true,
                    onSuccess: (page) => {
                        const shopCart = (page.props as any).shop.cart as any[]
                        const backendItem = shopCart.find((i: any) => i.productId === product.id)
                        
                        if (backendItem?.id && existing) {
                            existing.id = backendItem.id
                        }
                        toast.success('Product added to cart')
                    },
                    onError: (errors: any) => {
                        if (existing!.quantity > 1) {
                            existing!.quantity--
                        } else {
                            this.cart = this.cart.filter((i: CartItem) => i.id !== existing!.id)
                        }
                        const msg = Object.values(errors)[0] as string;
                        toast.error(msg || 'Could not add to cart');
                    },
                }
            )
        },

        async removeFromCart(itemId: number) {
            if (!this.checkAuth()) return;

            const backup = [...this.cart]
            this.cart = this.cart.filter((i: CartItem) => i.id !== itemId)

            router.delete(route('cart.destroy', itemId), {
                preserveState: true,
                onSuccess: () => toast.success('Product removed'),
                onError: () => {
                    this.cart = backup
                    toast.error('Remove failed')
                },
            })
        },

        async updateCartItemQuantity(itemId: number, quantity: number) {
            if (!this.checkAuth()) return;

            const item = this.cart.find((i: CartItem) => i.id === itemId)
            if (!item) return

            if (quantity < 1) {
                await this.removeFromCart(itemId)
                return
            }

            const oldQty = item.quantity
            item.quantity = quantity

            router.put(
                route('cart.updateQty', { itemId }),
                { quantity },
                {
                    preserveState: true,
                    onSuccess: () => toast.success('Quantity updated'),
                    onError: (errors: any) => {
                        item.quantity = oldQty
                        const msg = Object.values(errors)[0] as string;
                        toast.error(msg || 'Update failed');
                    },
                }
            )
        },

        /* ------------------ WISHLIST ------------------ */

        async toggleWishlist(product: Product) {
            if (!this.checkAuth()) return;

            const exists = this.wishlist.some((i: Product) => i.id === product.id)
            const backup = [...this.wishlist]

            if (exists) {
                this.wishlist = this.wishlist.filter((i: Product) => i.id !== product.id)
            } else {
                this.wishlist.push(product)
            }

            router.post(
                route('wishlist.toggle'),
                { product_id: product.id },
                {
                    preserveState: true,
                    onSuccess: () => toast.success('Wishlist updated'),
                    onError: () => {
                        this.wishlist = backup
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
        }
    },
})