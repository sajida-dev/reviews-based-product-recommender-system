<template>
    <section class="mt-16">
        <h3 class="text-xl font-bold mb-6">Recommended Products</h3>

        <div v-if="products.length" class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div v-for="p in products" :key="p.id" class="border rounded-lg overflow-hidden">
                <img :src="p.images[0]?.url" class="w-full h-40 object-cover" />
                <div class="p-2">
                    <h4 class="font-semibold text-gray-900">{{ p.name }}</h4>
                    <span class="text-primary font-bold">Rs {{ p.effective_price }}</span>
                </div>
            </div>
        </div>

        <p v-else class="text-gray-500">No recommended products found.</p>
    </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps<{ currentProductId: number }>()

const products = ref<any[]>([])

// We use Inertia router.get instead of axios
onMounted(() => {
    router.get(
        `/api/products/recommended/${props.currentProductId}`,
        {},
        {
            only: [],
            preserveState: true,
            onSuccess: (page) => {
                // Recommended products data should come in `page.props`
                // Adjust this if your backend returns JSON instead of Inertia props
                products.value = (page.props.recommended as any[]) || []
            },
            onError: (errors) => {
                console.error('Failed to load recommended products', errors)
            },
        }
    )
})
</script>
