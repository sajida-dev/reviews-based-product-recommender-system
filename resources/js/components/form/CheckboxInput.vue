<template>
    <div class="mb-4">
        <div class="flex items-center">
            <input :id="id" type="checkbox" :checked="modelValue"
                @change="$emit('update:modelValue', $event.target.checked)" class="form-checkbox mr-2"
                v-bind="$attrs" />
            <Label v-if="label" :for="id">
                {{ label }}<span v-if="required" class="text-red-500">*</span>
            </Label>
        </div>
        <InputError :message="error" />
    </div>
</template>
<script setup>
import Label from '@/components/ui/label/Label.vue';
import InputError from '@/components/InputError.vue';
defineProps({
    label: String,
    modelValue: Boolean,
    error: String,
    required: Boolean,
    id: { type: String, default: () => Math.random().toString(36).substr(2, 9) }
});
defineEmits(['update:modelValue']);
</script>