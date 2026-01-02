<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Edit Product" />

        <div class="max-w-4xl mx-auto w-full px-4 py-8">
            <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                Edit Product
            </h1>

            <form @submit.prevent="submit"
                class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg shadow p-6 space-y-6">
                <!-- BASIC INFO -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <TextInput label="Product Name" v-model="form.name" :error="form.errors.name" required />

                    <SelectInput label="Category" v-model="form.category_id" :error="form.errors.category_id" required
                        :options="categories.map(c => ({
                            label: c.name,
                            value: c.id
                        }))" />
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
                <TextInput label="Attributes (JSON)" v-model="attributesJson" :error="form.errors.attributes"
                    textarea />

                <!-- IMAGES -->
                <div>
                    <FileInput label="Add / Replace Images" v-model="form.images" multiple
                        :error="form.errors.images" />

                    <TextInput label="Primary Image Index (0 based)" type="number" min="0" v-model="form.primary_image"
                        :error="form.errors.primary_image" />
                </div>

                <!-- ACTIONS -->
                <div class="flex justify-end gap-2 pt-4">
                    <Button variant="outline" type="button" @click="goBack">
                        Cancel
                    </Button>
                    <Button variant="default" type="submit" :disabled="form.processing">
                        Update Product
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router, Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import TextInput from '@/components/form/TextInput.vue';
import SelectInput from '@/components/form/SelectInput.vue';
import FileInput from '@/components/form/FileInput.vue';
import { BreadcrumbItem } from '@/types';
import { toast } from 'vue3-toastify';

interface Category {
    id: number;
    name: string;
}

const page = usePage<any>();
const product = page.props.product;
const categories = computed<Category[]>(() => page.props.categories || []);

const form = useForm<{
    name: string;
    category_id: number | '';
    description: string;
    price: number | '';
    discount_price: number | '';
    stock: number | '';
    is_active: boolean;
    attributes: Record<string, any>;
    images: File[];
    primary_image?: number;
}>({
    name: product.name ?? '',
    category_id: product.category_id ?? '',
    description: product.description ?? '',
    price: product.price ?? '',
    discount_price: product.discount_price ?? '',
    stock: product.stock ?? '',
    is_active: !!product.is_active,
    attributes: product.attributes ?? {},
    images: [],
    primary_image: undefined,
});

/* JSON editor */
const attributesJson = ref(
    JSON.stringify(form.attributes, null, 2)
);

watch(attributesJson, (val) => {
    try {
        form.attributes = val ? JSON.parse(val) : {};
    } catch {
        // handled by backend validation
    }
});

function submit() {
    form.post(`/products/${product.id}`, {
        forceFormData: true,
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

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/' },
    { title: 'Products', href: '/products' },
    { title: 'Edit Product', href: `/products/${product.id}/edit` },
];
</script>
