<!-- Advanced buy page component -->

<template>
    <div>
        <div v-if="successMessage" class="ui success message">
            <span class="halflings halflings-ok-sign icon"></span>
            {{ successMessage }}
        </div>

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
        <User-Chooser v-if="selected.length > 0" 
                :selected="selected"
                :cart="cart"
                :buying="buying">
        </User-Chooser>
        <Cart v-if="cart.length > 0"
                v-on:buy="buy"
                :cart="cart"
                :buying="buying">
        </Cart>
    </div>
</template>

<script>
    import axios from 'axios';

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
                buying: false,
                successMessage: undefined,
            };
        },
        methods: {
            // Commit the current cart as purchase
            buy() {
                // Do not buy if already buying
                if(this.buying)
                    return;
                this.buying = true;

                // Buy the products through an AJAX call
                axios.post('./buy', this.cart)
                    .then(res => {
                        // Show a success message
                        // TODO: specify some nicer dynamic text here
                        this.successMessage = 'Successfully bought some product(s).';

                        // Clear the selected & cart
                        this.selected.splice(0);
                        this.cart.splice(0);

                        window.scrollTo(0, 0);
                    })
                    .catch(err => {
                        alert('Failed to purchase products, an error occurred');
                        console.error(err);
                    })
                    .finally(() => this.buying = false);
            },
        }
    }
</script>
