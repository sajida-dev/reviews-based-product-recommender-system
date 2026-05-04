<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

interface AdminRecentReview {
    id: number
    rating: number
    excerpt?: string
    is_approved?: boolean
    user?: { name?: string }
}

const props = defineProps<{
    analytics: {
        kpis: {
            total_users: number
            active_users_week: number
            total_products: number
            total_reviews: number
            avg_rating: number
            top_category: string | null
            gross_revenue: number
            delivered_orders: number
            cancelled_orders: number
            items_sold: number
        }
        reviews_per_day: { date: string; count: number }[]
        sentiment_distribution: { sentiment: string; count: number }[]
        top_aspects: { aspect: string; count: number }[]
        top_products: { id: number; name: string; slug: string; review_count: number; avg_rating: number }[]
        recent_reviews: AdminRecentReview[]
        recent_users: { id: number; name: string; email: string; created_at: string }[]
        system: {
            queue_connection: string
            failed_jobs: number
            embedding_service_configured: boolean
            qdrant_enabled: boolean
        }
    }
}>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Admin', href: '/admin/dashboard' },
]
const activeTab = ref<'overview' | 'moderation' | 'system'>('overview')

const maxReviewDay = computed(() => {
    const rows = props.analytics.reviews_per_day
    if (!rows.length) return 1
    return Math.max(...rows.map((r) => r.count), 1)
})

const sentimentTotal = computed(() =>
    props.analytics.sentiment_distribution.reduce((s, r) => s + r.count, 0),
)

function sentimentPct(count: number) {
    const t = sentimentTotal.value || 1
    return Math.round((count / t) * 100)
}

const maxAspect = computed(() => {
    const rows = props.analytics.top_aspects
    if (!rows.length) return 1
    return Math.max(...rows.map((r) => r.count), 1)
})
</script>

