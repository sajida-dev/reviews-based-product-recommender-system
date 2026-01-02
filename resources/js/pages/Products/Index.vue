<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Products" />
        <div class="max-w-full mx-auto w-full px-2 sm:px-4 md:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Products</h1>

            <!-- Search Input -->
            <div class="mb-6">
                <div class="relative max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input v-model="filtersForm.search" type="text" placeholder="Search products by name or category..."
                        class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm"
                        @input="onSearchInput" />
                    <button v-if="filtersForm.search" @click="clearSearch"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex lg:hidden justify-between items-center mb-4">
                <Button v-can="'create-products'" variant="default" class="h-10" @click="goToCreate">
                    <Plus class="w-4 h-4 mr-2" />
                    Add Product
                </Button>
                <button @click="openFilterSheet"
                    class="flex items-center gap-2 p-2 rounded-full bg-primary-100 dark:bg-primary-900 hover:bg-primary-200 dark:hover:bg-primary-800 shadow transition"
                    title="Show filters for product records">
                    <FilterIcon class="w-6 h-6 text-primary-700 dark:text-primary-200" />
                    <span class="text-primary-700 dark:text-primary-200 font-medium text-base">Filters</span>
                </button>
            </div>
            <!-- Filter Large screen -->
            <div class="lg:flex justify-between gap-4 mb-6 items-end hidden ">
                <div class="grid grid-cols-2 gap-2 w-lg">
                    <div class="flex flex-col">
                        <label class="text-sm font-medium">Category</label>
                        <select v-model="filtersForm.category_id"
                            class="w-full border border-neutral-300 dark:border-neutral-600 rounded px-2 py-1 bg-white dark:bg-neutral-900">
                            <option value="">All</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">
                                {{ c.name }}
                            </option>
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium">Status</label>
                        <select v-model="filtersForm.is_active"
                            class="w-full border border-neutral-300 dark:border-neutral-600 rounded px-2 py-1 bg-white dark:bg-neutral-900">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col">
                    <Button v-can="'create-products'" variant="default" class="h-10" @click="goToCreate">Add
                        Product</Button>
                </div>
            </div>
            <BaseDataTable :headers="headers" :items="products.data" :loading="loading" :server-options="serverOptions"
                :server-items-length="serverItemsLength"
                @update:server-options="(opts: Record<string, any>) => Object.assign(serverOptions, opts)"
                table-class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-md hover:shadow-lg transition-all min-w-full"
                row-class="hover:bg-purple-50 dark:hover:bg-purple-900/60 transition cursor-pointer border-b border-neutral-100 dark:border-neutral-800">
                <template #item-image="row">
                    <img v-if="row.primary_image" :src="row.primary_image" class="w-12 h-12 rounded object-cover" />
                    <div v-else class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center text-xs">
                        N/A
                    </div>
                </template>
                <template #item-name="row">
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ row.name }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ row.description }}</span>
                    </div>
                </template>
                <template #item-is_active="row">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold" :class="row.is_active
                        ? 'bg-green-200 text-green-700'
                        : 'bg-red-200 text-red-700'">
                        {{ row.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </template>
                <template #item-actions="row">
                    <!-- show -->
                    <button class="text-green-500 p-1" @click="showProduct(row.id)" title="View">
                        <Icon name="eye" />
                    </button>
                    <button class="text-blue-500 p-1" @click="editProduct(row.id)" title="Edit">
                        <Icon name="edit" />
                    </button>

                    <button class="text-red-500 p-1" @click="askDeleteProduct(row.id)" title="Delete">
                        <Icon name="trash" />
                    </button>
                </template>
            </BaseDataTable>
        </div>
        <AlertDialog v-model="showDeleteDialog" title="Delete Product"
            message="Are you sure you want to delete this product? This action cannot be undone."
            :confirm-text="'Delete'" :cancel-text="'Cancel'" @confirm="deleteProduct">
            <template #footer>
                <div class="flex gap-2 justify-end">
                    <Button variant="outline" @click="showDeleteDialog = false">Cancel</Button>
                    <Button variant="destructive" @click="deleteProduct">Delete</Button>
                </div>
            </template>
        </AlertDialog>
        <!-- Mobile Filters -->
        <vue-bottom-sheet :overlay="true" :can-swipe="true" :overlay-click-close="true" :transition-duration="0.5"
            ref="filterSheet" class="dark:bg-neutral-900">
            <div class="sheet-content dark:bg-neutral-900">
                <h2 class="text-lg font-semibold mb-4">Product Filters</h2>
                <div class="flex flex-col gap-4">
                    <!-- Category -->
                    <div class="flex flex-col">
                        <label for="category-mobile"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Category
                        </label>
                        <select id="category-mobile" v-model="filtersForm.category_id"
                            class="w-full border border-neutral-300 dark:border-neutral-600 rounded px-2 py-1 bg-white dark:bg-neutral-900">
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <!-- Status -->
                    <div class="flex flex-col">
                        <label for="status-mobile"
                            class="block text-sm font-medium text-neutral-700 dark:text-neutral-200 mb-1">
                            Status
                        </label>
                        <select id="status-mobile" v-model="filtersForm.is_active"
                            class="w-full border border-neutral-300 dark:border-neutral-600 rounded px-2 py-1 bg-white dark:bg-neutral-900">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <!-- Search -->
                    <div class="flex flex-col">
                        <label for="search-mobile"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Search
                        </label>
                        <input id="search-mobile" v-model="filtersForm.search" type="text"
                            placeholder="Search by product name..."
                            class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-neutral-900" />
                    </div>
                    <!-- Actions -->
                    <div class="flex gap-2 pt-2">
                        <Button variant="outline" class="flex-1" @click="closeFilterSheet">
                            Close
                        </Button>
                        <Button variant="default" class="flex-1" @click="fetchData(); closeFilterSheet();">
                            Apply
                        </Button>
                    </div>
                </div>
            </div>
        </vue-bottom-sheet>
    </AppLayout>
</template>

<script setup lang="ts">
import VueBottomSheet from "@webzlodimir/vue-bottom-sheet";
import "@webzlodimir/vue-bottom-sheet/dist/style.css";
import { ref, computed, watch, onMounted } from 'vue';
import { router, Head, usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import BaseDataTable from '@/components/ui/BaseDataTable.vue';
import Button from '@/components/ui/button/Button.vue';
import AlertDialog from '@/components/AlertDialog.vue';
import Icon from '@/components/Icon.vue';
import { BreadcrumbItem, User } from '@/types';
import { Filter as FilterIcon, Plus } from 'lucide-vue-next';
import { toast } from "vue3-toastify";

const filterSheet = ref<InstanceType<typeof VueBottomSheet>>();

function openFilterSheet() {
    filterSheet.value?.open();
}

function closeFilterSheet() {
    filterSheet.value?.close();
}

type ProductsPageProps = any & {
    products: any;
    filters: any;
    auth: any;
};



const page = usePage<ProductsPageProps>();
const products = computed(() => page.props.products);
const initialFilters = page.props.filters || {};
const categories = computed(() => page.props.categories || []);
const auth = computed(() => page.props.auth || {});

const filtersForm = useForm({
    category_id: initialFilters.category_id || '',
    is_active: initialFilters.is_active || '',
    search: initialFilters.search || '',
    page: 1,
    per_page: 12,
});


const loading = ref(false);
const serverOptions = ref({
    page: 1,
    rowsPerPage: 12,
    sortBy: '',
    sortType: '',
    search: '',
    filters: {},
});

watch(() => products.value, (newProducts) => {
    if (newProducts && newProducts.current_page) {
        serverOptions.value.page = newProducts.current_page;
        serverOptions.value.rowsPerPage = newProducts.per_page || 12;
    }
}, { immediate: true });

const serverItemsLength = computed(() => {
    return products.value?.total || 0;
});

const showDeleteDialog = ref(false);
const productToDelete = ref<number | null>(null);


const headers = [
    { text: 'Sr.', value: 'image', sortable: false },
    { text: 'Name', value: 'name' },
    { text: 'Category', value: 'category' },
    { text: 'Price', value: 'price' },
    { text: 'Discount', value: 'discount_price' },
    { text: 'Discount %', value: 'discount_percentage' },
    { text: 'Final Price', value: 'effective_price' },
    { text: 'Stock', value: 'stock' },
    { text: 'Status', value: 'is_active' },
    { text: 'Actions', value: 'actions', sortable: false },
];


const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/' },
    { title: 'Products', href: '/products' },
];


let debounceTimer: number;
type FilterField = 'category_id' | 'is_active';
const watchedFields: FilterField[] = ['category_id', 'is_active'];

watchedFields.forEach((field: FilterField) => {
    watch(() => filtersForm[field], () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchData();
        }, 500); // 500ms debounce
    });
});

