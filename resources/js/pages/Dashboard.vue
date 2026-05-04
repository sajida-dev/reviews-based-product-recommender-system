<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import SmartProductCard from '@/components/SmartProductCard.vue'
import RecommendationExplainerCard from '@/components/RecommendationExplainerCard.vue'
import { dashboard } from '@/routes'
import type { BreadcrumbItem } from '@/types'
import type { RecommendationProduct } from '@/types/recommendation'
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage<{
    recentlyViewed: RecommendationProduct[]
    recommendedForYou: RecommendationProduct[]
    wishlist: { id: number; product: Record<string, unknown> }[]
    yourReviews: {
        id: number
        rating: number
        text: string | null
        is_approved: boolean
        aspects: { aspect: string; sentiment: string }[]
        product: { id: number; name: string; slug: string } | null
    }[]
    interestProfile: {
        preferred_categories: unknown[]
        preference_score: number
        last_interest_update: string | null
    }
    activityTimeline: { type: string; label: string; at: string | null }[]
}>()

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: dashboard().url }]

const recent = computed(() => page.props.recentlyViewed ?? [])
const recommended = computed(() => page.props.recommendedForYou ?? [])
const wishlist = computed(() => page.props.wishlist ?? [])
const reviews = computed(() => page.props.yourReviews ?? [])
const profile = computed(() => page.props.interestProfile)
const timeline = computed(() => page.props.activityTimeline ?? [])
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-8 overflow-x-auto rounded-xl p-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Your space</h1>
                <p class="text-sm text-gray-400">Recently viewed, picks for you, and your reviews.</p>
            </div>

            <section v-if="recommended.length" class="space-y-3">
                <h2 class="text-lg font-semibold text-white">Recommended for you</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <RecommendationExplainerCard v-for="p in recommended" :key="p.id" :product="p" />
                </div>
            </section>

            <section v-if="recent.length" class="space-y-3">
                <h2 class="text-lg font-semibold text-white">Recently viewed</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <SmartProductCard v-for="p in recent" :key="p.id" :product="p" />
                </div>
            </section>

            <section v-if="wishlist.length" class="space-y-3">
                <h2 class="text-lg font-semibold text-white">Wishlist</h2>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="row in wishlist"
                        :key="row.id"
                        class="rounded-xl border border-white/15 bg-white/5 p-4 backdrop-blur-md"
                    >
                        <a :href="`/products/${row.product.slug}`" class="font-medium text-white hover:text-primary">
                            {{ row.product.name }}
                        </a>
                        <p class="mt-1 text-sm text-gray-400">Rs {{ row.product.effective_price }}</p>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-xl border border-white/15 bg-white/5 p-5 backdrop-blur-md">
                    <h2 class="mb-3 text-lg font-semibold text-white">Your interests</h2>
                    <p class="text-sm text-gray-400">Preference score: {{ profile?.preference_score?.toFixed?.(2) ?? '—' }}</p>
                    <p v-if="profile?.last_interest_update" class="mt-1 text-xs text-gray-500">
                        Updated {{ new Date(profile.last_interest_update).toLocaleString() }}
                    </p>
                    <p v-if="(profile?.preferred_categories?.length ?? 0) > 0" class="mt-3 text-sm text-gray-200">
                        Suggested categories: {{ (profile?.preferred_categories as string[]).join(', ') }}
                    </p>
                    <p v-else class="mt-3 text-sm text-gray-500">Leave more reviews to refine your taste profile.</p>
                </section>

                <section class="rounded-xl border border-white/15 bg-white/5 p-5 backdrop-blur-md">
                    <h2 class="mb-3 text-lg font-semibold text-white">Recent activity</h2>
                    <ul v-if="timeline.length" class="space-y-2 text-sm text-gray-300">
                        <li v-for="(t, i) in timeline" :key="i" class="flex justify-between gap-2 border-b border-white/10 py-2">
                            <span>{{ t.label }}</span>
                            <span class="shrink-0 text-xs text-gray-500">{{ t.at ? new Date(t.at).toLocaleString() : '' }}</span>
                        </li>
                    </ul>
                    <p v-else class="text-sm text-gray-500">No recent views yet.</p>
                </section>
            </div>

            <section v-if="reviews.length" class="space-y-3">
                <h2 class="text-lg font-semibold text-white">Your reviews</h2>
                <div class="grid gap-3 md:grid-cols-2">
                    <article
                        v-for="r in reviews"
                        :key="r.id"
                        class="rounded-xl border border-white/15 bg-white/5 p-4 text-sm text-gray-200 backdrop-blur-md"
                    >
                        <div class="mb-2 flex items-center justify-between">
                            <span class="font-medium text-white">{{ r.product?.name }}</span>
                            <span class="text-amber-300">★ {{ r.rating }}</span>
                        </div>
                        <p class="line-clamp-3">{{ r.text || '—' }}</p>
                        <div v-if="r.aspects?.length" class="mt-2 flex flex-wrap gap-1">
                            <span
                                v-for="a in r.aspects"
                                :key="a.aspect + a.sentiment"
                                class="rounded-full bg-white/10 px-2 py-0.5 text-[11px] text-gray-100"
                            >
                                {{ a.aspect }} · {{ a.sentiment }}
                            </span>
                        </div>
                        <p class="mt-2 text-xs" :class="r.is_approved ? 'text-emerald-400' : 'text-amber-300'">
                            {{ r.is_approved ? 'Published' : 'Pending moderation' }}
                        </p>
                    </article>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
