<!-- Advanced buy page component -->

<template>
    <div id="advancedbuy">
        <div v-if="successMessage" class="ui success message">
            <span class="halflings halflings-ok-sign icon"></span>
            {{ successMessage }}
        </div>

        <center>
            <p v-if="selected.length == 0 && cart.length == 0">
                {{ __('pages.bar.advancedBuy.tapProducts') }}
            </p>
            <p v-else-if="cart.length == 0">
                {{ __('pages.bar.advancedBuy.tapUsers') }}
            </p>
            <p v-else>
                {{ __('pages.bar.advancedBuy.tapBuy') }}
            </p>
        </center>

        <Products :apiUrl="apiUrl" :selected="selected" />
        <Users v-if="selected.length > 0" 
                v-on:queryResults="deferredDontShrink"
                :apiUrl="apiUrl"
                :selected="selected"
                :cart="cart"
                :buying="buying" />
        <Cart v-if="cart.length > 0"
                v-on:buy="buy"
                :cart="cart"
                :buying="buying" />
    </div>
</template>

<script>
    import axios from 'axios';

    const Cart = require('./Cart.vue').default;
    const Products = require('./Products.vue').default;
    const Users = require('./Users.vue').default;

    export default {
        components: {
            Cart,
            Products,
            Users,
        },
        data() {
            return {
                selected: [],
                cart: [],
                buying: false,
                successMessage: undefined,
            };
        },
        watch: {
            selected: function() {
                this.dontShrink();
            },
        },
        props: [
            'apiUrl',
        ],
        created() {
            // Prevent accidental closing
            window.addEventListener('beforeunload', this.onClose);
        },
        methods: {
            // Commit the current cart as purchase
            buy() {
                // Do not buy if already buying
                if(this.buying)
                    return;
                this.buying = true;

                // Buy the products through an AJAX call
                axios.post(this.apiUrl + '/buy', this.cart)
                    .then(res => {
                        // Build the success message
                        let products = res.data.productCount;
                        let users = res.data.userCount;
                        this.successMessage = users <= 1
                            ? this.langChoice('pages.bar.advancedBuy.boughtProducts#', products)
                            : this.langChoice('pages.bar.advancedBuy.boughtProductsUsers#', products, {users});

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

            onClose(event) {
                // Do not prevent closing if nothing is selected
                if(this.cart.length == 0 && this.selected.length == 0)
                    return;

                // Prevent closing the page, set a warning message
                let msg = this.__('pages.bar.advancedBuy.pageCloseWarning');
                console.log(msg);
                event.preventDefault();
                event.returnValue = msg;
                return msg;
            },

            // Defer call to prevent widget shrinking to next event cycle.
            // Deferred to the page layout has time to adjust, after which the
            // don't shrink logic is called.
            deferredDontShrink() {
                setTimeout(this.dontShrink);
            },

            // Prevent buy widget from shrinking at the current state.
            dontShrink() {
                let widget = $('#advancedbuy');
                let height = widget.height();
                if(height != undefined)
                    widget.css('minHeight', height + 'px');
            }
        },
    }
</script>
