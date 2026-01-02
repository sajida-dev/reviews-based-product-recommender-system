<template>
    <div class="mb-4">
        <label :for="id" class="block text-neutral-700 dark:text-neutral-200 mb-1">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>

        <div class="relative flex flex-col items-center justify-center border-2 border-dashed border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 cursor-pointer transition hover:border-blue-400 min-h-[120px] text-center"
            @dragover.prevent @drop.prevent="onDrop" @click="triggerFileInput">
            <input ref="fileInput" :id="id" type="file" accept="image/png,image/jpeg" :multiple="multiple"
                class="hidden" @change="onFileChange" />

            <InputError :error="error" />

            <!-- SINGLE IMAGE PREVIEW -->
            <div v-if="singlePreview" class="flex flex-col items-center">
                <img :src="singlePreview" class="h-24 w-24 object-cover rounded border mb-2" />
                <span class="text-xs text-neutral-500">Preview</span>
            </div>

            <!-- MULTIPLE FILE COUNT -->
            <div v-else-if="multiple && fileCount > 0" class="text-sm text-neutral-500">
                {{ fileCount }} files selected
            </div>

            <!-- EMPTY STATE -->
            <div v-else class="flex flex-col items-center py-6">
                <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12" />
                </svg>
                <span class="font-semibold text-neutral-500 mt-2">
                    Click to upload
                </span>
                <span class="text-neutral-400 text-sm">
                    or drag and drop
                </span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import InputError from '../InputError.vue';

const emit = defineEmits<{
    (e: 'update:modelValue', value: File | File[] | null): void;
}>();

const props = defineProps<{
    label?: string;
    modelValue: File | File[] | null;
    required?: boolean;
    error?: string;
    multiple?: boolean;
    id?: string;
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);

const fileCount = computed(() =>
    Array.isArray(props.modelValue) ? props.modelValue.length : 0
);

const singlePreview = computed(() =>
    !props.multiple && previewUrl.value
);

function triggerFileInput() {
    fileInput.value?.click();
}

function onFileChange(e: Event) {
    const input = e.target as HTMLInputElement;
    if (!input.files) return;

    const files = Array.from(input.files);
    emit('update:modelValue', props.multiple ? files : files[0] ?? null);
    setPreview(files[0]);
}

function onDrop(e: DragEvent) {
    if (!e.dataTransfer?.files) return;

    const files = Array.from(e.dataTransfer.files);
    emit('update:modelValue', props.multiple ? files : files[0] ?? null);
    setPreview(files[0]);
}

function setPreview(file?: File | null) {
    if (!file || !file.type.startsWith('image/')) {
        previewUrl.value = null;
        return;
    }
    previewUrl.value = URL.createObjectURL(file);
}

watch(
    () => props.modelValue,
    (value) => {
        if (Array.isArray(value)) {
            setPreview(value[0]);
        } else {
            setPreview(value);
        }
    },
    { immediate: true }
);
</script>
