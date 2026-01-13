<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PasswordInput from '@/components/ui/PasswordInput.vue';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/password/confirm';
import { Form, Head } from '@inertiajs/vue3';
</script>

<template>
    <AuthLayout title="Confirm your password"
        description="This is a secure area of the application. Please confirm your password before continuing.">

        <Head title="Confirm password" />

        <Form v-bind="store.form()" reset-on-success v-slot="{ errors, processing }">
            <div class="space-y-6">
                <PasswordInput id="password" name="password" label="Password" autocomplete="current-password"
                    placeholder="Password" :tabindex="2" :error="errors.password" />

                <div class="flex items-center">
                    <Button class="w-full" :disabled="processing" data-test="confirm-password-button">
                        <Spinner v-if="processing" />
                        Confirm Password
                    </Button>
                </div>
            </div>
        </Form>
    </AuthLayout>
</template>
