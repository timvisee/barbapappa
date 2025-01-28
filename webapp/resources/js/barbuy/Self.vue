<!-- User buy page component for self -->

<template>
    <div class="ui stackable 2 column grid">
        <div class="nine wide column inline">

            <!-- Product list -->
            <Products
                    ref="products"
                    :apiUrl="apiUrl"
                    :barUrl="barUrl"
                    :self="true"
                    :selectedUsers="selectedUsers"
                    :selectedProducts="selectedProducts"
                    :cart="cart"
                    :buying="false"
                    :buyCounts="buyCounts"
                    :_buyProduct="buy"
                    :_getUserCart="getUserCart"
                    :_getSelectCart="getSelectCart"
                    :_getCartQuantity="getCartQuantity"
                    :_setCartQuantity="setCartQuantity"
                    :_addCartQuantity="addCartQuantity"
                    :_getCartSize="getCartSize"
                    :_removeCart="removeCart"
                    :_getBuyQueueQuantity="getBuyQueueQuantity" />

        </div>
        <div class="seven wide column inline">

            <!-- History -->
            <History
                    ref="history"
                    :apiUrl="apiUrl"
                    :barUrl="barUrl" />

            <!-- Buy queue notification -->
            <div v-if="buyQueueCache.length > 0" class="ui message warning">
                <span class="halflings halflings-synchronization icon"></span>
                {{ buyQueueCache.length == 1
                    ? __('pages.bar.bannerProcessingTransactionsOne')
                    : __('pages.bar.bannerProcessingTransactionsMany').replace(':count', buyQueueCache.length)
                }}
                <a href="#" @click.prevent.stop="resetBuyQueue" class="float-right">{{ __('general.stop') }}</a>
            </div>

        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    // TODO: remove cart?
    const Cart = require('./Cart.vue').default;
    const Products = require('./Products.vue').default;
    const History = require('./History.vue').default;

    /**
     * Key for buy queue data to store in local storage.
     */
    const BUY_QUEUE_DATA_KEY = 'barbuy-self-buy-queue';

    /**
     * Delay between each buy queue item when draining the queue.
     *
     * This delay is required to prevent rate limiting.
     */
    const BUY_QUEUE_DRAIN_INTERVAL = 1;

    /**
     * Seconds after which we reset the buy counts.
     */
    const BUY_COUNT_RESET_DELAY = 5;

    export default {
        components: {
            Cart,
            Products,
            History,
        },
        data() {
            return {
                selectedUsers: [],
                selectedProducts: [],
                cart: [],
                successMessage: undefined,
                // Timer handle after which to clear the success message
                decayTimer: null,
                stateOnline: navigator.onLine,
                // Cache holding the buy queue state
                buyQueueCache: [],
                // True when we're currently draining
                buyQueueDraining: false,
                // Current purchase count for user, used to show count on client
                buyCounts: {},
                // Timer for resetting the purchase count
                buyCountResetTimer: null,
            };
        },
        props: [
            'apiUrl',
            'barUrl',
        ],
        watch: {
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

            // Update buy queue length
            this.buyQueueCache = this._buyQueueLoad();

            // Drain any items from buy queue if we're online
            if(this.stateOnline)
                this._buyQueueDrainAllDelayed();
        },
        methods: {
            // Buy the given product
            buy(product) {
                // Increase buy count
                let id = product.id;
                if(id in this.buyCounts)
                    this.buyCounts[id] += 1;
                else
                    this.buyCounts[id] = 1;

                // Create buy data object, add unique UUID
                let timestamp = Date.now() / 1000;
                let buyData = {
                    uuid: this.uuidv4(),
                    initiated_at: timestamp,
                    product: JSON.parse(JSON.stringify(product)),
                };

                // Add item to buy queue
                this._buyQueuePush(buyData);

                // Initiate draining queue
                this._buyQueueDrainAll();
            },

            onClose(event) {
                // Do not prevent closing if nothing is in queue
                if(this.buyQueueCache.length == 0)
                    return;

                // Prevent closing the page, set a warning message
                let msg = this.__('pages.bar.advancedBuy.pageCloseWarning');
                console.log(msg);
                event.preventDefault();
                event.returnValue = msg;
                return msg;
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

            // Get quantity of products in buy queue
            getBuyQueueQuantity(product) {
                return this.buyQueueCache.filter(d => d.product.id == product.id).length;
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

            // Remove the cart for a given user.
            removeUserCart(user) {
                if(user == null || user.id === undefined)
                    return;

                // Find user cart, then remove it
                let i = this.cart.findIndex(c => c.user.id == user.id);
                if(i >= 0)
                    this.cart.splice(i, 1);
            },

            // Reset the current buy queue.
            resetBuyQueue() {
                this._buyQueueStore([]);
                this.buyCounts = {};
            },

            // Send a buy request with the given data.
            // Has no error handling, retry or fallback methods.
            _sendBuyRequest(data, isBackground) {
                return axios
                    .post(this.apiUrl + '/buy/self', data);
            },

            // Load all buy queue data.
            _buyQueueLoad() {
                this.buyQueueCache =
                    JSON.parse(localStorage.getItem(BUY_QUEUE_DATA_KEY)) || [];
                return this.buyQueueCache;
            },

            // Store all buy queue data.
            _buyQueueStore(queue) {
                if(queue.length !== 0) {
                    localStorage.setItem(BUY_QUEUE_DATA_KEY, JSON.stringify(queue));
                } else {
                    localStorage.removeItem(BUY_QUEUE_DATA_KEY);
                }

                this.buyQueueCache = queue;
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

                        // Start timer to reset counts after a while
                        if(this.buyCountResetTimer != null)
                            clearTimeout(this.buyCountResetTimer);
                        if(this._buyQueueLoad().length == 0) {
                            this.buyCountResetTimer = setTimeout(() => {
                                this.buyCounts = {};
                            }, BUY_COUNT_RESET_DELAY * 1000);
                        }

                        this.onBoughtProduct();
                    })
                    .catch(err => {
                        alert(err.response.data.message ?? 'Failed to purchase products, an error occurred');
                        console.error(err);
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

            // Fired when a product is succesfully bought (in the background)
            onBoughtProduct() {
                // Refresh history
                this.$refs.history.refresh();
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
    .float-right {
        float: right;
    }
</style>
