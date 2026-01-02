<!-- DarkCard.vue -->
<template>
    <BaseProductCard :product="product" cardStyle="dark" @add-to-cart="$emit('add-to-cart', $event)" />
</template>
<script>
import BaseProductCard from "./BaseProductCard.vue";
export default { name: "DarkCard", components: { BaseProductCard }, props: ["product"] };
</script>
