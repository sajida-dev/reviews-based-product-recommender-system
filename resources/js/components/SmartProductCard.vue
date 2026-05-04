<template>
    <article
        class="group relative flex flex-col overflow-hidden rounded-2xl border bg-white/5 shadow-lg backdrop-blur-md transition hover:-translate-y-0.5 hover:shadow-xl"
        :class="cardRingClass"
    >
        <span
            v-if="badgeLabel"
            class="absolute right-3 top-3 z-20 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide shadow-md"
            :class="badgeClass"
        >
            {{ badgeLabel }}
        </span>

        <Link :href="`/products/${product.slug}`" class="relative block aspect-[4/3] overflow-hidden bg-neutral-900/40">
            <img
                :src="imgSrc"
                :alt="product.name"
                loading="lazy"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                @error="onImgError"
            />
            <span
                v-if="product.discount_percentage"
                class="absolute left-3 top-3 rounded-md bg-primary px-2 py-1 text-xs font-semibold text-white"
            >
                -{{ product.discount_percentage }}%
            </span>
        </Link>

        <div class="flex flex-1 flex-col gap-3 p-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-primary/90">
                    {{ product.category || 'Product' }}
                </p>
                <Link :href="`/products/${product.slug}`" class="mt-1 line-clamp-2 text-base font-semibold text-white hover:text-primary">
                    {{ product.name }}
                </Link>
            </div>

            <div class="flex flex-wrap gap-1.5">
                <span
                    v-for="a in (product.top_aspects || []).slice(0, 2)"
                    :key="a.aspect + a.sentiment"
                    class="rounded-full border border-white/20 bg-white/10 px-2 py-0.5 text-[11px] text-gray-100"
                >
                    {{ a.label }}
                </span>
            </div>

            <div class="flex items-end justify-between gap-2">
                <div>
                    <p v-if="product.discount_percentage" class="text-xs text-gray-400 line-through">Rs {{ product.price.toFixed(0) }}</p>
                    <p class="text-lg font-bold text-white">Rs {{ product.effective_price.toFixed(0) }}</p>
                    <p class="text-xs text-gray-400">
                        ★ {{ product.rating_avg.toFixed(1) }}
                        <span class="text-gray-500">({{ product.rating_count }})</span>
                    </p>
                </div>
                <span
                    class="rounded-md px-2 py-1 text-[11px] font-semibold"
                    :class="product.in_stock ? 'bg-emerald-500/20 text-emerald-300' : 'bg-red-500/20 text-red-300'"
                >
                    {{ product.in_stock ? 'In stock' : 'Out of stock' }}
                </span>
            </div>

            <div class="mt-auto flex gap-2">
                <Link
                    :href="`/products/${product.slug}`"
                    class="flex-1 rounded-xl border border-white/20 py-2.5 text-center text-sm font-semibold text-white transition hover:border-primary hover:bg-primary/20 hover:text-white"
                >
                    View details
                </Link>
                <button
                    type="button"
                    class="rounded-xl bg-white/10 px-3 py-2 text-sm transition hover:bg-primary hover:text-white"
                    :aria-pressed="wishlisted"
                    @click="toggleWishlist"
                >
                    <Heart class="h-5 w-5" :class="wishlisted ? 'fill-primary text-primary' : 'text-gray-200'" />
                </button>
            </div>
        </div>
    </article>
</template>

<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3'
import { Heart } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'
import type { RecommendationProduct } from '@/types/recommendation'

const PLACEHOLDER = '/img/default.png'

const props = defineProps<{
    product: RecommendationProduct
    initiallyWishlisted?: boolean
}>()

const page = usePage()
const authed = computed(() => !!page.props.auth?.user)

const imgSrc = ref(props.product.main_image || PLACEHOLDER)

watch(
    () => props.product.main_image,
    (v) => {
        imgSrc.value = v || PLACEHOLDER
    },
)

function onImgError() {
    imgSrc.value = PLACEHOLDER
}

const badgeLabel = computed(() => {
    const b = props.product.recommendation_badge
    if (props.product.recommended_for_you || b === 'for_you') return 'Recommended for you'
    if (b === 'similar') return 'Similar'
    if (b === 'trending') return 'Trending'
    if (b === 'popular') return 'Popular'
    return null
})

const badgeClass = computed(() => {
    const b = props.product.recommendation_badge
    if (props.product.recommended_for_you || b === 'for_you') return 'bg-amber-500 text-white'
    if (b === 'similar') return 'bg-violet-600 text-white'
    return 'bg-primary text-white'
})

const cardRingClass = computed(() => {
    const b = props.product.recommendation_badge
    if (props.product.recommended_for_you || b === 'for_you') return 'border-amber-400/50 ring-1 ring-amber-400/30'
    if (b === 'similar') return 'border-violet-400/40 ring-1 ring-violet-400/25'
    return 'border-white/15 ring-1 ring-white/10 hover:border-primary/40'
})

const wishlisted = ref(props.initiallyWishlisted ?? false)

watch(
    () => props.initiallyWishlisted,
    (v) => {
        if (v !== undefined) wishlisted.value = v
    },
)

function toggleWishlist() {
    if (!authed.value) {
        router.visit('/login')
        return
    }
    wishlisted.value = !wishlisted.value
    router.post(
        '/wishlist/toggle',
        { product_id: props.product.id },
        { preserveScroll: true, preserveState: true },
    )
}
</script>
