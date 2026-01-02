export interface MenuCategory {
    id: number
    name: string
    slug: string
    image: string | null
    products_count: number
    children: MenuCategory[]
}

export interface ResourceCollection<T> {
    data: T[]
}
