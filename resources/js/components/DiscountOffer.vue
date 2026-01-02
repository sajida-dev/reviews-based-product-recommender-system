<template>
    <section class="max-w-7xl mx-auto mt-10 px-15 py-30 bg-yellow-400 rounded-md flex flex-col md:flex-row gap-10">
        <!-- Text Section -->
        <div class="flex-1 text-gray-900">
            <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-4">
                Get <span class="text-red-600">{{ discountPercent }}% Discount</span><br />
                on your first purchase
            </h1>
            <p class="text-gray-800 text-base md:text-lg leading-relaxed max-w-md">
                {{ description }}
            </p>
        </div>

        <!-- Form Section -->
        <div class="flex-1">
            <form @submit.prevent="submitForm" class="flex flex-col gap-2">
                <!-- Name Input -->
                <TextInput label="Name" v-model="form.name" :error="form.errors.name" required class="w-full" />

                <!-- Email Input -->
                <TextInput label="Email" v-model="form.email" :error="form.errors.email" required class="w-full" />

                <!-- Subscribe Checkbox -->
                <label class="flex items-center gap-2 text-gray-900 text-sm">
                    <input type="checkbox" v-model="form.subscribe"
                        class="w-4 h-4 text-yellow-500 rounded border-gray-300 focus:ring-2 focus:ring-yellow-400" />
                    Subscribe to the newsletter
                </label>

                <!-- Submit Button -->
                <button type="submit" :disabled="form.processing"
                    class="mt-4 w-full bg-gray-900 text-white py-3 rounded-lg text-lg font-semibold hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Submit
                </button>
            </form>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import TextInput from "./form/TextInput.vue";
import { toast } from "vue3-toastify";

// Props
const props = defineProps({
    discountPercent: { type: Number, default: 25 },
    description: {
        type: String,
        default:
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Dictumst amet, metus, sit massa posuere maecenas. At tellus ut nunc amet vel egestas.",
    },
});

// Toast

// Inertia form
const form = useForm({
    name: "",
    email: "",
    subscribe: false,
});

// Submit handler
const submitForm = () => {
    form.post("/newsletter/subscribe", {
        onSuccess: () => {
            toast.success(`Thank you, ${form.name}! You’ve successfully submitted.`, {
                closeOnClick: true,
            });
            form.reset("name", "email", "subscribe");
        },
        onError: () => {
            toast.error("Oops! Something went wrong. Please try again.", {
                closeOnClick: true,
            });
        },
    });
};
</script>

<style scoped>
/* Keep any additional scrollbars or overrides if needed */
</style>
