<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue'
import { dashboard, login, register } from '@/routes'
import { Link, usePage } from '@inertiajs/vue3'
import MegaMenu from '../navigation/MegaMenu.vue';

defineProps<{
    canRegister?: boolean
}>()

const page = usePage()
const user = page.props.auth.user
</script>

<template>
    <header class="fixed top-0 left-0 right-0 z-50 mx-auto mt-4 max-w-6xl
           rounded-xl bg-white/20 backdrop-blur-md border border-white/30
           px-6 py-3 flex items-center justify-between">
        <!-- Left: Logo -->
        <div class="flex items-center gap-2">
            <AppLogo />
        </div>

        <!-- Center: Menu -->
        <nav class="hidden md:flex items-center gap-6 text-white/80 font-medium">
            <Link href="/" class="hover:text-white transition">Home</Link>
            <MegaMenu />
            <Link href="/features" class="hover:text-white transition">Features</Link>
            <Link href="/about" class="hover:text-white transition">About</Link>
            <Link href="/contact" class="hover:text-white transition">Contact</Link>
        </nav>

        <!-- Right: Auth Buttons -->
        <div class="flex items-center gap-3">
            <Link v-if="user" :href="dashboard()" class="rounded-full bg-primary backdrop-blur-md
               px-5 py-2 text-sm font-semibold text-white
               hover:bg-white/30 transition">
                Dashboard
            </Link>

            <template v-else>
                <Link :href="login()" class="rounded-full bg-primary backdrop-blur-md
                 px-5 py-2 text-sm font-semibold text-white
                 hover:bg-primary/50 transition">
                    Log in
                </Link>

                <Link v-if="canRegister" :href="register()" class="rounded-full border border-white/40
                 px-5 py-2 text-sm font-semibold text-white
                 hover:bg-white/20 transition">
                    Register
                </Link>
            </template>
        </div>
    </header>
</template>
