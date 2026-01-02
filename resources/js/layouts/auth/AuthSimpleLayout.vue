<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <!-- https://enterprise-insights.dji.com/hs-fs/hubfs/M2EA%20Accessories.gif?width=1024&name=M2EA%20Accessories.gif -->
    <div
        class="flex min-h-svh flex-col text-white items-start justify-center gap-6 bg-[url('/bg/bg-right.jpg')] bg-cover bg-right p-6 md:p-10">
        <div class="w-full max-w-md bg-[#28292965] lg:ml-20 p-5 rounded-md shadow-lg">
            <div class="flex flex-col gap-8  ">
                <div class="flex flex-col items-center gap-4 ">
                    <Link :href="home()" class="flex flex-col items-center gap-2 font-medium">
                    <div class="mb-1 flex h-9 w-9 items-center justify-center rounded-md">
                        <AppLogoIcon class="size-13 fill-current text-[var(--foreground)] dark:text-white" />
                    </div>
                    <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>

    </div>
</template>
