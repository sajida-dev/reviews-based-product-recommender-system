<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import type { BreadcrumbItem } from '@/types'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

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
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Admin', href: '/admin/dashboard' },
]

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
    <Head title="Admin · Analytics" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-8 p-4">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-white">Admin dashboard</h1>
                    <p class="text-sm text-gray-400">KPIs, review activity, and system signals.</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <Link href="/admin/products" class="rounded-lg border border-white/20 px-3 py-2 text-white hover:border-primary"> Products </Link>
                    <Link href="/admin/categories" class="rounded-lg border border-white/20 px-3 py-2 text-white hover:border-primary"> Categories </Link>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-xl border border-white/15 bg-white/5 p-4 backdrop-blur-md">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Users</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ analytics.kpis.total_users }}</p>
                    <p class="text-xs text-gray-500">Active (7d): {{ analytics.kpis.active_users_week }}</p>
                </div>
                <div class="rounded-xl border border-white/15 bg-white/5 p-4 backdrop-blur-md">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Catalog</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ analytics.kpis.total_products }}</p>
                    <p class="text-xs text-gray-500">Top category: {{ analytics.kpis.top_category || '—' }}</p>
                </div>
                <div class="rounded-xl border border-white/15 bg-white/5 p-4 backdrop-blur-md">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Reviews</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ analytics.kpis.total_reviews }}</p>
                    <p class="text-xs text-gray-500">Avg rating: {{ analytics.kpis.avg_rating }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-xl border border-white/15 bg-white/5 p-5 backdrop-blur-md">
                    <h2 class="mb-4 text-lg font-semibold text-white">Reviews per day</h2>
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
                            <span class="rotate-45 text-[8px] text-gray-500">{{ row.date.slice(5) }}</span>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500">No review data in this window.</p>
                </section>

                <section class="rounded-xl border border-white/15 bg-white/5 p-5 backdrop-blur-md">
                    <h2 class="mb-4 text-lg font-semibold text-white">Sentiment (ABSA)</h2>
                    <div v-if="analytics.sentiment_distribution.length" class="space-y-3">
                        <div v-for="s in analytics.sentiment_distribution" :key="s.sentiment" class="space-y-1">
                            <div class="flex justify-between text-xs text-gray-300">
                                <span class="capitalize">{{ s.sentiment }}</span>
                                <span>{{ s.count }} ({{ sentimentPct(s.count) }}%)</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-white/10">
                                <div class="h-full rounded-full bg-primary" :style="{ width: sentimentPct(s.count) + '%' }" />
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500">No aspect sentiments recorded yet.</p>
                </section>
            </div>

            <section class="rounded-xl border border-white/15 bg-white/5 p-5 backdrop-blur-md">
                <h2 class="mb-4 text-lg font-semibold text-white">Top aspects</h2>
                <div v-if="analytics.top_aspects.length" class="space-y-2">
                    <div v-for="a in analytics.top_aspects" :key="a.aspect" class="flex items-center gap-3 text-sm">
                        <span class="w-28 truncate text-gray-300">{{ a.aspect }}</span>
                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-white/10">
                            <div
                                class="h-full rounded-full bg-amber-400/90"
                                :style="{ width: `${(a.count / maxAspect) * 100}%` }"
                            />
                        </div>
                        <span class="w-8 text-right text-gray-400">{{ a.count }}</span>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-500">No aspects yet.</p>
            </section>

            <section class="rounded-xl border border-white/15 bg-white/5 p-5 backdrop-blur-md">
                <h2 class="mb-4 text-lg font-semibold text-white">Top products by reviews</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-200">
                        <thead class="border-b border-white/10 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="py-2 pr-4">Product</th>
                                <th class="py-2 pr-4">Reviews</th>
                                <th class="py-2">Avg</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in analytics.top_products" :key="p.id" class="border-b border-white/5">
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

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-xl border border-white/15 bg-white/5 p-5 backdrop-blur-md">
                    <h2 class="mb-3 text-lg font-semibold text-white">Recent reviews</h2>
                    <ul class="space-y-3 text-sm">
                        <li v-for="r in analytics.recent_reviews" :key="r.id" class="border-b border-white/10 pb-2">
                            <span class="text-gray-400">{{ r.user?.name }}</span>
                            · ★ {{ r.rating }}
                            <span class="ml-2 text-xs" :class="r.is_approved ? 'text-emerald-400' : 'text-amber-300'">
                                {{ r.is_approved ? 'approved' : 'pending' }}
                            </span>
                            <p class="mt-1 text-gray-300">{{ r.excerpt }}</p>
                        </li>
                    </ul>
                </section>

                <section class="rounded-xl border border-white/15 bg-white/5 p-5 backdrop-blur-md">
                    <h2 class="mb-3 text-lg font-semibold text-white">New users</h2>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li v-for="u in analytics.recent_users" :key="u.id" class="flex justify-between gap-2">
                            <span>{{ u.name }}</span>
                            <span class="text-xs text-gray-500">{{ u.email }}</span>
                        </li>
                    </ul>
                </section>
            </div>

            <section class="rounded-xl border border-white/15 bg-white/5 p-5 backdrop-blur-md">
                <h2 class="mb-3 text-lg font-semibold text-white">System health</h2>
                <ul class="grid gap-2 text-sm text-gray-300 sm:grid-cols-2">
                    <li>Queue: {{ analytics.system.queue_connection }}</li>
                    <li>Failed jobs: {{ analytics.system.failed_jobs }}</li>
                    <li>Embedding URL configured: {{ analytics.system.embedding_service_configured ? 'yes' : 'no' }}</li>
                    <li>Qdrant enabled: {{ analytics.system.qdrant_enabled ? 'yes' : 'no' }}</li>
                </ul>
                <p class="mt-3 text-xs text-gray-500">
                    Horizon, latency metrics, and external ML health checks can be wired here when those services are available.
                </p>
            </section>
        </div>
    </AppLayout>
</template>
