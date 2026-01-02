<template>
    <div class="flex w-full flex-row p-10 rounded-lg justify-between items-center transition-transform duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg  md:text-center cursor-pointer bg-gray-100"
        @click="$emit('click', title)">
        <!--  flex-1  -->
        <!-- Content -->
        <div class="flex flex-col gap-1 md:text-left sm:text-center">
            <p class="text-sm text-gray-600 mb-2.5">{{ offerText }}</p>
            <h2 class="text-3xl lg:text-4xl font-extrabold text-neutral-900 drop-shadow-lg mb-5">{{ title }}</h2>
            <!-- class="text-3xl lg:text-4xl font-extrabold text-neutral-900 drop-shadow-lg" -->
            <button
                class="bg-gray-900 text-white px-5 py-2.5 rounded text-sm transition-colors duration-300 hover:bg-gray-700">
                {{ buttonText }}
            </button>
        </div>

        <div>
            <!-- Image -->
            <img :src="image" :alt="title" class="w-48 h-auto md:mt-5" />
        </div>

    </div>
</template>

<script setup>
defineProps({
    offerText: { type: String, default: "Upto 25% Off" },
    title: { type: String, default: "Product Title" },
    image: { type: String, required: true },
    buttonText: { type: String, default: "SHOP NOW" },
});
</script>