<template>
    <Head title="Admin - Data Analytics" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-8 p-4">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Admin dashboard</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">KPIs, review activity, and system signals.</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <Link href="/admin/products" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-700 hover:border-primary dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"> Products </Link>
                    <Link href="/admin/categories" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-700 hover:border-primary dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"> Categories </Link>
                    <Link href="/admin/products/create" class="rounded-lg bg-primary px-3 py-2 text-white hover:bg-primary/90"> Add Product </Link>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <button
                    class="rounded-lg border px-3 py-1.5 text-sm transition"
                    :class="activeTab === 'overview' ? 'border-primary bg-primary/10 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-primary dark:border-neutral-700 dark:bg-neutral-900 dark:text-gray-300'"
                    @click="activeTab = 'overview'"
                >
                    Overview
                </button>
                <button
                    class="rounded-lg border px-3 py-1.5 text-sm transition"
                    :class="activeTab === 'moderation' ? 'border-primary bg-primary/10 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-primary dark:border-neutral-700 dark:bg-neutral-900 dark:text-gray-300'"
                    @click="activeTab = 'moderation'"
                >
                    Moderation
                </button>
                <button
                    class="rounded-lg border px-3 py-1.5 text-sm transition"
                    :class="activeTab === 'system' ? 'border-primary bg-primary/10 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-primary dark:border-neutral-700 dark:bg-neutral-900 dark:text-gray-300'"
                    @click="activeTab = 'system'"
                >
                    System
                </button>
            </div>

            <div v-if="activeTab === 'overview'" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Users</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ analytics.kpis.total_users }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Active (7d): {{ analytics.kpis.active_users_week }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Catalog</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ analytics.kpis.total_products }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Top category: {{ analytics.kpis.top_category || '—' }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Reviews</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ analytics.kpis.total_reviews }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Avg rating: {{ analytics.kpis.avg_rating }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Revenue</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Rs {{ analytics.kpis.gross_revenue.toFixed(2) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Delivered: {{ analytics.kpis.delivered_orders }}</p>
                </div>
            </div>

            <section v-if="activeTab === 'overview'" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Commerce snapshot</h2>
                <ul class="grid gap-2 text-sm text-gray-700 dark:text-gray-300 sm:grid-cols-3">
                    <li>Items sold: {{ analytics.kpis.items_sold }}</li>
                    <li>Cancelled orders: {{ analytics.kpis.cancelled_orders }}</li>
                    <li>Delivered orders: {{ analytics.kpis.delivered_orders }}</li>
                </ul>
            </section>

            <div v-if="activeTab === 'overview'" class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Reviews per day</h2>
                    <div v-if="analytics.reviews_per_day.length" class="flex h-40 items-end gap-1">
                        <div
                            v-for="row in analytics.reviews_per_day"
                            :key="row.date"
                            class="flex flex-1 flex-col items-center gap-1"
                        >
                            <div
                                class="w-full max-w-[14px] rounded-t bg-primary/80"
                                :style="{ height: `${(row.count / maxReviewDay) * 100}%`, minHeight: row.count ? '4px' : '0' }"
                                :title="`${row.date}: ${row.count}`"
                            />
                            <span class="rotate-45 text-[8px] text-gray-500 dark:text-gray-400">{{ row.date.slice(5) }}</span>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500 dark:text-gray-400">No review data in this window.</p>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Sentiment (ABSA)</h2>
                    <div v-if="analytics.sentiment_distribution.length" class="space-y-3">
                        <div v-for="s in analytics.sentiment_distribution" :key="s.sentiment" class="space-y-1">
                            <div class="flex justify-between text-xs text-gray-700 dark:text-gray-300">
                                <span class="capitalize">{{ s.sentiment }}</span>
                                <span>{{ s.count }} ({{ sentimentPct(s.count) }}%)</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-white/10">
                                <div class="h-full rounded-full bg-primary" :style="{ width: sentimentPct(s.count) + '%' }" />
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500 dark:text-gray-400">No aspect sentiments recorded yet.</p>
                </section>
            </div>

            <section v-if="activeTab === 'overview'" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Top aspects</h2>
                <div v-if="analytics.top_aspects.length" class="space-y-2">
                    <div v-for="a in analytics.top_aspects" :key="a.aspect" class="flex items-center gap-3 text-sm">
                        <span class="w-28 truncate text-gray-700 dark:text-gray-300">{{ a.aspect }}</span>
                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-white/10">
                            <div
                                class="h-full rounded-full bg-amber-400/90"
                                :style="{ width: `${(a.count / maxAspect) * 100}%` }"
                            />
                        </div>
                        <span class="w-8 text-right text-gray-500 dark:text-gray-400">{{ a.count }}</span>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-500 dark:text-gray-400">No aspects yet.</p>
            </section>

            <section v-if="activeTab === 'overview'" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Top products by reviews</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-700 dark:text-gray-200">
                        <thead class="border-b border-gray-200 text-xs uppercase text-gray-500 dark:border-neutral-700 dark:text-gray-500">
                            <tr>
                                <th class="py-2 pr-4">Product</th>
                                <th class="py-2 pr-4">Reviews</th>
                                <th class="py-2">Avg</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in analytics.top_products" :key="p.id" class="border-b border-gray-100 dark:border-neutral-800">
                                <td class="py-2 pr-4">
                                    <Link :href="`/products/${p.slug}`" class="text-primary hover:underline">{{ p.name }}</Link>
                                </td>
                                <td class="py-2 pr-4">{{ p.review_count }}</td>
                                <td class="py-2">{{ p.avg_rating }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <div v-if="activeTab === 'moderation'" class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Recent reviews</h2>
                    <ul class="space-y-3 text-sm">
                        <li v-for="r in analytics.recent_reviews" :key="r.id" class="border-b border-gray-200 pb-2 dark:border-neutral-700">
                            <span class="text-gray-500 dark:text-gray-400">{{ r.user?.name }}</span>
                            · ★ {{ r.rating }}
                            <span class="ml-2 text-xs" :class="r.is_approved ? 'text-emerald-400' : 'text-amber-300'">
                                {{ r.is_approved ? 'approved' : 'pending' }}
                            </span>
                            <p class="mt-1 text-gray-700 dark:text-gray-300">{{ r.excerpt }}</p>
                        </li>
                    </ul>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">New users</h2>
                    <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <li v-for="u in analytics.recent_users" :key="u.id" class="flex justify-between gap-2">
                            <span>{{ u.name }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ u.email }}</span>
                        </li>
                    </ul>
                </section>
            </div>

            <section v-if="activeTab === 'system'" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">System health</h2>
                <ul class="grid gap-2 text-sm text-gray-700 dark:text-gray-300 sm:grid-cols-2">
                    <li>Queue: {{ analytics.system.queue_connection }}</li>
                    <li>Failed jobs: {{ analytics.system.failed_jobs }}</li>
                    <li>Embedding URL configured: {{ analytics.system.embedding_service_configured ? 'yes' : 'no' }}</li>
                    <li>Qdrant enabled: {{ analytics.system.qdrant_enabled ? 'yes' : 'no' }}</li>
                </ul>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                    Horizon, latency metrics, and external ML health checks can be wired here when those services are available.
                </p>
            </section>
        </div>
    </AppLayout>
</template>
