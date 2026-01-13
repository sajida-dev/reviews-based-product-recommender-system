import type { Product } from '@/types/product'

declare module '@inertiajs/core' {
    interface PageProps {
        menuMega: {
            new: ResourceCollection<MenuCategory>
            trending: ResourceCollection<MenuCategory>
            popular: ResourceCollection<MenuCategory>
        }
        products?: {
            current_page: number
            data: Partial<Product>[]  // array of products inside "data"
            first_page_url: string
            from: number
            last_page: number
            last_page_url: string
            next_page_url: string | null
            path: string
            per_page: number
            prev_page_url: string | null
            to: number
            total: number
        }
    }
}
