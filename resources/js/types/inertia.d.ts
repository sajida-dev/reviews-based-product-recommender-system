import type { MenuCategory, ResourceCollection } from '@/types/menu'

declare module '@inertiajs/core' {
    interface PageProps {
        menuMega: {
            new: ResourceCollection<MenuCategory>
            trending: ResourceCollection<MenuCategory>
            popular: ResourceCollection<MenuCategory>
        }
    }
}
