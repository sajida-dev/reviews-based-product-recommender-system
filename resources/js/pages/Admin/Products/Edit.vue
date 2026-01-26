<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Edit Product" />

        <div class="max-w-6xl mx-auto w-full px-4 py-8">
            <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                Edit Product
            </h1>

            <form @submit.prevent="submit"
                class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg shadow p-6 space-y-6">
                <!-- BASIC INFO -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <TextInput label="Product Name" v-model="form.name" :error="form.errors.name" required />
                    <SelectInput label="Category" v-model="form.category_id" :error="form.errors.category_id" required
                        :options="categories.map(c => ({ label: c.name, value: c.id }))" />
                </div>

                <TextInput label="Description" v-model="form.description" :error="form.errors.description" textarea />

                <!-- PRICING -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <TextInput label="Price" type="number" min="0" step="0.01" v-model="form.price" required />
                    <TextInput label="Discount Price" type="number" min="0" step="0.01" v-model="form.discount_price" />
                    <TextInput label="Stock" type="number" min="0" v-model="form.stock" required />
                </div>

                <!-- ATTRIBUTES -->
                <!-- <div>
                    <label class="block font-semibold mb-2">Product Attributes</label>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="(attr, index) in attributes" :key="index" class="border rounded p-3 relative">
                            <button type="button" @click="removeAttribute(index)"
                                class="absolute top-1 right-1 text-red-500">
                                ✕
                            </button>

                            <select v-model="attr.key" class="w-full border rounded px-2 py-1 mb-2">
                                <option disabled value="">Select Attribute</option>
                                <option v-for="opt in commonAttributes" :key="opt" :value="opt">
                                    {{ opt }}
                                </option>
                            </select>

                            <div v-if="attr.key === 'Color'">
                                <input type="color" v-model="attr.value" class="w-full h-10" />
                            </div>

                            <div v-else-if="attr.key === 'Size'">
                                <select v-model="attr.value" class="w-full border px-2 py-1">
                                    <option disabled value="">Select Size</option>
                                    <option v-for="s in sizes" :key="s" :value="s">{{ s }}</option>
                                </select>
                            </div>

                            <div v-else>
                                <input type="text" v-model="attr.value" class="w-full border px-2 py-1" />
                            </div>
                        </div>
                    </div>

                    <button type="button" @click="addAttribute" class="mt-3 border px-3 py-1 rounded text-blue-600">
                        + Add Attribute
                    </button>
                </div> -->

                <!-- EXISTING IMAGES -->
                <div v-if="existingImages.length">
                    <h3 class="font-semibold mb-2">Existing Images</h3>
                    <div class="flex flex-wrap gap-4">
                        <div v-for="img in existingImages" :key="img.id"
                            class="w-36 h-36 border rounded overflow-hidden">
                            <img :src="img.url" class="w-full h-full object-cover" />
                        </div>
                    </div>
                </div>

                <!-- NEW IMAGES -->
                <FileInput label="Add New Images" v-model="form.images" multiple />

                <!-- ACTIONS -->
                <div class="flex justify-end gap-2">
                    <Button variant="outline" type="button" @click="goBack">Cancel</Button>
                    <Button type="submit">Update Product</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { router, Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import TextInput from '@/components/form/TextInput.vue'
import SelectInput from '@/components/form/SelectInput.vue'
import FileInput from '@/components/form/FileInput.vue'
import { BreadcrumbItem } from '@/types'
import { toast } from 'vue3-toastify'

interface Category { id: number; name: string }
interface Attribute { key: string; value: string }
interface ExistingImage { id: number; url: string }

const props = defineProps<{
    product: any
    categories: Category[]
}>()

const categories = computed(() => props.categories ?? [])
const product = props.product

/* ---------- ATTRIBUTES PARSE (JSON SAFE) ---------- */
const parsedAttributes =
    typeof product.attributes === 'string'
        ? JSON.parse(product.attributes)
        : product.attributes ?? {}

const attributes = ref<Attribute[]>(
    Object.entries(parsedAttributes).map(([k, v]) => ({
        key: k,
        value: String(v),
    }))
)

/* ---------- EXISTING IMAGES ---------- */
const existingImages = ref<ExistingImage[]>(product.images ?? [])

/* ---------- FORM ---------- */
const form = useForm({
    name: product.name ?? '',
    category_id: Number(product.category_id),
    description: product.description ?? '',
    price: product.price ?? '',
    discount_price: product.discount_price ?? '',
    stock: product.stock ?? '',
    is_active: !!product.is_active,
    attributes: parsedAttributes,
    images: [] as File[],
})

watch(
    attributes,
    val => {
        const obj: Record<string, string> = {}
        val.forEach(a => {
            if (a.key && a.value) obj[a.key] = a.value
        })
        form.attributes = obj
    },
    { deep: true }
)

/* ---------- HELPERS ---------- */
const commonAttributes = ['Color', 'Size', 'Material', 'Brand', 'Weight', 'Features']
const sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL']

function addAttribute() {
    attributes.value.push({ key: '', value: '' })
}
function removeAttribute(index: number) {
    attributes.value.splice(index, 1)
}

/* ---------- SUBMIT ---------- */
function submit() {
    const fd = new FormData()
    fd.append('_method', 'PUT')

    Object.entries(form.data()).forEach(([key, value]: any) => {
        if (key === 'images') {
            value.forEach((f: File) => fd.append('images[]', f))
        } else if (key === 'attributes') {
            fd.append(key, JSON.stringify(value))
        } else {
            fd.append(key, String(value))
        }
    })

    router.post(`/admin/products/${product.id}`, fd, {
        onSuccess: () => {
            toast.success('Product updated successfully')
            router.visit('/products')
        },
        onError: () => toast.error('Update failed'),
    })
}

function goBack() {
    router.visit('/products')
}

/* ---------- BREADCRUMB ---------- */
const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/' },
    { title: 'Products', href: '/products' },
    { title: 'Edit Product', href: `/products/${product.id}/edit` },
]
</script>
