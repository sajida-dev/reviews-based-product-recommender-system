export interface Product {
    id: number;
    slug: string;
    name: string;
    category: string;
    price: number;
    discountedPrice: number;
    discountPercentage: number;
    mainImage: string;
    images: string[];
    inStock: boolean;
    attributes: Record<string, any>;
    isWishlisted: boolean;
}
export interface Card {
    id: number;
    title: string;
    slug: string;
    image: string;
    offer: number;
    button: string;
}

export function mapProducts(raw: any[]): Product[] {
    return raw.map(p => ({
        id: p.id,
        slug: p.slug ?? '',
        name: p.name ?? '',
        category: p.category ?? '',
        price: p.price ?? 0,
        discountedPrice: p.discountedPrice ?? 0,
        discountPercentage: p.discountPercentage ?? 0,
        mainImage: p.mainImage ?? '/img/default.png',
        images: p.images ?? [],
        inStock: p.inStock ?? false,
        attributes: p.attributes ?? {},
        isWishlisted: p.isWishlisted ?? false,
    }))
}

export function mapAdProducts(raw: any[]): Card[] {
    return raw.map(a => ({
        id: a.id,
        title: a.title ?? '',
        slug: a.slug ?? '',
        image: a.image ?? '/img/default.png',
        offer: a.offer ?? 0,
        button: a.button ?? 'SHOP NOW',
    }))
}