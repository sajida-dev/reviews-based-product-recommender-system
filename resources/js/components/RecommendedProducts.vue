<template>
    <section class="mt-16">
        <h3 class="text-xl font-bold mb-6">Recommended Products</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div v-for="p in products" :key="p.id" class="border rounded-lg overflow-hidden">
                <img :src="p.images[0]?.url" class="w-full h-40 object-cover" />
                <div class="p-2">
                    <h4 class="font-semibold text-gray-900">{{ p.name }}</h4>
                    <span class="text-primary font-bold">Rs {{ p.effective_price }}</span>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps<{ currentProductId: number }>()

const products = ref<any[]>([])

onMounted(async () => {
    const res = await axios.get(`/api/products/recommended/${props.currentProductId}`)
    products.value = res.data
})
</script>
