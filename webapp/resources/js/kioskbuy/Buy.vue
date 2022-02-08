<!-- Kiosk buy page component -->

<template>
    <div>
        <div v-if="!stateOnline" class="banner">
            <span class="halflings halflings-exclamation-sign icon"></span>
            {{ __('pages.kiosk.noConnectionBanner') }}
        </div>

        <div v-if="refreshing" class="ui active centered indeterminate large text loader">
            {{ __('misc.refreshing') }}...
        </div>

        <!-- Confirming/buying, bought, cancelled overlay -->
        <div v-if="confirming || buying" class="ui active dimmer on-top"></div>
        <div v-if="showBoughtOverlay" class="ui active dimmer positive on-top">
            <div class="ui text huge">{{ __('misc.bought') }}!</div>
        </div>
        <div v-if="showCancelledOverlay" class="ui active dimmer negative on-top">
            <div class="ui text huge">{{ __('misc.cancelled') }}!</div>
        </div>

        <!-- Main UI -->
        <div v-if="!refreshing">
            <div v-if="successMessage" class="ui success floating message notification">
                <span class="halflings halflings-ok-sign icon"></span>
                {{ successMessage }}
            </div>

            <!-- Users and product list, reverse if swapped -->
            <div class="ui grid">
                <div v-if="!swapped" class="seven wide column inline">
                    <Users
                            v-on:swap="swap"
                            v-on:highlightProducts="highlightProducts"
                            :apiUrl="apiUrl"
                            :swapped="swapped"
                            :selectedUsers="selectedUsers"
                            :selectedProducts="selectedProducts"
                            :cart="cart"
                            :buying="buying"
                            :_getUserCart="getUserCart"
                            :_mergeCart="mergeCart"
                            :_removeAllUserCarts="removeAllUserCarts"
                            :_getTotalCartQuantity="getTotalCartQuantity" />
                </div>
                <div class="nine wide column inline">
                    <Products
                            v-on:swap="swap"
                            v-on:highlightUsers="highlightUsers"
                            :apiUrl="apiUrl"
                            :swapped="swapped"
                            :selectedUsers="selectedUsers"
                            :selectedProducts="selectedProducts"
                            :cart="cart"
                            :buying="buying"
                            :_getUserCart="getUserCart"
                            :_getSelectCart="getSelectCart"
                            :_getCartQuantity="getCartQuantity"
                            :_setCartQuantity="setCartQuantity"
                            :_changeCartQuantity="changeCartQuantity"
                            :_getCartSize="getCartSize"
                            :_removeCart="removeCart" />
                </div>
                <div v-if="swapped" class="seven wide column inline">
                    <Users
                            v-on:swap="swap"
                            v-on:highlightProducts="highlightProducts"
                            :apiUrl="apiUrl"
                            :swapped="swapped"
                            :selectedUsers="selectedUsers"
                            :selectedProducts="selectedProducts"
                            :cart="cart"
                            :buying="buying"
                            :_getUserCart="getUserCart"
                            :_mergeCart="mergeCart"
                            :_removeAllUserCarts="removeAllUserCarts"
                            :_getTotalCartQuantity="getTotalCartQuantity" />
                </div>
            </div>

            <div class="ui divider hidden"></div>
            <div class="ui divider hidden"></div>

            <Cart v-if="cart.length > 0"
                    v-on:buy="buy"
                    v-on:cancel="cancel"
                    v-on:confirming="setConfirming"
                    :selectedUsers="selectedUsers"
                    :cart="cart"
                    :buying="buying"
                    :_getTotalCartQuantity="getTotalCartQuantity" />

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
                swapped: false,
                selectedUsers: [],
                selectedProducts: [],
                cart: [],
                confirming: false,
                buying: false,
                refreshing: false,
                showBoughtOverlay: false,
                showCancelledOverlay: false,
                successMessage: undefined,
                // Timer handle after which to clear the success message
                decayTimer: null,
                // Timer handle after which to cancel the current order (inactivity cancel)
                orderCancelTimer: null,
                // Timer handle after which to force reload the interface
                inactiveRefreshTimer: null,
                stateOnline: true,
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

            // Listen to network events
            window.addEventListener("online", (e) => this.stateOnline = true);
            window.addEventListener("offline", (e) => this.stateOnline = false);

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

                        // Show bought overlay for 1 second
                        this.showBoughtOverlay = true;
                        setTimeout(() => this.showBoughtOverlay = false, 1000);

                        // Cancel all current selections
                        this.cancel(false);

                        window.scrollTo(0, 0);
                    })
                    .catch(err => {
                        alert('Failed to purchase products, an error occurred');
                        console.error(err);
                    })
                    .finally(() => this.buying = false);
            },

            // Cancel everything
            cancel(showOverlay = true) {
                // Show cancelled overlay for 1 second
                if(showOverlay) {
                    this.showCancelledOverlay = true;
                    setTimeout(() => this.showCancelledOverlay = false, 1000);
                }

                this.selectedUsers.splice(0);
                this.selectedProducts.splice(0);
                this.removeAllUserCarts();
                this.resetSwap();

                // TODO: optionally reload list of users/products
            },

            // Confirming state.
            setConfirming(confirming) {
                this.confirming = !!confirming;
            },

            // Invoked on any user activity. Manages inactivity timers.
            heartbeat() {
                // Reset current timers
                clearTimeout(this.orderCancelTimer);
                clearTimeout(this.inactiveRefreshTimer);

                // Set up order inactivity cancel timeout
                this.orderCancelTimer = setTimeout(() => {
                    // Skip if no users selected or nothing in cart
                    // TODO: also check selected users!
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

            // Swap the view.
            swap() {
                // Toggle swap.
                this.swapped = !this.swapped;

                // TODO: reset queries
                // TODO: reset selections

                // Reset selected users
                this.selectedUsers.splice(0);
                this.selectedProducts.splice(0);
            },

            // Reset swap state.
            resetSwap() {
                // If swapped, reset
                if(this.swapped)
                    this.swap();
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

            // Get cart for given user.
            getUserCart(user, create = false) {
                if(user == null)
                    return null;

                let cart = this.cart.filter(c => c.user.id == user.id)[0] || null;
                if(cart != null || !create)
                    return cart;

                // Create cart
                this.cart.push({
                    user,
                    products: [],
                });
                return this.getUserCart(user, false);
            },

            // Get selection cart.
            getSelectCart(create = false) {
                // Create cart if it doesn't exist
                if(create && this.selectedProducts.length == 0)
                    this.selectedProducts.push({
                        user: null,
                        products: [],
                    });

                // Return cart or null
                return this.selectedProducts[0] || null;
            },

            // Get quantity of all user carts.
            getTotalCartQuantity() {
                if(this.cart == null || this.cart.length == 0)
                    return 0;

                return this.cart
                    .map(i => i.products
                        .map(p => p.quantity)
                        .reduce((a, b) => a + b)
                    )
                    .reduce((a, b) => a + b);
            },

            // Get the selection quantity for a given product
            getCartQuantity(cart, product) {
                if(cart == null)
                    return 0;

                let item = cart.products.filter(p => p.id == product.id);
                return item.length > 0 ? item[0].quantity : 0;
            },

            // Set product quantity in user cart.
            setCartQuantity(cart, product, quantity) {
                if(cart == null)
                    return;

                if(quantity > 0) {
                    // Add/get product, set quantity
                    let item = cart.products.filter(p => p.id == product.id);
                    if(item.length > 0)
                        item[0].quantity = quantity;
                    else
                        cart.products.push({
                            id: product.id,
                            quantity: quantity,
                            product,
                        });
                } else {
                    // Remove product from cart
                    let i = cart.products.findIndex(p => p.id == product.id);
                    if(i >= 0)
                        cart.products.splice(i, 1);

                    // If user does not have products anymore, remove cart
                    if(this.getCartSize(cart) <= 0)
                        this.removeCart(cart);
                }
            },

            // Change quantity by given amount
            changeCartQuantity(cart, product, diff = 1) {
                this.setCartQuantity(cart, product, this.getCartQuantity(cart, product) + diff);
            },

            // Merge products from cart into other cart.
            mergeCart(from, target) {
                if(from == null || target == null || from.products == undefined || target.products == undefined)
                    return;

                // Change quantities in target to merge.
                from.products.forEach((item) => {
                    this.changeCartQuantity(target, item.product, item.quantity);
                });
            },

            // Get the number of products in the given cart.
            getCartSize(cart) {
                if(cart == null || cart.products == undefined)
                    return 0;
                return cart.products.reduce((sum, product) => product.quantity + sum, 0);
            },

            // Remove the given cart.
            //
            // If cart has no user specified, it is considered to be the
            // selection cart which is then removed.
            removeCart(cart) {
                if(cart == null)
                    return;

                // If no user, remove selection cart
                if(cart.user == null) {
                    this.selectedProducts.splice(0);
                    return;
                }

                this.removeUserCart(cart.user);
            },

            // Remove all user carts.
            removeAllUserCarts() {
                this.cart.splice(0);
            },

            // Remove the cart for a given user.
            removeUserCart(user) {
                if(user == null || user.id === undefined)
                    return;

                // Find user cart, then remove it
                let i = this.cart.findIndex(c => c.user.id == user.id);
                if(i >= 0)
                    this.cart.splice(i, 1);
            }
        },
    }
</script>

<style>
    body {
        /* Prevent any accidental selections in kiosk mode */
        user-select: none;
    }

    /**
     * Remove all padding on small screens.
     */
    @media only screen and (max-width:767px) {
        .page {
            margin-top: 14px;
        }

        .banner {
            margin-top: -1rem;
            margin-left: -1rem;
            margin-right: -1rem;
            border-radius: 0 !important;
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

    .banner {
        background: #db2828;
        color: #fff;
        margin-bottom: 1rem;
        padding: 1em;
        border-radius: .28571429rem;
    }

    .banner .icon {
        margin-right: .35em;
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

    .ui.dimmer.on-top {
        z-index: 1001;
    }

    .ui.dimmer.positive {
        background-color: rgba(33, 186, 69, .85);
        color: white;
    }

    .ui.dimmer.negative {
        background-color: rgba(219, 40, 40, .85);
        color: white;
    }

    .ui.dimmer .text.huge {
        font-weight: bold;
        font-size: 2em;
        padding: 1em;
        line-height: 2;
    }
</style>
