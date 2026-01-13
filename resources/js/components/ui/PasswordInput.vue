<script setup lang="ts">
import { ref } from 'vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import { Eye, EyeOff } from 'lucide-vue-next'

defineProps<{
    id: string
    name: string
    label: string
    error?: string
    tabindex?: number
    autocomplete?: string
    placeholder?: string
}>()

const showPassword = ref(false)
</script>

<template>
    <div class="grid gap-2">
        <Label :for="id">{{ label }}</Label>

        <div class="relative">
            <Input :id="id" :name="name" :type="showPassword ? 'text' : 'password'" :autocomplete="autocomplete"
                :placeholder="placeholder" :tabindex="tabindex" required class="pr-10" />

            <button type="button"
                class="absolute inset-y-0 right-2 flex items-center text-muted-foreground hover:text-foreground"
                @click="showPassword = !showPassword" tabindex="-1">
                <Eye v-if="!showPassword" class="h-4 w-4" />
                <EyeOff v-else class="h-4 w-4" />
            </button>
        </div>

        <InputError :message="error" />
    </div>
</template>
