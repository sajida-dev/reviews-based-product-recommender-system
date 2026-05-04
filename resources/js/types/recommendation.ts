export interface TopAspect {
    aspect: string
    sentiment: string
    label: string
}

export interface BecauseYouLiked {
    id: number
    name: string
    slug: string
}

export interface RecommendationProduct {
    id: number
    slug: string
    name: string
    category?: string | null
    price: number
    discount_price?: number | null
    effective_price: number
    discount_percentage: number
    main_image: string
    in_stock: boolean
    rating_avg: number
    rating_count: number
    top_aspects: TopAspect[]
    similarity_score?: number | null
    because_you_liked?: BecauseYouLiked | null
    matching_aspects: string[]
    recommended_for_you: boolean
    /** similar | for_you | trending | popular */
    recommendation_badge?: string
}
