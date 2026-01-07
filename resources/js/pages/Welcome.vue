<script setup lang="ts">
import AdsCards from '@/components/AdsCards.vue';
import DiscountOffer from '@/components/DiscountOffer.vue';
import FeatureProductsSection from '@/components/FeatureProductsSection.vue';
import FeatureSection from '@/components/FeatureSection.vue';
import WhyUs from '@/components/WhyUs.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { mapProducts, type Product } from '@/types/product'
import { mapAdProducts, type Card } from '@/types/product'

withDefaults(
    defineProps<{
        canRegister: boolean;

    }>(),
    {
        canRegister: true,
    },
);

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


const page = usePage()
// const products: Product[] = page.props.products ?? []
// console.log('page.props.products', page.props.products)
// const adProducts = page.props.adProducts
const products: Product[] = mapProducts(page.props.products ?? [])

const adProducts: Card[] = mapAdProducts(page.props.adProducts as any[] ?? []);
</script>

<template>
    <PublicLayout hero-title="Welcome to Your Next Adventure"
        hero-subtitle="Seamless login, personalized experience, and a world of possibilities." :hero-buttons="[
            { label: 'Get Started', href: '/register', primary: true },
            { label: 'Login', href: '/login' }
        ]">

        <Head title="Welcome">
            <link rel="preconnect" href="https://rsms.me/" />
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        </Head>
        <div class="bg-neutral-900 max-w-full py-20">
            <AdsCards :cards="adProducts" />
        </div>
        <div class="bg-neutral-900 max-w-full pb-15">
            <FeatureSection :features="features" />
        </div>
        <div class="bg-neutral-900 max-w-full py-15">
            <FeatureProductsSection :products="products" />
        </div>
        <div class="bg-neutral-900 max-w-full pb-15">
            <WhyUs :features="features5" />
        </div>
        <div class="bg-neutral-900 max-w-full py-15">
            <DiscountOffer :discountPercent="30"
                description="Sign up today and receive an exclusive 30% discount code for your first order!"
                @submit="onFormSubmit" />
        </div>
    </PublicLayout>
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
