<template>
    <div class="mb-4">
        <Label v-if="label" :for="id" class="mb-1">
            {{ label }}<span v-if="required" class="text-red-500">*</span>
        </Label>
        <select :id="id" :value="modelValue" @change="$emit('update:modelValue', $event.target.value)"
            :required="required"
            class="w-full border bg-white dark:bg-neutral-900 border-input dark:border-input text-gray-900 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            v-bind="$attrs">
            <option value="" disabled>Select...</option>
            <option v-for="option in options" :key="typeof option === 'object' ? option.value : option"
                :value="typeof option === 'object' ? option.value : option">
                {{ typeof option === 'object' ? option.label : option }}
            </option>
        </select>
        <InputError :message="error" />
    </div>
</template>
<script setup>
import Label from '@/components/ui/label/Label.vue';
import InputError from '@/components/InputError.vue';
defineProps({
    label: String,
    modelValue: [String, Number],
    error: String,
    required: Boolean,
    options: Array,
    id: { type: String, default: () => Math.random().toString(36).substr(2, 9) }
});
defineEmits(['update:modelValue']);
</script>