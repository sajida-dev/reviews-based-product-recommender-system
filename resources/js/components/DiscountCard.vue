<!-- DiscountCard.vue -->
<template>
    <BaseProductCard :product="product" cardStyle="tagged" @add-to-cart="$emit('add-to-cart', $event)" />
</template>
<script>
import BaseProductCard from "./BaseProductCard.vue";
export default { name: "DiscountCard", components: { BaseProductCard }, props: ["product"] };
</script>
