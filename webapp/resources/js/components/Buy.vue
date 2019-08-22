<!-- Advanced buy page component -->

<template>
    <div>
        <center>
            <p v-if="selected.length == 0 && cart.length == 0">
                Tap products to buy for any user.
            </p>
            <p v-else-if="cart.length == 0">
                Tap users to add the selected products in cart for.
            </p>
            <p v-else>
                Press the blue buy button to commit the purchase.
            </p>
        </center>

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
