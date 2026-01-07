import type { PageProps as InertiaPageProps } from '@inertiajs/core'
import type { Product } from './product'

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps {
        products?: {
            data: Product[]
            meta: any
            links: any
        }
    }
}
