<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import PublicHeader from '@/components/layouts/PublicHeader.vue';
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>

    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="flex min-h-screen flex-col items-center justify-center bg-[url('https://mir-s3-cdn-cf.behance.net/project_modules/source/244d47129717169.6170bea8eee75.gif')] bg-cover bg-center p-6 text-[#1b1b18] dark:bg-[#0a0a0a]">
        <PublicHeader :canRegister="canRegister" />
        <!-- Hero Content -->
        <div class="flex flex-col items-center justify-center text-center mt-28 lg:mt-32 px-4">
            <h1 class="text-4xl lg:text-6xl font-extrabold text-white drop-shadow-lg">
                Welcome to Your Next Adventure
            </h1>
            <p class="mt-4 text-lg lg:text-2xl text-white/80 max-w-2xl drop-shadow-md">
                Seamless login, personalized experience, and a world of possibilities at your fingertips.
            </p>

            <!-- Call to Action Buttons -->
            <div class="mt-8 flex gap-4">
                <Link v-if="canRegister" :href="register()"
                    class="rounded-full bg-primary backdrop-blur-md px-6 py-3 text-sm font-semibold text-white hover:bg-primary/50 transition">
                    Get Started
                </Link>
                <Link :href="login()"
                    class="rounded-full border border-white/30 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20 transition">
                    Login
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Hero animation */
h1 {
    animation: fadeInUp 1s ease forwards;
    opacity: 0;
}

p {
    animation: fadeInUp 1.3s ease forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }

    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
