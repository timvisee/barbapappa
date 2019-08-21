<!-- Advanced buy page component -->

<template>
    <div>
        <Product-Select :selected="selected"></Product-Select>
        <User-Chooser v-if="selected.length > 0" :selected="selected" :cart="cart"></User-Chooser>
        <Cart v-if="cart.length > 0" :cart="cart"></Cart>
    </div>
</template>

<script>
    const Cart = require('./Cart.vue').default;
    const ProductSelect = require('./ProductSelect.vue').default;
    const UserChooser = require('./UserChooser.vue').default;

    export default {
        components: {
            Cart,
            ProductSelect,
            UserChooser,
        },
        data() {
            return {
                selected: [],
                cart: [],
            };
        },
    }
</script>
