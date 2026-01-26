<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Add Product" />

        <div class="max-w-6xl mx-auto w-full px-4 py-8">
            <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                Add New Product
            </h1>

            <form @submit.prevent="submit"
                class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg shadow p-6 space-y-6">

                <!-- BASIC INFO -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <TextInput label="Product Name" v-model="form.name" :error="form.errors.name" required />

                    <div>
                        <TextInput label="slug" v-model="form.slug" :error="form.errors.slug" required />
                        <p class="text-xs text-gray-500">This slug is used in the product URL. You can edit it
                            manually if needed.</p>
                    </div>
                    <SelectInput label="Category" v-model="form.category_id" :error="form.errors.category_id" required
                        :options="categories.map((c: Category) => ({ label: c.name, value: c.id }))" />
                </div>
                <label class="block text-sm font-semibold mb-1">Description <span class="text-red-500">*</span></label>
                <textarea rows="3" v-model="form.description" class="w-full border rounded px-2 py-1"></textarea>
                <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                    {{ form.errors.description }}
                </p>
                <!-- PRICING -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <TextInput label="Price" type="number" min="0" step="0.01" v-model="form.price"
                        :error="form.errors.price" required />
                    <TextInput label="Discount Price" type="number" min="0" step="0.01" v-model="form.discount_price"
                        :error="form.errors.discount_price" />
                    <TextInput label="Stock Quantity" type="number" min="0" v-model="form.stock"
                        :error="form.errors.stock" required />
                </div>


                <!-- <div>
                    <label class="block text-sm font-semibold mb-2">Product Attributes</label>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="(attr, index) in attributes" :key="index"
                            class="border rounded-lg p-3 shadow hover:shadow-md transition relative">

                            <button type="button" @click="removeAttribute(index)"
                                class="absolute top-1 right-1 text-red-500 hover:text-white bg-white/80 hover:bg-red-500 rounded-full p-1 transition">
                                <Trash class="w-4 h-4" />
                            </button>

                            <select v-model="attr.key" class="w-full border rounded px-2 py-1 mb-1">
                                <option disabled value="">Select Attribute</option>
                                <option v-for="opt in commonAttributes" :key="opt" :value="opt">{{ opt }}</option>
                            </select>
                            <p v-if="attr.key" class="text-xs text-gray-500 mb-2">
                                {{ getAttributeHint(attr.key) }}
                            </p>

                            <div v-if="attr.key === 'Color'">
                                <input type="color" v-model="attr.value" class="w-full h-10 rounded cursor-pointer" />
                                <p class="text-xs text-gray-500 mt-1">Pick a color from the palette or type a hex code
                                    (e.g., #FF5733).</p>
                            </div>

                            <div v-else-if="attr.key === 'Size'">
                                <select v-model="attr.value" class="w-full border rounded px-2 py-1">
                                    <option disabled value="">Select Size</option>
                                    <option v-for="size in sizes" :key="size" :value="size">{{ size }}</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Select a standard product size.</p>
                            </div>

                            <div v-else-if="attr.key === 'Features'">
                                <input type="text" v-model="attr.value" placeholder="Enter features, comma separated"
                                    class="w-full border rounded px-2 py-1" />
                                <p class="text-xs text-gray-500 mt-1">Add key product features separated by commas.
                                    Example: Waterproof, Lightweight.</p>
                            </div>

                            <div v-else-if="attr.key == 'Material'">
                                <input type="text" v-model="attr.value" placeholder="Enter value"
                                    class="w-full border rounded px-2 py-1" />
                                <p class="text-xs text-gray-500 mt-1">Provide a value for this attribute. Example:
                                    Material → Cotton.</p>
                            </div>

                            <div v-else-if="attr.key == 'Brand'">
                                <input type="text" v-model="attr.value" placeholder="Enter value"
                                    class="w-full border rounded px-2 py-1" />
                                <p class="text-xs text-gray-500 mt-1">Provide a value for this attribute. Example:
                                    Brand → Apple.</p>
                            </div>
                            <div v-else-if="attr.key == 'Weight'">
                                <input type="text" v-model="attr.value" placeholder="Enter value"
                                    class="w-full border rounded px-2 py-1" />
                                <p class="text-xs text-gray-500 mt-1">Provide a value for this attribute. Example:
                                    Weight → 1.5kg.</p>
                            </div>

                        </div>
                    </div>

                    <button type="button" @click="addAttribute"
                        class="mt-3 inline-flex items-center gap-1 text-blue-500 hover:text-white hover:bg-blue-500 border border-blue-500 rounded px-3 py-1 transition">
                        <Plus class="w-4 h-4" /> Add Attribute
                    </button>
                </div> -->
                <!-- IMAGES -->
                <div>
                    <FileInput label="Product Images" v-model="form.images" multiple @change="onFileSelect"
                        :error="form.errors.images" />

                    <div v-if="form.images.length" class="mt-4">
                        <h3 class="font-semibold mb-2">Selected Images:</h3>
                        <div class="flex flex-wrap gap-4">
                            <div v-for="(img, index) in form.images" :key="index"
                                class="relative border rounded-lg overflow-hidden w-34 h-34 cursor-pointer hover:border-blue-500 transition">

                                <div class="relative w-36 h-36 border rounded-lg overflow-hidden cursor-pointer">
                                    <img :src="getPreviewUrl(img)" @click.stop="setPrimary(index)"
                                        class="w-full h-full object-cover" />
                                    <button @click.stop="removeImage(index)"
                                        class="absolute top-1 right-3 bg-white/80 p-1 rounded-full">
                                        <Trash class="w-5 h-5 text-red-500" />
                                    </button>
                                    <button v-if="form.primary_image === index"
                                        class="absolute left-1 top-1 bg-white/80 p-1 rounded-full">
                                        <Star class="w-5 h-5 text-yellow-400" />
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <p v-if="form.images.length == 0 && form.primary_image === null" class="text-red-500 text-sm mt-1">
                        Please select a primary image.
                    </p>
                </div>

                <!-- ACTIONS -->
                <div class="flex justify-end gap-2 pt-4">
                    <Button variant="outline" type="button" @click="goBack">Cancel</Button>
                    <Button variant="default" type="submit"
                        :disabled="form.processing || (form.images.length && form.primary_image === null)">
                        Create Product
                    </Button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import TextInput from '@/components/form/TextInput.vue';
import SelectInput from '@/components/form/SelectInput.vue';
import FileInput from '@/components/form/FileInput.vue';
import { BreadcrumbItem } from '@/types';
import { toast } from 'vue3-toastify';
import { Star, Trash, Plus } from 'lucide-vue-next';

interface Category { id: number; name: string; }
interface Attribute {
    key: string;
    value: string;
}
const page = usePage<any>();
const categories = computed<Category[]>(() => page.props.categories || []);

const form = useForm<{
    name: string;
    slug?: string;
    category_id: number | '';
    description: string;
    price: number | '';
    discount_price: number | '';
    stock: number | '';
    is_active: boolean;
    attributes: Record<string, string>;
    images: File[];
    primary_image: number | null;
}>({
    name: '',
    slug: '',
    category_id: '',
    description: '',
    price: '',
    discount_price: '',
    stock: '',
    is_active: true,
    attributes: {},
    images: [],
    primary_image: null,
});

watch(() => form.images, (files) => {
    if (files.length === 1) form.primary_image = 0;
    if (files.length === 0) form.primary_image = null;
});

watch(() => form.name, (newName) => {
    if (!form.slug) { // only auto-generate if user hasn't manually edited slug
        form.slug = generateSlug(newName);
    }
});


function generateSlug(str: string) {
    return str
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-') // replace spaces and non-alphanum with dash
        .replace(/^-+|-+$/g, ''); // remove starting and ending dashes
}


/* ATTRIBUTES as key-value */
const commonAttributes = ['Color', 'Size', 'Material', 'Brand', 'Weight', 'Features'];
const sizes = ['S', 'M', 'L', 'XL', 'XXL'];
const attributes = ref<Attribute[]>([]);

watch(attributes, (val) => {
    const obj: Record<string, string> = {};
    val.forEach(a => {
        if (a.key && a.value) obj[a.key] = a.value.trim();
    });
    form.attributes = obj;
});

/* IMAGES */
function onFileSelect(e: Event) {
    const files = Array.from((e.target as HTMLInputElement).files || []);
    const newFiles = files.filter(f =>
        !form.images.some(existing => existing.name === f.name && existing.size === f.size)
    );
    form.images = [...form.images, ...newFiles];

    if (form.images.length === 1) form.primary_image = 0; // auto-primary if only one
}


function getPreviewUrl(file: File) { return URL.createObjectURL(file); }
function setPrimary(index: number) { form.primary_image = index; }
function removeImage(index: number) {
    form.images.splice(index, 1);
    if (form.primary_image === index) form.primary_image = null;
    else if (form.primary_image !== null && form.primary_image > index) form.primary_image--;
}

/* ATTRIBUTES helpers */
function addAttribute() { attributes.value.push({ key: '', value: '' }); }
function removeAttribute(index: number) { attributes.value.splice(index, 1); }
function getAttributeHint(attrKey: string) {
    switch (attrKey) {
        case 'Color': return 'Select the color of the product. You can choose multiple colors later.';
        case 'Size': return 'Pick a standard size for your product.';
        case 'Features': return 'List product features separated by commas.';
        case 'Material': return 'Specify what the product is made of.';
        case 'Weight': return 'Enter the weight in grams or kg.';
        default: return 'Enter a value for this attribute.';
    }
}
function submit() {
    if (form.images.length && form.primary_image === null) {
        toast.error('Please select a primary image before submitting.');
        return;
    }

    form.post('/admin/products', {
        forceFormData: true,
        onSuccess: () => {
            toast.success('Product created successfully');
            router.visit('/admin/products');
        },
        onError: () => {
            toast.error('Please fix the errors in the form.');
        },
    });
}

function goBack() { router.visit('/admin/products'); }

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Products', href: '/admin/products' },
    { title: 'Add Product', href: '/admin/products/create' },
];
</script>
