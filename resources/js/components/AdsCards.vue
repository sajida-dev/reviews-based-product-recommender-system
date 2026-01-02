<template>
    <div class="card-container">
        <ProductAdCard v-for="(item, index) in cards" :key="index" :offer-text="item.offer" :title="item.title"
            :image="item.image" :button-text="item.button" @click="onCardClick" />
    </div>
</template>

<script setup>
import ProductAdCard from "./ProductAdCard.vue";

const props = defineProps({
    cards: {
        type: Array,
        default: () => [
            {
                offer: "Upto 25% Off",
                title: "Sports Shoes",
                image:
                    "/img/headphones.png",
                button: "SHOP NOW",
            },
            {
                offer: "Upto 25% Off",
                title: "Kids Collection",
                image:
                    "/img/headphones.png",
                button: "SHOP NOW",
            },
        ],
    },
});

const onCardClick = (title) => {
    alert(`Clicked on: ${title}`);
};
</script>

<style scoped>
.card-container {
    display: flex;
    gap: 30px;
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
}

@media (max-width: 900px) {
    .card-container {
        flex-direction: column;
    }
}
</style>