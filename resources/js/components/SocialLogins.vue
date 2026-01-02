<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
        <!-- Google -->
        <a href="/auth/google" class="flex w-full items-center justify-center gap-2 rounded-lg
               bg-white text-gray-700 border border-gray-300
               px-1.5 py-2.5 font-medium
               hover:bg-gray-50 hover:shadow-sm
               transition">
            <!-- Google Icon -->
            <svg class="h-5 w-5" viewBox="0 0 48 48">
                <path fill="#EA4335"
                    d="M24 9.5c3.54 0 6.02 1.54 7.4 2.82l5.46-5.46C33.24 3.88 28.97 2 24 2 14.61 2 6.73 7.38 3.69 14.97l6.39 4.97C11.78 14.1 17.41 9.5 24 9.5z" />
                <path fill="#4285F4"
                    d="M46.1 24.5c0-1.64-.15-3.21-.42-4.73H24v9.01h12.44c-.54 2.9-2.18 5.36-4.62 7.02l7.1 5.52C43.02 37.15 46.1 31.4 46.1 24.5z" />
                <path fill="#FBBC05"
                    d="M10.08 28.94c-.48-1.43-.75-2.96-.75-4.44s.27-3.01.75-4.44l-6.39-4.97C1.24 18.11 0 21.01 0 24.5s1.24 6.39 3.69 9.41l6.39-4.97z" />
                <path fill="#34A853"
                    d="M24 46c6.48 0 11.92-2.14 15.9-5.82l-7.1-5.52c-1.97 1.33-4.5 2.12-8.8 2.12-6.59 0-12.22-4.6-14.22-10.44l-6.39 4.97C6.73 40.62 14.61 46 24 46z" />
            </svg>

            <span class="text-sm">Continue with Google</span>
        </a>

        <!-- Facebook -->
        <a href="/auth/facebook" class="flex w-full items-center justify-center gap-2 rounded-lg
               bg-[#1877F2] text-white
               px-1.5 py-2.5 font-medium
               hover:bg-[#166FE5]
               transition">
            <!-- Facebook Icon -->
            <svg class="h-5 w-5 rounded-full fill-white" viewBox="0 0 24 24">
                <path
                    d="M22.675 0h-21.35C.597 0 0 .597 0 1.326v21.348C0 23.403.597 24 1.326 24h11.495v-9.294H9.691V11.01h3.13V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.796.715-1.796 1.763v2.312h3.587l-.467 3.696h-3.12V24h6.116C23.403 24 24 23.403 24 22.674V1.326C24 .597 23.403 0 22.675 0z" />
            </svg>

            <span class="text-sm">Continue with Facebook</span>
        </a>
    </div>
</template>