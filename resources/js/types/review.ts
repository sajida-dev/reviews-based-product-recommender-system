export interface ReviewCardAspect {
    aspect: string
    sentiment: string
}

export interface ReviewCardModel {
    id: number
    rating: number
    review?: string | null
    is_approved?: boolean
    spam_flagged?: boolean
    helpful_count?: number
    created_at?: string
    user?: {
        name?: string
        avatar_url?: string
    }
    aspect_sentiments?: ReviewCardAspect[]
    aspectSentiments?: ReviewCardAspect[]
}
