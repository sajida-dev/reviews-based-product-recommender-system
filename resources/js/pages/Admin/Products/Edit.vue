<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Edit Product" />

        <div class="max-w-6xl mx-auto w-full px-4 py-8">
            <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Edit Product</h1>

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
                    <TextInput label="Price" type="number" min="0" step="0.01" v-model="form.price"
                        :error="form.errors.price" required />
                    <TextInput label="Discount Price" type="number" min="0" step="0.01" v-model="form.discount_price"
                        :error="form.errors.discount_price" />
                    <TextInput label="Stock" type="number" min="0" v-model="form.stock" :error="form.errors.stock"
                        required />
                </div>

                <!-- STATUS -->
                <div class="flex items-center gap-2">
                    <input id="is_active" type="checkbox" v-model="form.is_active" class="rounded border-gray-300" />
                    <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">
                        Product is active
                    </label>
                </div>

                <!-- ATTRIBUTES -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Product Attributes</label>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="(attr, index) in attributes" :key="index"
                            class="border rounded-lg p-3 shadow hover:shadow-md transition relative">

                            <!-- Remove button -->
                            <button type="button" @click="removeAttribute(index)"
                                class="absolute top-1 right-1 text-red-500 hover:text-white bg-white/80 hover:bg-red-500 rounded-full p-1 transition">
                                ✕
                            </button>

                            <!-- Attribute key -->
                            <select v-model="attr.key" class="w-full border rounded px-2 py-1 mb-1">
                                <option disabled value="">Select Attribute</option>
                                <option v-for="opt in commonAttributes" :key="opt" :value="opt">{{ opt }}</option>
                            </select>
                            <p v-if="attr.key" class="text-xs text-gray-500 mb-2">{{ getAttributeHint(attr.key) }}</p>

                            <!-- Dynamic input based on attribute -->
                            <div v-if="attr.key === 'Color'">
                                <input type="color" v-model="attr.value" class="w-full h-10 rounded cursor-pointer" />
                            </div>

                            <div v-else-if="attr.key === 'Size'">
                                <select v-model="attr.value" class="w-full border rounded px-2 py-1">
                                    <option disabled value="">Select Size</option>
                                    <option v-for="size in sizes" :key="size" :value="size">{{ size }}</option>
                                </select>
                            </div>

                            <div v-else-if="['Brand', 'Material', 'Weight', 'Features'].includes(attr.key)">
                                <input type="text" v-model="attr.value" placeholder="Enter value"
                                    class="w-full border rounded px-2 py-1" />
                            </div>

                        </div>
                    </div>

                    <!-- Add attribute button -->
                    <button type="button" @click="addAttribute"
                        class="mt-3 inline-flex items-center gap-1 text-blue-500 hover:text-white hover:bg-blue-500 border border-blue-500 rounded px-3 py-1 transition">
                        + Add Attribute
                    </button>
                </div>

                <!-- IMAGES -->
                <div>
                    <FileInput label="Add / Replace Images" v-model="form.images" multiple
                        :error="form.errors.images" />
                    <TextInput label="Primary Image Index (0 based)" type="number" min="0" v-model="form.primary_image"
                        :error="form.errors.primary_image" />
                </div>

                <!-- ACTIONS -->
                <div class="flex justify-end gap-2 pt-4">
                    <Button variant="outline" type="button" @click="goBack">Cancel</Button>
                    <Button variant="default" type="submit" :disabled="form.processing">Update Product</Button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import TextInput from '@/components/form/TextInput.vue';
import SelectInput from '@/components/form/SelectInput.vue';
import FileInput from '@/components/form/FileInput.vue';
import { BreadcrumbItem } from '@/types';
import { toast } from 'vue3-toastify';

interface Category { id: number; name: string; }
interface Attribute { key: string; value: string; }

const props = defineProps<{
    product: any
    categories: Category[]
}>()
console.log('props', props)
const product = props.product
console.log('product', product)
const categories = computed<Category[]>(() => props.categories || []);

const form = useForm({
    name: product.name || '',
    category_id: Number(product.category_id),
    description: product.description || '',
    price: product.price || '',
    discount_price: product.discount_price || '',
    stock: product.stock || '',
    is_active: !!product.is_active,
    attributes: product.attributes || [],
    images: [] as File[],
    primary_image: product.primary_image ?? 0,
});
console.log('form', form)
const attributes = ref<Attribute[]>(
    product.attributes ? [...product.attributes] : []
)

const commonAttributes = ref(['Color', 'Size', 'Material', 'Brand', 'Weight', 'Features']);

const sizes = ref(['XS', 'S', 'M', 'L', 'XL', 'XXL']);

function addAttribute() { attributes.value.push({ key: '', value: '' }); }

function removeAttribute(index: number) { attributes.value.splice(index, 1); }

function getAttributeHint(key: string) {
    const hints: Record<string, string> = {
        Color: 'Pick a color or type a hex code.',
        Size: 'Select a standard product size.',
        Material: 'Provide the material (e.g., Cotton).',
        Brand: 'Provide the brand name.',
        Weight: 'Provide weight (e.g., 1.5kg).',
        Features: 'Comma separated key features.'
    };
    return hints[key] || '';
}

// Breadcrumb
const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/' },
    { title: 'Products', href: '/products' },
    { title: 'Edit Product', href: `/products/${product.id}/edit` },
];

// Submit
function submit() {
    const formData = new FormData();
    formData.append('_method', 'PUT');

    form.attributes = attributes.value;

    const data = form.data();
    Object.keys(data).forEach(key => {
        const value = (data as any)[key];
        if (value === null || value === undefined) return;

        if (key === 'images' && Array.isArray(value)) {
            value.forEach((file: File) => formData.append('images[]', file));
        } else if (key === 'attributes') {
            formData.append(key, JSON.stringify(value));
        } else {
            formData.append(key, typeof value === 'number' || typeof value === 'boolean' ? value.toString() : value);
        }
    });

    router.post(`/products/${product.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
        onSuccess: () => {
            toast.success('Product updated successfully');
            router.visit('/products');
        },
        onError: () => {
            toast.error('Failed to update product');
        },
    });
}

function goBack() {
    router.visit('/products');
}
</script>
