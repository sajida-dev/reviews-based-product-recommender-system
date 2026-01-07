<template>
    <div class="card-container">
        <ProductAdCard v-for="(item, index) in cards" :key="index" :offer-text="item.offer" :title="item.title"
            :image="item.image" :slug="item.slug" :button-text="item.button" @click="onCardClick" />
    </div>
</template>

<script setup lang="ts">
import { Card } from "@/types/product";
import ProductAdCard from "./ProductAdCard.vue";

const props = defineProps<{ cards: Card[] }>();
const cards = props.cards ?? [];


const onCardClick = (title: string) => {
    console.log(`Clicked on: ${title}`);
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