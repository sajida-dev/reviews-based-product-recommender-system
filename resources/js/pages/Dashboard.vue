<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import SmartProductCard from '@/components/SmartProductCard.vue'
import RecommendationExplainerCard from '@/components/RecommendationExplainerCard.vue'
import { dashboard } from '@/routes'
import type { BreadcrumbItem } from '@/types'
import type { RecommendationProduct } from '@/types/recommendation'
import { Head, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage<{
    insights: {
        total_spend: number
        total_orders: number
        completed_orders: number
        cancelled_orders: number
        avg_order_value: number
        items_purchased: number
        wishlist_count: number
        review_count: number
    }
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
    recentOrders: {
        id: number
        order_number: string
        total: number
        status: string
        created_at: string | null
    }[]
}>()

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: dashboard().url }]

const activeTab = ref<'insights' | 'recommendations' | 'activity'>('insights')
const insights = computed(() => page.props.insights)
const recent = computed(() => page.props.recentlyViewed ?? [])
const recommended = computed(() => page.props.recommendedForYou ?? [])
const wishlist = computed(() => page.props.wishlist ?? [])
const reviews = computed(() => page.props.yourReviews ?? [])
const profile = computed(() => page.props.interestProfile)
const timeline = computed(() => page.props.activityTimeline ?? [])
const recentOrders = computed(() => page.props.recentOrders ?? [])
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Track your spending insights, activity, and recommendations.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <button
                    class="rounded-lg border px-3 py-1.5 text-sm transition"
                    :class="activeTab === 'insights' ? 'border-primary bg-primary/10 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-primary dark:border-neutral-700 dark:bg-neutral-900 dark:text-gray-300'"
                    @click="activeTab = 'insights'"
                >
                    Insights
                </button>
                <button
                    class="rounded-lg border px-3 py-1.5 text-sm transition"
                    :class="activeTab === 'recommendations' ? 'border-primary bg-primary/10 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-primary dark:border-neutral-700 dark:bg-neutral-900 dark:text-gray-300'"
                    @click="activeTab = 'recommendations'"
                >
                    Recommendations
                </button>
                <button
                    class="rounded-lg border px-3 py-1.5 text-sm transition"
                    :class="activeTab === 'activity' ? 'border-primary bg-primary/10 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-primary dark:border-neutral-700 dark:bg-neutral-900 dark:text-gray-300'"
                    @click="activeTab = 'activity'"
                >
                    Activity
                </button>
            </div>

            <section v-if="activeTab === 'insights'" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Total spend</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Rs {{ insights.total_spend.toFixed(2) }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Completed orders</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ insights.completed_orders }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total orders: {{ insights.total_orders }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Average order value</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Rs {{ insights.avg_order_value.toFixed(2) }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Items purchased</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ insights.items_purchased }}</p>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                        <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Recent orders</h2>
                        <ul v-if="recentOrders.length" class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <li
                                v-for="o in recentOrders"
                                :key="o.id"
                                class="flex items-center justify-between gap-2 border-b border-gray-200 py-2 dark:border-neutral-700"
                            >
                                <span>{{ o.order_number }} · Rs {{ o.total.toFixed(2) }}</span>
                                <span class="capitalize text-xs text-gray-500 dark:text-gray-400">{{ o.status }}</span>
                            </li>
                        </ul>
                        <p v-else class="text-sm text-gray-500 dark:text-gray-400">No orders yet.</p>
                    </section>

                    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                        <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Quick profile</h2>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Wishlist items: {{ insights.wishlist_count }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Reviews written: {{ insights.review_count }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Cancelled orders: {{ insights.cancelled_orders }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="/products" class="rounded-md border border-primary/50 px-3 py-1.5 text-xs text-primary hover:bg-primary/10">Explore products</a>
                            <a href="/wishlist" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs text-gray-700 hover:border-primary dark:border-neutral-600 dark:text-gray-200">Open wishlist</a>
                            <a href="/cart" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs text-gray-700 hover:border-primary dark:border-neutral-600 dark:text-gray-200">Go to cart</a>
                        </div>
                    </section>
                </div>
            </section>

            <section v-if="activeTab === 'recommendations' && recommended.length" class="space-y-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recommended for you</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <RecommendationExplainerCard v-for="p in recommended" :key="p.id" :product="p" />
                </div>
            </section>
            <section v-else-if="activeTab === 'recommendations'" class="rounded-xl border border-dashed border-white/20 bg-white/5 p-6 text-center">
                <h2 class="text-lg font-semibold text-white">No recommendations yet</h2>
                <p class="mt-1 text-sm text-gray-400">Browse products and add reviews to unlock personalized picks.</p>
                <a href="/products" class="mt-4 inline-block rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary/90">Start browsing</a>
            </section>

            <section v-if="activeTab === 'recommendations' && recent.length" class="space-y-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recently viewed</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <SmartProductCard v-for="p in recent" :key="p.id" :product="p" />
                </div>
            </section>

            <section v-if="activeTab === 'recommendations' && wishlist.length" class="space-y-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Wishlist</h2>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="row in wishlist"
                        :key="row.id"
                        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900"
                    >
                        <a :href="`/products/${row.product.slug}`" class="font-medium text-gray-900 hover:text-primary dark:text-white">
                            {{ row.product.name }}
                        </a>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Rs {{ row.product.effective_price }}</p>
                    </div>
                </div>
            </section>
            <section v-else-if="activeTab === 'recommendations'" class="rounded-xl border border-dashed border-white/20 bg-white/5 p-5 text-sm text-gray-400">
                Your wishlist is empty. Save items to compare and track later.
            </section>

            <div v-if="activeTab === 'activity'" class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Your interests</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Preference score: {{ profile?.preference_score?.toFixed?.(2) ?? '—' }}</p>
                    <p v-if="profile?.last_interest_update" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Updated {{ new Date(profile.last_interest_update).toLocaleString() }}
                    </p>
                    <p v-if="(profile?.preferred_categories?.length ?? 0) > 0" class="mt-3 text-sm text-gray-700 dark:text-gray-200">
                        Suggested categories: {{ (profile?.preferred_categories as string[]).join(', ') }}
                    </p>
                    <p v-else class="mt-3 text-sm text-gray-500 dark:text-gray-400">Leave more reviews to refine your taste profile.</p>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Recent activity</h2>
                    <ul v-if="timeline.length" class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <li v-for="(t, i) in timeline" :key="i" class="flex justify-between gap-2 border-b border-gray-200 py-2 dark:border-neutral-700">
                            <span>{{ t.label }}</span>
                            <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400">{{ t.at ? new Date(t.at).toLocaleString() : '' }}</span>
                        </li>
                    </ul>
                    <p v-else class="text-sm text-gray-500 dark:text-gray-400">No recent views yet.</p>
                </section>
            </div>
            <section v-if="activeTab === 'activity' && !timeline.length" class="rounded-xl border border-dashed border-white/20 bg-white/5 p-5 text-sm text-gray-400">
                No activity yet. Open a few products and your activity timeline will appear here.
            </section>

            <section v-if="activeTab === 'activity' && reviews.length" class="space-y-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your reviews</h2>
                <div class="grid gap-3 md:grid-cols-2">
                    <article
                        v-for="r in reviews"
                        :key="r.id"
                        class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-700 shadow-sm dark:border-neutral-700 dark:bg-neutral-900 dark:text-gray-200"
                    >
                        <div class="mb-2 flex items-center justify-between">
                            <span class="font-medium text-gray-900 dark:text-white">{{ r.product?.name }}</span>
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
            <section v-else-if="activeTab === 'activity'" class="rounded-xl border border-dashed border-white/20 bg-white/5 p-5 text-sm text-gray-400">
                You have not submitted reviews yet. Rate a product to build your preference profile faster.
            </section>
        </div>
    </AppLayout>
</template>
