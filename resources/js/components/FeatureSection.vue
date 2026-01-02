<template>
    <h1 class="text-4xl lg:text-5xl font-extrabold text-white text-center mb-2 drop-shadow-lg">Features </h1>
    <hr class="w-15 mx-auto pb-15 ">
    <section
        class="features-section max-w-6xl   mx-auto flex flex-wrap justify-center gap-10 py-15 px-6 bg-white/20 backdrop-blur-md border border-white/30 text-neutral-200">
        <FeatureBox v-for="(feature, index) in features" :key="index" :icon="feature.icon" :title="feature.title"
            :description="feature.description" />
    </section>
</template>
<script setup lang="ts">
import FeatureBox from './FeatureBox.vue'

interface Feature {
    icon: string
    title: string
    description: string
}

const props = defineProps<{
    features: Feature[]
}>()

const features = props.features
</script>
<style scoped>
/* Optional Tailwind utility fallback for FontAwesome icons */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');
</style>
