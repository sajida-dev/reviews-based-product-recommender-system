<template>
    <div class="max-w-xs bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-200 flex flex-col">
        <!-- Main Product Image -->
        <div class="p-6 flex justify-center items-center bg-white">
            <img :src="selectedImage" class="h-36 object-contain transition-all duration-300" alt="product" />
        </div>

        <!-- Thumbnails -->
        <div class="flex gap-2 px-4 pb-4 overflow-x-auto">
            <div v-for="(img, index) in images" :key="index" @click="selectedImage = img"
                class="cursor-pointer border rounded-lg p-1" :class="{
                    'border-orange-500': selectedImage === img,
                    'border-gray-700': selectedImage !== img
                }">
                <img :src="img" class="h-10 w-10 object-contain" />
            </div>
        </div>

        <!-- Product Info -->
        <div class="bg-[#000B18] text-white px-4 pt-4 pb-6 rounded-t-3xl">
            <p class="text-sm text-gray-300 uppercase tracking-wide mb-1">
                {{ category }}
            </p>

            <div class="flex justify-between items-center mb-3">
                <h2 class="font-semibold text-lg">{{ title }}</h2>
                <span class="font-semibold text-lg">${{ price }}</span>
            </div>

            <!-- Add to Cart Button -->
            <div class="flex items-center justify-between gap-2">
                <button class="flex-1 bg-white text-black py-3 rounded-xl font-semibold hover:bg-gray-100">
                    ADD TO CART
                </button>

                <!-- Favorite Button -->
                <button @click="toggleLike" class="bg-white p-3 rounded-xl hover:bg-gray-100 transition">
                    <svg v-if="liked" class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 
              4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 
              3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 
              8.5c0 3.78-3.4 6.86-8.55 
              11.54L12 21.35z" />
                    </svg>

                    <svg v-else class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 
              0L12 7.636l1.318-1.318a4.5 4.5 0 
              116.364 6.364L12 21.364l-7.682-7.682a4.5 
              4.5 0 010-6.364z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ProductCard",
    props: {
        title: String,
        category: String,
        price: Number,
        images: Array,
    },
    data() {
        return {
            selectedImage: this.images[0],
            liked: false,
        };
    },
    methods: {
        toggleLike() {
            this.liked = !this.liked;
        },
    },
};
</script>

<style scoped>
/* Optional: Smooth scrolling thumbnails */
::-webkit-scrollbar {
    height: 4px;
}

::-webkit-scrollbar-thumb {
    background: #333;
    border-radius: 5px;
}
</style>
