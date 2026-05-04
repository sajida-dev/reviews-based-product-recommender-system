<template>
    <article class="relative rounded-xl border border-white/20 bg-white/10 p-6 backdrop-blur-md transition hover:border-white/30">
        <div class="mb-3 flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <img
                    :src="review.user?.avatar_url || fallbackAvatar"
                    alt=""
                    class="h-10 w-10 rounded-full border-2 border-white/30 object-cover"
                    loading="lazy"
                />
                <div>
                    <p class="font-semibold text-white">{{ review.user?.name || 'Customer' }}</p>
                    <p class="text-xs text-gray-400">{{ formattedDate }}</p>
                </div>
            </div>
            <div class="flex gap-0.5" aria-label="Rating">
                <Star v-for="i in 5" :key="i" class="h-4 w-4" :class="i <= review.rating ? 'fill-amber-400 text-amber-400' : 'text-gray-600'" />
            </div>
        </div>

        <p class="mb-3 whitespace-pre-wrap break-words text-gray-200">{{ review.review || '—' }}</p>

        <div v-if="chips.length" class="mb-3 flex flex-wrap gap-1.5">
            <span
                v-for="c in chips"
                :key="c.key"
                class="rounded-full border px-2 py-0.5 text-[11px]"
                :class="chipClass(c.sentiment)"
            >
                {{ c.label }}
            </span>
        </div>

        <div v-if="showModeration" class="flex flex-wrap items-center gap-2 text-xs">
            <span
                class="rounded-md px-2 py-1 font-medium"
                :class="review.is_approved ? 'bg-emerald-500/20 text-emerald-200' : 'bg-amber-500/20 text-amber-100'"
            >
                {{ review.is_approved ? 'Approved' : 'Pending approval' }}
            </span>
            <span v-if="review.spam_flagged" class="rounded-md bg-red-500/20 px-2 py-1 text-red-200">Spam flagged</span>
        </div>

        <div v-if="typeof review.helpful_count === 'number'" class="mt-3 text-xs text-gray-400">
            {{ review.helpful_count }} found this helpful
        </div>
    </article>
</template>

<script setup lang="ts">
import type { ReviewCardModel } from '@/types/review'
import { Star } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps<{
    review: ReviewCardModel
    showModeration?: boolean
}>()

const fallbackAvatar = 'https://www.gravatar.com/avatar/?d=identicon'

const formattedDate = computed(() => {
    if (!props.review.created_at) return ''
    return new Date(props.review.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
})

const chips = computed(() => {
    const raw = props.review.aspect_sentiments || props.review.aspectSentiments || []
    return raw.map((a) => ({
        key: `${a.aspect}-${a.sentiment}`,
        label: `${a.aspect} · ${a.sentiment}`,
        sentiment: a.sentiment,
    }))
})

function chipClass(sentiment: string) {
    if (sentiment === 'positive') return 'border-emerald-500/40 bg-emerald-500/10 text-emerald-100'
    if (sentiment === 'negative') return 'border-red-500/40 bg-red-500/10 text-red-100'
    return 'border-white/25 bg-white/5 text-gray-200'
}
</script>
