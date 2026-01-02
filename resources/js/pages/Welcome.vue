<script setup lang="ts">
import AdsCards from '@/components/AdsCards.vue';
import DiscountOffer from '@/components/DiscountOffer.vue';
import FeatureProductsSection from '@/components/FeatureProductsSection.vue';
import FeatureSection from '@/components/FeatureSection.vue';
import Footer from '@/components/layouts/Footer.vue';
import PublicHeader from '@/components/layouts/PublicHeader.vue';
import Reviews from '@/components/Reviews.vue';
import WhyUs from '@/components/WhyUs.vue';
import { login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);


const productCards = [
    {
        offer: "Upto 30% Off",
        title: "Men’s Sneakers",
        image: "/img/headphones.png",
        button: "SHOP NOW",
    },
    {
        offer: "Flat 20% Off",
        title: "Women’s Heels",
        image: "/img/headphones.png",
        button: "SHOP NOW",
    },
];
const onFormSubmit = (data: any) => {
    console.log("Form Submitted:", data);
    // You can send this to backend or show a success message
};
const handleSocial = (icon: string) => {
    console.log("Clicked social icon:", icon);
};

const handleLinkClick = (link: string) => {
    console.log("Clicked footer link:", link);
};

const features = [
    {
        icon: 'fa-solid fa-cart-shopping',
        title: 'Pick Up In Store',
        description: 'At imperdiet dui accumsan sit amet nulla risus est ultricies quis.'
    },
    {
        icon: 'fa-solid fa-gift',
        title: 'Special Packaging',
        description: 'At imperdiet dui accumsan sit amet nulla risus est ultricies quis.'
    },
    {
        icon: 'fa-solid fa-store',
        title: 'Free Global Returns',
        description: 'At imperdiet dui accumsan sit amet nulla risus est ultricies quis.'
    }
]
const features5 = [
    {
        icon: 'fa-solid fa-cart-shopping',
        title: 'Free delivery',
        description: 'Lorem ipsum dolor sit amet, consectetur adipi elit.'
    },
    {
        icon: 'fa-solid fa-shield-halved',
        title: '100% secure payment',
        description: 'Lorem ipsum dolor sit amet, consectetur adipi elit.'
    },
    {
        icon: 'fa-solid fa-store',
        title: 'Quality guarantee',
        description: 'Lorem ipsum dolor sit amet, consectetur adipi elit.'
    },
    {
        icon: 'fa-solid fa-heart-circle-check',
        title: 'Guaranteed savings',
        description: 'Lorem ipsum dolor sit amet, consectetur adipi elit.'
    },
    {
        icon: 'fa-solid fa-gift',
        title: 'Daily offers',
        description: 'Lorem ipsum dolor sit amet, consectetur adipi elit.'
    }
]

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
    <div class="bg-neutral-900 max-w-full py-20">
        <AdsCards :cards="productCards" />
    </div>
    <div class="bg-neutral-900 max-w-full pb-15">
        <FeatureSection :features="features" />
    </div>
    <div class="bg-neutral-900 max-w-full py-15">
        <FeatureProductsSection />
    </div>
    <div class="bg-neutral-900 max-w-full py-15">
        <DiscountOffer :discountPercent="30"
            description="Sign up today and receive an exclusive 30% discount code for your first order!"
            @submit="onFormSubmit" />
    </div>
    <div class="bg-neutral-900 max-w-full pb-15">
        <WhyUs :features="features5" />
    </div>
    <Footer logo-text="E-Com" @social-click="handleSocial" @link-click="handleLinkClick" />
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
