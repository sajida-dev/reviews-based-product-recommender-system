import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'
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

/* ------------------ HELPER ------------------ */
/**
 * Generic optimistic UI handler
 */
function optimisticRequest<T>({
    optimisticUpdate,
    rollback,
    request,
}: {
    optimisticUpdate: () => T
    rollback: (backup: T) => void
    request: (onError: () => void) => void
}) {
    const backup = optimisticUpdate()

    request(() => {
        rollback(backup)
    })
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
        /* ------------------ CART ------------------ */

        addToCart(product: Product) {
            optimisticRequest({
                optimisticUpdate: () => {
                    let existing = this.cart.find(i => i.productId === product.id)

                    if (existing) {
                        existing.quantity++
                        return existing
                    }

                    const tempItem: CartItem = {
                        id: Date.now() * -1,
                        productId: product.id,
                        name: product.name,
                        image: product.image || '',
                        price: product.price,
                        quantity: 1,
                    }

                    this.cart.push(tempItem)
                    return tempItem
                },

                rollback: (item) => {
                    if (item.quantity > 1) item.quantity--
                    else this.cart = this.cart.filter(i => i.id !== item.id)

                    toast.error('Product could not be added to cart')
                },

                request: (onError) => {
                    router.post(
                        route('cart.store'),
                        { product_id: product.id, quantity: 1 },
                        {
                            preserveState: true,
                            preserveScroll: true,
                            onSuccess: (page) => {
                                const serverItem = (page.props as any).shop.cart
                                    .find((i: any) => i.productId === product.id)

                                if (serverItem) {
                                    const local = this.cart.find(
                                        i => i.productId === product.id
                                    )
                                    if (local) {
                                        local.id = serverItem.id
                                        local.quantity = serverItem.quantity
                                    }
                                }
                                toast.success('Product added')
                            },
                            onError,
                        }
                    )
                },
            })
        },


        removeFromCart(itemId: number) {
            optimisticRequest({
                optimisticUpdate: () => {
                    const backup = [...this.cart]
                    this.cart = this.cart.filter(i => i.id !== itemId)
                    return backup
                },

                rollback: (backup) => {
                    this.cart = backup
                    toast.error('Remove failed')
                },

                request: (onError) => {
                    router.delete(route('cart.destroy', itemId), {
                        preserveState: true,
                        preserveScroll: true,
                        onSuccess: () => toast.success('Removed'),
                        onError,
                    })
                },
            })
        },

        updateCartItemQuantity(itemId: number, quantity: number) {
            const item = this.cart.find(i => i.id === itemId)
            if (!item) return

            if (quantity < 1) {
                this.removeFromCart(itemId)
                return
            }

            optimisticRequest({
                optimisticUpdate: () => {
                    const oldQty = item.quantity
                    item.quantity = quantity
                    return oldQty
                },

                rollback: (oldQty) => {
                    item.quantity = oldQty
                    toast.error('Update failed')
                },

                request: (onError) => {
                    router.put(
                        route('cart.updateQty', { itemId }),
                        { quantity },
                        {
                            preserveState: true,
                            preserveScroll: true,
                            onSuccess: () => toast.success('Quantity updated'),
                            onError,
                        }
                    )
                },
            })
        },

        setCartFromServer(cartItems: CartItem[]) {
            this.cart = cartItems
        },

        /* ------------------ WISHLIST ------------------ */

        toggleWishlist(product: Product) {
            optimisticRequest({
                optimisticUpdate: () => {
                    const exists = this.wishlist.some(i => i.id === product.id)

                    if (exists) {
                        this.wishlist = this.wishlist.filter(i => i.id !== product.id)
                    } else {
                        this.wishlist.push(product)
                    }

                    return exists
                },

                rollback: (wasExists) => {
                    if (wasExists) this.wishlist.push(product)
                    else this.wishlist = this.wishlist.filter(i => i.id !== product.id)

                    toast.error('Wishlist update failed')
                },

                request: (onError) => {
                    router.post(
                        route('wishlist.toggle'),
                        { product_id: product.id },
                        {
                            preserveState: true,
                            preserveScroll: true,
                            onSuccess: () => toast.success('Wishlist updated'),
                            onError,
                        }
                    )
                },
            })
        },
    },
})
