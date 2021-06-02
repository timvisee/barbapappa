<!-- Kiosk buy page component -->

<template>
    <div>
        <div v-if="refreshing" class="ui active centered indeterminate large text loader">
            {{ __('misc.refreshing') }}...
        </div>

        <div v-if="!refreshing">
            <div v-if="successMessage" class="ui success floating message notification">
                <span class="halflings halflings-ok-sign icon"></span>
                {{ successMessage }}
            </div>

            <div class="ui grid">
                <div class="seven wide column inline">
                    <Users
                            v-on:highlightProducts="highlightProducts"
                            :apiUrl="apiUrl"
                            :selectedUsers="selectedUsers"
                            :cart="cart"
                            :buying="buying" />
                </div>
                <div class="nine wide column inline">
                    <Products
                            v-on:highlightUsers="highlightUsers"
                            :apiUrl="apiUrl"
                            :selectedUsers="selectedUsers"
                            :cart="cart"
                            :buying="buying" />
                </div>
            </div>

            <div class="ui divider hidden"></div>

            <Cart v-if="cart.length > 0"
                    v-on:buy="buy"
                    v-on:cancel="cancel"
                    :selectedUsers="selectedUsers"
                    :cart="cart"
                    :buying="buying" />

        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    const Cart = require('./Cart.vue').default;
    const Products = require('./Products.vue').default;
    const Users = require('./Users.vue').default;

    /**
     * Order timeout in seconds. Cancel current order after number of seconds of
     * inactivity.
     */
    const ORDER_CANCEL_TIMEOUT = 2.5 * 60;

    /**
     * Order timeout in seconds. Cancel current order after number of seconds of
     * inactivity.
     */
    const INACTIVITY_REFRESH_TIMEOUT = 2 * 60 * 60;

    export default {
        components: {
            Cart,
            Products,
            Users,
        },
        data() {
            return {
                selectedUsers: [],
                cart: [],
                buying: false,
                refreshing: false,
                successMessage: undefined,
                // Timer handle after which to clear the success message
                decayTimer: null,
                // Timer handle after which to cancel the current order (inactivity cancel)
                orderCancelTimer: null,
                // Timer handle after which to force reload the interface
                inactiveRefreshTimer: null,
            };
        },
        props: [
            'apiUrl',
        ],
        watch: {
            selectedUsers: function() {
                this.heartbeat();
            },
            cart: function() {
                this.heartbeat();
            },
            successMessage: function(newMsg, oldMsg) {
                if(newMsg != undefined) {
                    clearTimeout(this.decayTimer);
                    this.decayTimer = setTimeout(() => {
                        this.successMessage = undefined;
                    }, 5000);
                }
            },
        },
        created() {
            // Initial heartbeat
            this.heartbeat();

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

                        // Cancel all current selections
                        this.cancel();

                        window.scrollTo(0, 0);
                    })
                    .catch(err => {
                        alert('Failed to purchase products, an error occurred');
                        console.error(err);
                    })
                    .finally(() => this.buying = false);
            },

            // Cancel everything
            cancel() {
                this.selectedUsers.splice(0);
                this.cart.splice(0);

                // TODO: optionally reload list of users/products
            },

            // Invoked on any user activity. Manages inactivity timers.
            heartbeat() {
                // Reset current timers
                clearTimeout(this.orderCancelTimer);
                clearTimeout(this.inactiveRefreshTimer);

                // Set up order inactivity cancel timeout
                this.orderCancelTimer = setTimeout(() => {
                    // Skip if no users selected or nothing in cart
                    if(this.selectedUsers.length == 0 && this.cart.length == 0)
                        return;

                    // Cancel
                    this.cancel();
                }, ORDER_CANCEL_TIMEOUT * 1000);

                // Set up inactive refresh timer
                this.inactiveRefreshTimer = setTimeout(() => {
                    // Skip refresh if order is configured
                    if(this.cart.length > 0)
                        return;

                    // Force refresh
                    console.log("Refreshing kiosk page after time of activity");
                    this.refreshing = true;
                    window.location.reload();
                }, INACTIVITY_REFRESH_TIMEOUT * 1000);
            },

            onClose(event) {
                // Do not prevent closing if nothing is selected
                if(this.cart.length == 0)
                    return;

                // Prevent closing the page, set a warning message
                let msg = this.__('pages.bar.advancedBuy.pageCloseWarning');
                console.log(msg);
                event.preventDefault();
                event.returnValue = msg;
                return msg;
            },

            // Hint to select a user.
            highlightUsers() {
                // TODO: propegate to users model
                $('.panel-users')
                    .transition('stop')
                    .transition('glow');
            },

            // Hint to select products.
            highlightProducts() {
                // TODO: propegate to products model
                $('.panel-products')
                    .transition('stop')
                    .transition('glow');
            },
        },
    }
</script>

<style>
    /**
     * Remove all padding on small screens.
     */
    @media only screen and (max-width:767px) {
        .page {
            margin-top: 14px;
        }

        .column.inline {
            padding: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            padding-top: 0 !important;
            border-radius: 0;
        }

        .ui.menu,
        .ui.vertical.menu {
            border-radius: 0;
            box-shadow: none !important;
        }
    }

    .notification {
        position: fixed !important;
        top: 64px;
        left: 14px;
        right: 14px;
        z-index: 1001;

        /* TODO: do not use this hack! */
        width: calc(100% - 28px) !important;
    }
</style>