watch(serverOptions, fetchData, { deep: true });


function fetchData() {
    loading.value = true;
    filtersForm.page = serverOptions.value.page;
    filtersForm.per_page = serverOptions.value.rowsPerPage;

    filtersForm.get('/products', {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => (loading.value = false),
    });
}

function showProduct(id: number) {
    router.visit(`/products/${id}`);
}

function editProduct(id: number) {
    router.visit(`/products/${id}/edit`);
}

function askDeleteProduct(id: number) {
    productToDelete.value = id;
    showDeleteDialog.value = true;
}

function deleteProduct() {
    if (!productToDelete.value) return;
    loading.value = true;
    router.delete(`/products/${productToDelete.value}`, {
        preserveState: true,
        onSuccess: () => {
            showDeleteDialog.value = false;
            productToDelete.value = null;
            loading.value = false;
            toast.success('Product deleted successfully.');
            fetchData();
        },
        onError: () => {
            loading.value = false;
        },
    });
}
function goToCreate() {
    router.visit('/products/create', {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            console.log('finish')
        },
    });
}
function onSearchInput(event: Event) {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        fetchData();
    }, 500);
}

function clearSearch() {
    filtersForm.search = '';
    fetchData();
}
</script>

<style scoped>
.sheet-content {
    padding: 20px;
}

.overflow-x-auto {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.7);
}

:deep(.v-data-table) {
    min-width: 1200px;
}

:deep(.v-data-table th),
:deep(.v-data-table td) {
    white-space: nowrap;
    padding: 0.75rem 0.5rem;
}

:deep(.v-data-table td .truncate) {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>