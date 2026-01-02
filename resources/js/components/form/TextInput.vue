<template>
    <div class="mb-4">
        <Label v-if="label" :for="id" class="mb-1">
            {{ label }}<span v-if="required" class="text-red-500">*</span>
        </Label>
        <Input :id="id" :modelValue="modelValue" @update:modelValue="$emit('update:modelValue', $event)" :type="type"
            :required="required" v-bind="$attrs" />
        <InputError :message="error" />
    </div>
</template>
<script setup>
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import InputError from '@/components/InputError.vue';
defineProps({
    label: String,
    modelValue: [String, Number],
    error: String,
    type: { type: String, default: 'text' },
    required: Boolean,
    id: { type: String, default: () => Math.random().toString(36).substr(2, 9) }
});
defineEmits(['update:modelValue']);
</script>