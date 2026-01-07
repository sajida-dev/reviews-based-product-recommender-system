import type { MenuCategory, ResourceCollection } from '@/types/menu'

declare module '@inertiajs/core' {
    interface PageProps {
        menuMega: {
            new: ResourceCollection<MenuCategory>
            trending: ResourceCollection<MenuCategory>
            popular: ResourceCollection<MenuCategory>
        }
        products?: {
            id: number
            name: string
            price: number
            slug?: string
            images?: string[]
            discountPercentage?: number
            inStock?: boolean
        }[]
    }
}
