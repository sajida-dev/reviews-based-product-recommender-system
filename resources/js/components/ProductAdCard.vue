<template>
    <div class="flex md:flex-row flex-col-reverse w-full px-10 py-5 rounded-lg justify-between items-center transition-transform duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg  md:text-center cursor-pointer bg-white/60 backdrop-blur-md border border-white/30"
        @click="$emit('click', title)">
        <div class="flex flex-col max-w-[250px] gap-1 md:text-left text-center">
            <p class="text-sm text-neutral-600 mb-2.5">Upto {{ offerText }}% Off</p>
            <h2 class="text-3xl lg:text-4xl font-extrabold text-neutral-900 drop-shadow-lg mb-5">{{ title }}</h2>
            <Link v-if="slug" :href="route('products.show', slug)"
                class="bg-primary max-w-[150px] text-white px-5 py-2.5 rounded text-sm transition-colors duration-300 hover:bg-neutral-700">
                {{ buttonText }}
            </Link>
        </div>

        <div
            class="w-48 h-48 md:w-56 md:h-56 rounded-full overflow-hidden md:mt-3 mb-5 flex-shrink-0 border border-white/30 shadow-sm">
            <img :src="image" :alt="title" class="w-full h-full object-cover" />
        </div>

    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    offerText: { type: Number, default: 25 },
    title: { type: String, default: "Product Title" },
    slug: { type: String, default: "" },
    image: { type: String, required: true },
    buttonText: { type: String, default: "SHOP NOW" },
});
</script>
