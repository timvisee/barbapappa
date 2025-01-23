<!-- Main buy page component -->

<template>
    <div>
        <div class="ui two item menu">
            <a href="#" class="item" @click.prevent.stop="self = true" v-bind:class="{ active: self }">{{ __('pages.bar.buy.forMe') }}</a>
            <a href="#" class="item" @click.prevent.stop="self = false" v-bind:class="{ active: !self }">{{ __('pages.bar.buy.forOthers') }}</a>
        </div>

        <div v-if="!stateOnline" class="ui message error">
            <span class="halflings halflings-exclamation-sign icon"></span>
            {{ __('pages.bar.bannerNoConnection') }}
        </div>

        <!-- Main UI -->
        <Self ref="self" v-if="self" :apiUrl="apiUrl" :barUrl="barUrl" />
        <Other ref="other" v-else :apiUrl="apiUrl" />
    </div>
</template>

<script>
    import axios from 'axios';

    const Self = require('./Self.vue').default;
    const Other = require('./Other.vue').default;

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

    /**
     * Success message timeout in seconds.
     */
    const SUCCESS_MESSAGE_TIMEOUT = 5;

    /**
     * Time to show bought/cancel overlay in seconds.
     */
    const OVERLAY_TIMEOUT = 1.5;

    /**
     * Timeout for buy requests in seconds.
     */
    const BUY_REQUEST_TIMEOUT = 5;

    /**
     * Timeout for buy requests draining in the background in seconds.
     */
    const BUY_REQUEST_BACKGROUND_TIMEOUT = 30;

    /**
     * Key for buy queue data to store in local storage.
     */
    const BUY_QUEUE_DATA_KEY = 'kiosk-buy-queue';

    /**
     * Delay between each buy queue item when draining the queue.
     *
     * This delay is required to prevent rate limiting.
     */
    const BUY_QUEUE_DRAIN_INTERVAL = 2;

    export default {
        components: {
            Self,
            Other,
        },
        data() {
            return {
                self: true,
                swapped: false,
                selectedUsers: [],
                selectedProducts: [],
                cart: [],
                confirming: false,
                buying: false,
                showBoughtOverlay: false,
                showCancelledOverlay: false,
                successMessage: undefined,
                // Timer handle after which to clear the success message
                decayTimer: null,
                stateOnline: navigator.onLine,
                // Whether to show the cart reset modal
                showResetModal: false,
                // Number of items currently in the deferred buy queue
                buyQueueLength: 0,
                // True when we're currently draining
                buyQueueDraining: false,
            };
        },
        props: [
            'barUrl',
            'apiUrl',
        ],
        watch: {
            successMessage: function(newMsg, oldMsg) {
                if(newMsg != undefined) {
                    clearTimeout(this.decayTimer);
                    this.decayTimer = setTimeout(() => {
                        this.successMessage = undefined;
                    }, SUCCESS_MESSAGE_TIMEOUT * 1000);
                }
            },
            stateOnline: function(newState) {
                // Skip if we're now online
                if(!newState) {
                    return;
                }

                // When we come online, try to drain buy queue
                this._buyQueueDrainAllDelayed();
            },
        },
        created() {
            // Listen to network events
            window.addEventListener("online", (e) => this.stateOnline = true);
            window.addEventListener("offline", (e) => this.stateOnline = false);

            // Prevent accidental closing
            window.addEventListener('beforeunload', this.onClose);

            // Update buy queue length
            this.buyQueueLength = this._buyQueueLoad().length;

            // Drain any items from buy queue if we're online
            if(this.stateOnline) {
                this._buyQueueDrainAllDelayed();
            }
        },
        methods: {
            // Commit the current cart as purchase
            buy() {
                // Do not buy if already buying
                if(this.buying)
                    return;
                this.buying = true;

                // Create buy data object, add unique UUID
                let timestamp = Date.now() / 1000;
                let buyData = {
                    uuid: this.uuidv4(),
                    initiated_at: timestamp,
                    cart: JSON.parse(JSON.stringify(this.cart)),
                };

                // Buy the products through an AJAX call
                this._buyOrQueue(buyData)
                    .then(res => {
                        // Show bought overlay for 1 second
                        this.showBoughtOverlay = true;
                        setTimeout(() => this.showBoughtOverlay = false, OVERLAY_TIMEOUT * 1000);

                        // Cancel all current selections
                        this.cancel(false);

                        // Reset scroll
                        window.scrollTo(0, 0);

                        // Process any queued purchases
                        this._buyQueueDrainAllDelayed();
                    })
                    .catch(err => {
                        alert(err.response.data.message ?? 'Failed to purchase products, an error occurred');
                        console.error(err);
                    })
                    .finally(() => this.buying = false);
            },

            // Attempt to buy products.
            // Will try over network. Falls back to defer buy on buy queue.
            _buyOrQueue(data) {
                return this
                    // Attempt to submit buy POST
                    ._sendBuyRequest(data, false)
                    // Fall back to deferred queue
                    .then(null, reject => {
                        // If we got a response, don't queue, forward error
                        if(reject.response)
                            return Promise.reject(reject);

                        // Add buy request to queue
                        this._buyQueuePush(data);
                        return Promise.resolve({});
                    });
            },

            // Cancel everything
            cancel(showOverlay = true) {
                // Show cancelled overlay for 1 second
                if(showOverlay) {
                    this.showCancelledOverlay = true;
                    setTimeout(() => this.showCancelledOverlay = false, OVERLAY_TIMEOUT * 1000);
                }

                // Reset selections
                this.selectedUsers.splice(0);
                this.selectedProducts.splice(0);
                this.removeAllUserCarts();

                // Reset query fields
                this.$refs.users.query = '';
                this.$refs.products.query = '';

                // Reset swap
                this.resetSwap();

                // Hide reset modal
                this.showResetModal = false;
            },

            // Confirming state.
            setConfirming(confirming) {
                this.confirming = !!confirming;
            },

            onClose(event) {
                if(this.self)
                    return this.$refs.self.onClose(event);
                else
                    return this.$refs.other.onClose(event);
            },

            // Swap the view.
            swap() {
                // Toggle swap.
                this.swapped = !this.swapped;

                // Highlight first column
                if(!this.swapped)
                    setTimeout(() => this.highlightUsers(), 0);
                else
                    this.highlightProducts();
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
            addCartQuantity(cart, product, diff = 1) {
                this.setCartQuantity(cart, product, this.getCartQuantity(cart, product) + diff);
            },

            // Merge products from cart into other cart.
            mergeCart(from, target) {
                if(from == null || target == null || from.products == undefined || target.products == undefined)
                    return;

                // Change quantities in target to merge.
                from.products.forEach((item) => {
                    this.addCartQuantity(target, item.product, item.quantity);
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
            },

            // Send a buy request with the given data.
            // Has no error handling, retry or fallback methods.
            _sendBuyRequest(data, isBackground) {
                return axios
                    .post(this.apiUrl + '/buy', data, {
                        timeout: (isBackground ? BUY_REQUEST_BACKGROUND_TIMEOUT : BUY_REQUEST_TIMEOUT) * 1000,
                    });
            },

            // Load all buy queue data.
            _buyQueueLoad() {
                return JSON.parse(localStorage.getItem(BUY_QUEUE_DATA_KEY)) || [];
            },

            // Store all buy queue data.
            _buyQueueStore(queue) {
                if(queue.length !== 0) {
                    localStorage.setItem(BUY_QUEUE_DATA_KEY, JSON.stringify(queue));
                } else {
                    localStorage.removeItem(BUY_QUEUE_DATA_KEY);
                }

                // Update buy queue length when storing data
                this.buyQueueLength = queue.length || 0;
            },

            // Push a new buy item into the buy queue.
            _buyQueuePush(data) {
                let queue = this._buyQueueLoad();
                queue.push(data);
                this._buyQueueStore(queue);
            },

            // Peek the first item of the buy queue.
            _buyQueuePeek() {
                // Load queue
                let queue = this._buyQueueLoad();
                if(queue == undefined || queue.length === 0) {
                    return null;
                }
                return queue[0];
            },

            // Pop the first item of the buy queue.
            _buyQueuePop() {
                let queue = this._buyQueueLoad();
                let item = queue.shift();
                this._buyQueueStore(queue);
                return item;
            },

            // Drain one item of the buy queue.
            // Returns null if there is none, returns promise if draining.
            _buyQueueDrainOne() {
                // Only drain by one process at a time
                if(this.buyQueueDraining) {
                    return null;
                }
                this.buyQueueDraining = true;

                // Load queue
                let item = this._buyQueuePeek();
                if(item == null) {
                    this.buyQueueDraining = false;
                    return null;
                }

                return this
                    ._sendBuyRequest(item, true)
                    .then(() => {
                        // TODO: do some sanity checks, we have data field?

                        // Pop first item from queue
                        this._buyQueuePop();
                    })
                    .finally(() => {
                        this.buyQueueDraining = false;
                    });
            },

            // Drain all items of the buy queue one by one.
            _buyQueueDrainAll() {
                let item = this._buyQueueDrainOne();
                if(item == null) {
                    return;
                }

                item.then(() => {
                    this._buyQueueDrainAllDelayed();
                });
            },

            // Drain all items of the buy queue one by one, but wait first.
            _buyQueueDrainAllDelayed() {
                setTimeout(() => {
                    this._buyQueueDrainAll();
                }, BUY_QUEUE_DRAIN_INTERVAL * 1000);
            },

            // Generate random UUID.
            uuidv4() {
                return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
                    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
                );
            },
        },
    }
</script>

<style lang="scss">
    .notification {
        position: fixed !important;
        bottom: 0;
        left: 14px;
        right: 14px;
        z-index: 1001;

        /* TODO: do not use this hack! */
        width: calc(100% - 28px) !important;
    }

    .ui.dimmer.on-top {
        z-index: 1002;
    }

    .ui.dimmer.positive {
        background-color: rgba(33, 186, 69, .85);
        color: white;

        .glyphicons {
            color: white;
        }
    }

    .ui.dimmer.negative {
        background-color: rgba(219, 40, 40, .85);
        color: white;

        .glyphicons {
            color: white;
        }
    }

    .ui.dimmer .text.huge {
        font-weight: bold;
        font-size: 2em;
        padding: 1em;
        line-height: 2;
    }
</style>
