<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Categories" />

        <div class="max-w-6xl mx-auto w-full py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Categories</h1>
                <Button @click="openCreateModal">Add Category</Button>
            </div>

            <!-- Table -->
            <BaseDataTable :headers="headers" :items="categories" :loading="loading">
                <template #["item-parent"]="row">
                    {{ row.parent?.name ?? '—' }}
                </template>

                <template #["item-actions"]="row">
                    <button class="text-blue-500 p-1" @click="openEditModal(row)" title="Edit">
                        <Edit class="w-5 h-5" />
                    </button>

                    <button class="text-red-500 p-1" @click="handleDelete(row)" title="Delete">
                        <Trash class="w-5 h-5" />
                    </button>
                </template>
            </BaseDataTable>
        </div>

        <!-- Create / Edit Modal -->
        <Dialog v-model:open="modalOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>
                        {{ isEdit ? 'Edit Category' : 'Add Category' }}
                    </DialogTitle>
                </DialogHeader>

                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <TextInput label="Name" v-model="form.name" required :error="form.errors.name" />

                    <TextInput label="Slug" v-model="form.slug" required :error="form.errors.slug" />

                    <p class="text-xs text-gray-500 mt-1">
                        Auto-generated from name. You can edit it manually if needed.
                    </p>

                    <SelectInput label="Parent Category" v-model="form.parent_id" :options="parentOptions"
                        placeholder="None" :error="form.errors.parent_id" />

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Description
                        </label>
                        <textarea v-model="form.description" rows="3" class="w-full border rounded px-3 py-2" />
                        <p v-if="form.errors.description" class="text-red-500 text-sm">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <Button variant="outline" @click="closeModal" type="button">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="loading">
                            {{ isEdit ? 'Update' : 'Create' }}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Category?</DialogTitle>
                </DialogHeader>

                <p class="mb-4">
                    This category cannot be deleted if it has products or child categories.
                </p>

                <DialogFooter>
                    <Button variant="outline" @click="cancelDelete">Cancel</Button>
                    <Button variant="destructive" @click="confirmDelete">
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import { Edit, Trash } from 'lucide-vue-next'
import { route } from 'ziggy-js'
import AppLayout from '@/layouts/AppLayout.vue'
import BaseDataTable from '@/components/ui/BaseDataTable.vue'
import { Button } from '@/components/ui/button'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import TextInput from '@/components/form/TextInput.vue'
import SelectInput from '@/components/form/SelectInput.vue'
import { BreadcrumbItem } from '@/types'
const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Categories', href: '/admin/categories' },
];
interface Category {
    id: number
    name: string
    slug: string
    description?: string
    parent?: { id: number; name: string }
}

const props = defineProps<{
    categories: Category[]
    parentOptions: { label: string; value: number }[]
}>()

const categories = computed(() => props.categories)
const loading = ref(false)

const modalOpen = ref(false)
const isEdit = ref(false)

const showDeleteDialog = ref(false)
const itemToDelete = ref<Category | null>(null)
const slugManuallyEdited = ref(false)

const form = useForm({
    id: null as number | null,
    name: '',
    slug: '',
    parent_id: undefined as number | undefined,
    description: '',
})

const headers = [
    { text: 'ID', value: 'id' },
    { text: 'Name', value: 'name' },
    { text: 'Slug', value: 'slug' },
    { text: 'Parent', value: 'parent', slotName: 'item-parent' },
    { text: 'Actions', value: 'actions', slotName: 'item-actions' },
]

function openCreateModal() {
    isEdit.value = false
    form.reset()
    slugManuallyEdited.value = false
    modalOpen.value = true
}
function generateSlug(value: string) {
    return value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
}

watch(
    () => form.name,
    (newName) => {
        // if (!slugManuallyEdited.value) {
        form.slug = generateSlug(newName)
        // }
    }
)
watch(
    () => form.slug,
    () => {
        slugManuallyEdited.value = true
    }
)


function openEditModal(row: Category) {
    isEdit.value = true
    form.reset()
    form.id = row.id
    form.name = row.name
    form.slug = row.slug
    slugManuallyEdited.value = true
    form.parent_id = row.parent?.id ?? undefined
    form.description = row.description ?? ''
    modalOpen.value = true
}

function closeModal() {
    modalOpen.value = false
    form.reset()
}
function handleSubmit() {
    loading.value = true

    if (isEdit.value && form.id) {
        form.put(route('admin.categories.update', form.id), {
            onSuccess: () => {
                toast.success('Category updated')
                closeModal()
                router.reload({ only: ['categories'] })
            },
            onFinish: () => (loading.value = false),
        })
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => {
                toast.success('Category created')
                closeModal()
                router.reload({ only: ['categories'] })
            },
            onFinish: () => (loading.value = false),
        })
    }
}

function handleDelete(row: Category) {
    itemToDelete.value = row
    showDeleteDialog.value = true
}

function confirmDelete() {
    if (!itemToDelete.value) return

    router.delete(route('admin.categories.destroy', itemToDelete.value.id), {
        onSuccess: () => {
            toast.success('Category deleted')
            showDeleteDialog.value = false
            router.reload({ only: ['categories'] })
        },
        onError: () => {
            toast.error('Cannot delete category with products or children')
        },
    })
}

function cancelDelete() {
    showDeleteDialog.value = false
}
</script>
