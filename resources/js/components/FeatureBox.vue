<template>
    <div class="feature-box flex items-start gap-4 flex-1 min-w-[200px]">
        <i :class="icon" class="text-4xl text-primary mt-1"></i>
        <div class="feature-text">
            <h2 class="text-2xl font-semibold text-white mb-1">{{ title }}</h2>
            <p class="text-neutral-400 text-sm">{{ description }}</p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    icon: { type: String, required: true },
    title: { type: String, required: true },
    description: { type: String, required: true }
})
</script>
