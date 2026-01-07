<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import type { MenuCategory } from '@/types/menu'
import type { PageProps as InertiaPageProps } from '@inertiajs/core'

const show = ref(false)
const megaMenuButtonRef = ref<HTMLDivElement | null>(null)

interface PageProps extends InertiaPageProps {
    menuCategories: {
        new: {
            data: MenuCategory[],
        },
        trending: {
            data: MenuCategory[],
        },
        popular: {
            data: MenuCategory[],
        }
    }
}

const page = usePage<PageProps>()

const handleClickOutside = (event: MouseEvent) => {
    if (
        megaMenuButtonRef.value &&
        !megaMenuButtonRef.value.contains(event.target as Node)
    ) {
        show.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})

const newArrivals = computed<MenuCategory[]>(() =>
    page.props.menuCategories?.new?.data ?? []
)
const trending = computed<MenuCategory[]>(() =>
    page.props.menuCategories?.trending?.data ?? []
)
const popular = computed<MenuCategory[]>(() =>
    page.props.menuCategories?.popular?.data ?? []
)


</script>
<template>
    <div class="relative">
        <!-- Trigger -->
        <button @mouseenter="show = true" class="flex items-center gap-2 font-medium text-white hover:text-primary">
            Shop
            <span :class="show ? '-scale-y-100' : ''" class="duration-200">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10 14.25C9.8125 14.25 9.65625 14.1875 9.5 14.0625L2.3125 7C2.03125 6.71875 2.03125 6.28125 2.3125 6C2.59375 5.71875 3.03125 5.71875 3.3125 6L10 12.5312L16.6875 5.9375C16.9688 5.65625 17.4062 5.65625 17.6875 5.9375C17.9688 6.21875 17.9688 6.65625 17.6875 6.9375L10.5 14C10.3437 14.1562 10.1875 14.25 10 14.25Z"
                        fill="currentColor" />
                </svg>
            </span> </button>

        <!-- Mega Menu -->
        <transition name="fade-slide">
            <div v-if="show" @mouseleave="show = false" class="absolute left-1/2 top-full z-50 mt-6
               w-[65vw] max-w-6xl -translate-x-1/2
               rounded-2xl bg-neutral-900/95
               backdrop-blur-md border border-white/10
               p-8 text-white shadow-2xl">
                <div class="grid gap-8
                 grid-cols-1 sm:grid-cols-2
                 lg:grid-cols-4
                 ">
                    <!-- New -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold uppercase text-primary">
                            New Arrivals
                        </h4>
                        <Link v-for="c in newArrivals" :key="c.id" :href="`/category/${c.slug}`"
                            class="block text-sm text-gray-300 hover:text-white">
                            {{ c.name }}
                        </Link>
                        <Link href="/shop?sort=new" class="mt-4 inline-block text-xs text-primary">
                            View all →
                        </Link>
                    </div>

                    <!-- Trending -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold uppercase text-primary">
                            Trending
                        </h4>
                        <Link v-for="c in trending" :key="c.id" :href="`/category/${c.slug}`"
                            class="block text-sm text-gray-300 hover:text-white">
                            {{ c.name }}
                        </Link>
                        <Link href="/shop?sort=trending" class="mt-4 inline-block text-xs text-primary">
                            View all →
                        </Link>
                    </div>

                    <!-- Popular -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold uppercase text-primary">
                            Popular
                        </h4>
                        <Link v-for="c in popular" :key="c.id" :href="`/category/${c.slug}`"
                            class="block text-sm text-gray-300 hover:text-white">
                            {{ c.name }}
                        </Link>
                        <Link href="/shop?sort=popular" class="mt-4 inline-block text-xs text-primary">
                            View all →
                        </Link>
                    </div>

                    <!-- Promo -->
                    <div class="relative overflow-hidden rounded-xl">
                        <img src="https://cdn.tailgrids.com/assets/images/core-components/mega-menu/image-1.jpg"
                            class="h-full w-full object-cover" />
                        <div class="absolute inset-0 bg-primary/70 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-4xl font-bold">50%</p>
                                <p class="text-lg">Flat Discount</p>
                                <span class="mt-3 inline-block rounded-lg bg-black/30 px-6 py-2 text-sm">
                                    Shop Now
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 200ms ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>
