<!-- NormalCard.vue -->
<template>
    <BaseProductCard :product="product" cardStyle="" @add-to-cart="$emit('add-to-cart', $event)" />
</template>
<script>
import BaseProductCard from "./BaseProductCard.vue";
export default { name: "NormalCard", components: { BaseProductCard }, props: ["product"] };
</script>
