<template>
    <div class="ui vertical huge menu inverted fluid panel-products">

        <div v-if="!swapped && selectedUsers.length == 0"
                v-on:click="hintUsers()"
                class="ui active dimmer">
            <div class="ui text inverted">
                {{ __('pages.kiosk.firstSelectUser') }}

                <div class="ui hidden divider"></div>

                <em class="opacity-50">{{ __('general.or') }}</em>

                <div class="ui hidden divider"></div>

                <a v-on:click.stop.prevent="swap()"
                        class="ui big button basic primary inverted dimmer-swap opacity-50"
                        href="#">
                    <i class="halflings halflings-retweet"></i>
                    {{ __('pages.kiosk.productMode') }}
                </a>
            </div>
        </div>

        <h5 class="ui item header">
            {{ __('pages.kiosk.selectProducts') }}

            <div class="header-actions">

                <a v-if="swapped"
                        v-on:click.stop.prevent="swap()"
                        href="#"
                        :title="__('pages.kiosk.swapColumns')">
                    <i class="halflings halflings-retweet"></i>
                </a><!--

                --><a v-if="isSelectMode() && selectedProducts.length && !buying"
                        v-on:click.stop.prevent="reset(); query = ''"
                        href="#"
                        class="negative">
                    <i class="halflings halflings-remove"></i>
                </a><!--

                --><a v-if="!isSelectMode() && getCartSize() > 0 && !buying"
                        v-on:click.stop.prevent="removeCart(); query = ''"
                        href="#"
                        class="negative">
                    <i class="halflings halflings-trash"></i>
                </a>
            </div>
        </h5>

        <div class="item">
            <div class="ui transparent icon input">
                <input v-model="query"
                        @input="e => query = e.target.value"
                        @focus="e => e.target.select()"
                        type="search"
                        :placeholder="__('pages.products.search') + '...'"
                        autocomplete="off" />
                <div v-if="searching" class="ui active inline tiny loader"></div>
                <i v-if="!searching && !query" v-on:click.prevent.stop="search(query)" class="icon link">
                    <span class="glyphicons glyphicons-search"></span>
                </i>
                <i v-if="!searching && query" v-on:click.prevent.stop="query = ''" class="icon link">
                    <span class="glyphicons glyphicons-remove"></span>
                </i>
            </div>
        </div>

        <!-- Top products -->
        <a v-if="products"
                v-for="product in products.top"
                v-on:click.stop.prevent="changeQuantity(product, 1)"
                href="#"
                class="item kiosk-select-item prominent"
                v-bind:class="{ disabled: buying || (product.exhausted && getQuantity(product) <= 0), active: getQuantity(product) > 0 }">
            <div class="item-text">
                <span v-if="getQuantity(product) > 0" class="subtle quantity">
                    <span v-if="isSelectMode()">+{{ getQuantity(product) }}</span>
                    <span v-else>{{ getQuantity(product) }}×</span>
                </span>

                <span v-if="product.exhausted && getQuantity(product) <= 0" class="halflings halflings-trash"></span>

                {{ product.name }}
            </div>

            <div v-if="getQuantity(product) == 0" class="item-label">
                <div class="ui blue large label">{{ product.price_display }}</div>
            </div>

            <div v-if="getQuantity(product)" class="item-buttons">
                <div class="ui two buttons">
                    <a href="#"
                            v-on:click.stop.prevent="quantityModal(product)"
                            v-bind:class="{ disabled: buying }"
                            class="ui large button black">
                        <i class="glyphicons glyphicons-hash"></i>
                    </a>

                    <a href="#"
                            v-on:click.stop.prevent="setQuantity(product, 0)"
                            v-bind:class="{ red: !isSelectMode(), black: isSelectMode(), disabled: buying }"
                            class="ui large button">
                        <i class="glyphicons glyphicons-remove"></i>
                    </a>
                </div>
            </div>
        </a>

        <!-- Main product list (some top, recents, search results) -->
        <a v-for="product in products.list"
                v-on:click.stop.prevent="changeQuantity(product, 1)"
                href="#"
                class="green inverted item kiosk-select-item"
                v-bind:class="{ disabled: buying || (product.exhausted && getQuantity(product) <= 0), active: getQuantity(product) > 0 }">
            <div class="item-text">
                <span v-if="getQuantity(product) > 0" class="subtle quantity">
                    <span v-if="isSelectMode()">+{{ getQuantity(product) }}</span>
                    <span v-else>{{ getQuantity(product) }}×</span>
                </span>

                <span v-if="product.exhausted && getQuantity(product) <= 0" class="halflings halflings-trash"></span>

                {{ product.name }}
            </div>

            <div v-if="getQuantity(product) == 0" class="item-label">
                <div class="ui label"
                        v-bind:class="{ blue: !product.exhausted, black: product.exhausted}">
                    {{ product.price_display }}
                </div>
            </div>

            <div v-if="getQuantity(product)" class="item-buttons">
                <div class="ui two buttons">
                    <a href="#"
                            v-on:click.stop.prevent="quantityModal(product)"
                            v-bind:class="{ disabled: buying }"
                            class="ui large button black">
                        <i class="glyphicons glyphicons-hash"></i>
                    </a>

                    <a href="#"
                            v-on:click.stop.prevent="setQuantity(product, 0)"
                            v-bind:class="{ red: !isSelectMode(), black: isSelectMode(), disabled: buying }"
                            class="ui large button">
                        <i class="glyphicons glyphicons-remove"></i>
                    </a>
                </div>
            </div>
        </a>

        <!-- Search indicators -->
        <i v-if="searching && productCount == 0 && query != ''" class="item">
            {{ __('pages.products.searchingFor', {term: query}) }}...
        </i>
        <i v-if="searching && productCount == 0 && query == 0" class="item">
            {{ __('misc.loading') }}...
        </i>
        <i v-if="!searching && productCount == 0" class="item">
            {{ __('pages.products.noProductsFoundFor', {term: query}) }}.
        </i>

        <!-- Selected products not in list (always show on bottom) -->
        <a v-for="product in productsBacklog"
                v-on:click.stop.prevent="changeQuantity(product, 1)"
                href="#"
                class="green inverted item kiosk-select-item"
                v-bind:class="{ disabled: buying, active: getQuantity(product) > 0 }">
            <div class="item-text">
                <span v-if="getQuantity(product) > 0" class="subtle quantity">
                    <span v-if="isSelectMode()">+{{ getQuantity(product) }}</span>
                    <span v-else>{{ getQuantity(product) }}×</span>
                </span>

                {{ product.name }}
            </div>

            <div class="item-buttons">
                <div class="ui two buttons">
                    <a href="#"
                            v-on:click.stop.prevent="quantityModal(product)"
                            v-bind:class="{ disabled: buying }"
                            class="ui large button black">
                        <i class="glyphicons glyphicons-hash"></i>
                    </a>

                    <a href="#"
                            v-on:click.stop.prevent="setQuantity(product, 0)"
                            v-bind:class="{ red: !isSelectMode(), black: isSelectMode(), disabled: buying }"
                            class="ui large button">
                        <i class="glyphicons glyphicons-remove"></i>
                    </a>
                </div>
            </div>
        </a>

        <!-- Product quantity selection modal -->
        <QuantityModal :initialQuantity="quantityModalQuantity" @onSubmit="onQuantityModalSubmit" />
    </div>
</template>

<script>
    import axios from 'axios';

    const QuantityModal = require('./QuantityModal.vue').default;

    /**
     * Delay for warming up all products cache in service worker.
     */
    const CACHE_WARMUP_DELAY = 5;

    export default {
        components: {
            QuantityModal,
        },
        data() {
            return {
                query: '',
                searching: true,
                products: {
                    top: [],
                    list: [],
                },
                quantityModalQuantity: null,
                quantityModalCallback: null,
            };
        },
        computed: {
            productCount: function() {
                return this.products != null
                        ? this.products.top.length + this.products.list.length
                        : 0;
            },

            // Products that are selected but not in current results
            productsBacklog: function() {
                let cart = this.getCart();
                if(cart == null)
                    return [];

                return cart.products
                    .map(p => p.product)
                    .filter(p => !this.isProductInResult(p));
            },
        },
        watch: {
            query: function() {
                this.search(this.query);
            },
            cart: function() {
                // Any modal should close on cart change
                this.quantityModalQuantity = undefined;
            },
        },
        mounted: function() {
            // Plain search for default product list
            this.search();

            // Warm up service worker cache for list of all users
            setTimeout(
                () => this._searchRequest(null, true),
                CACHE_WARMUP_DELAY * 1000,
            );
        },
        props: [
            'apiUrl',
            'swapped',
            'selectedUsers',
            'selectedProducts',
            'cart',
            'buying',
            '_getUserCart',
            '_getSelectCart',
            '_getCartQuantity',
            '_setCartQuantity',
            '_addCartQuantity',
            '_getCartSize',
            '_removeCart',
        ],
        methods: {
            // If we're currently in user selection mode.
            isSelectMode() {
                return this.swapped;
            },

            // TODO: rename this to bag?
            // Get the current cart.
            // In normal mode, returns cart of selected user, or null.
            // In swapped mode, returns selection cart.
            getCart(create = false) {
                if(!this.isSelectMode())
                    return this.getUserCart(null, create);
                else
                    return this._getSelectCart(create);
            },

            // Get user cart.
            getUserCart(user, create = false) {
                // Use selected user if not provided
                if(user == null || user == undefined)
                    user = this.selectedUsers[0];

                return this._getUserCart(user, create);
            },

            // Get the selection quantity for a given product
            getQuantity(product) {
                return this._getCartQuantity(this.getCart(), product);
            },

            // Set product quantity in user cart.
            setQuantity(product, quantity) {
                return this._setCartQuantity(this.getCart(true), product, quantity);
            },

            // Change quantity by given amount
            changeQuantity(product, diff = 1) {
                // In selection mode, highlight user column
                if(this.isSelectMode())
                    this.hintUsers();

                return this._addCartQuantity(this.getCart(true), product, diff);
            },

            // Get the number of products in the current user cart
            getCartSize() {
                return this._getCartSize(this.getCart());
            },

            // Remove current cart.
            removeCart() {
                this._removeCart(this.getCart());
            },

            // Search products with the given query
            search(query = null, all = false) {
                // Fetch a list of products, set the searching state
                this.searching = true;
                this._searchRequest(query, all)
                    .then(products => this.products = products)
                    .catch(err => {
                        alert('An error occurred while listing products');
                        console.error(err);
                    })
                    .finally(() => this.searching = false);
            },

            // Do a search request.
            _searchRequest(query = null, all = false) {
                // Build URL
                let url = new URL(this.apiUrl + '/products');
                if(query != null && query.length > 0)
                    url.searchParams.append('q', encodeURIComponent(query));
                if(all)
                    url.searchParams.append('all', 'true');

                // Fetch a list of products
                return axios.get(url.toString()).then(res => res.data);
            },

            // Check if given product is in current search result list
            isProductInResult(product) {
                return this.products.top.filter(p => p.id == product.id).length > 0
                    || this.products.list.filter(p => p.id == product.id).length > 0;
            },

            // Reset selection
            reset() {
                this.selectedProducts.splice(0);
            },

            // Hint to select a user first
            hintUsers() {
                if(this.selectedProducts.length > 0)
                    return;
                this.$emit('highlightUsers');
            },

            // Swap view
            swap() {
                this.$emit('swap');
            },

            // Show quantity modal for product
            quantityModal(product) {
                this.quantityModalCallback = (q) => this.setQuantity(product, q);
                this.quantityModalQuantity = this.getQuantity(product);
            },

            // Called on quantity modal submit
            onQuantityModalSubmit(quantity) {
                // Call configured submit callback once
                if(this.quantityModalCallback != null) {
                    if(this.quantityModalQuantity != null)
                        this.quantityModalCallback(quantity);
                    this.quantityModalCallback = null;
                }

                // Reset assigned modal
                this.quantityModalQuantity = undefined;
            },
        },
    }
</script>

<style>
    /**
     * Remove all padding on small screens.
     */
    @media only screen and (max-width:767px) {
        .kiosk-select-item {
            border-radius: 0 !important;
        }
    }

    .kiosk-select-item {
        display: flex !important;
        justify-content: space-between;
        align-items: stretch;
        overflow: hidden;
        padding: 0 !important;
    }

    /* Left aligned text (label) */
    .kiosk-select-item .item-text {
        flex-grow: 1;
        padding: .92857143em 1.14285714em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Right aligned label */
    .kiosk-select-item .item-label {
        flex-shrink: 0;
        padding-right: 1.14285714em;
        overflow: hidden;

        display: flex;
        align-items: center;
    }

    /* Right aligned buttons */
    .kiosk-select-item .item-buttons {
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        flex-direction: row;
        align-items: stretch;
    }

    .kiosk-select-item .item-buttons .button {
        text-align: center;
        padding: .92857143em 1.125em;
        line-height: 1.1;
        border-radius: 0 !important;
    }

    .button .glyphicons {
        vertical-align: middle;
    }

    .button .glyphicons::before {
        padding: 0;
    }

    .quantity,
    .item.active {
        font-weight: bold !important;
    }

    /* Inverted colors */
    .kiosk-select-item.active {
        background: rgba(0, 182, 240, 0.45) !important;
    }

    /* Prominent items on top of product list */
    .kiosk-select-item.prominent {
        font-size: 1.15em;
        font-weight: bold !important;
        line-height: 1.7 !important;
        height: 67.99338px;
        height: calc(48.5667px * 7 / 5);
    }

    .kiosk-select-item.prominent:not(.disabled) {
        border-left: 4px solid #db2828 !important;
    }

    .kiosk-select-item.prominent:nth-of-type(5n+1) {
        border-color: #db2828 !important;
    }
    .kiosk-select-item.prominent:nth-of-type(5n+1) .ui.label {
        background-color: #db2828 !important;
    }
    .kiosk-select-item.prominent:nth-of-type(5n+1).active {
        color: #db2828 !important;
        /* background: rgba(219, 40, 40, 0.05) !important; */
        background: rgba(219, 40, 40, 0.45) !important;
    }

    .kiosk-select-item.prominent:nth-of-type(5n+2) {
        border-color: #f2711c !important;
    }
    .kiosk-select-item.prominent:nth-of-type(5n+2) .ui.label {
        background-color: #f2711c !important;
    }
    .kiosk-select-item.prominent:nth-of-type(5n+2).active {
        color: #f2711c !important;
        /* background: rgba(242, 113, 28, 0.05) !important; */
        background: rgba(242, 113, 28, 0.45) !important;
    }

    .kiosk-select-item.prominent:nth-of-type(5n+3) {
        border-color: #fbbd08 !important;
    }
    .kiosk-select-item.prominent:nth-of-type(5n+3) .ui.label {
        background-color: #fbbd08 !important;
    }
    .kiosk-select-item.prominent:nth-of-type(5n+3).active {
        color: #fbbd08 !important;
        /* background: rgba(251, 189, 8, 0.05) !important; */
        background: rgba(251, 189, 8, 0.45) !important;
    }

    .kiosk-select-item.prominent:nth-of-type(5n+4) {
        border-color: #b5cc18 !important;
    }
    .kiosk-select-item.prominent:nth-of-type(5n+4) .ui.label {
        background-color: #b5cc18 !important;
    }
    .kiosk-select-item.prominent:nth-of-type(5n+4).active {
        color: #b5cc18 !important;
        /* background: rgba(181, 204, 24, 0.05) !important; */
        background: rgba(181, 204, 24, 0.45) !important;
    }

    .kiosk-select-item.prominent:nth-of-type(5n+5) {
        border-color: #21ba45 !important;
    }
    .kiosk-select-item.prominent:nth-of-type(5n+5) .ui.label {
        background-color: #21ba45 !important;
    }
    .kiosk-select-item.prominent:nth-of-type(5n+5).active {
        color: #21ba45 !important;
        /* background: rgba(33, 186, 69, 0.05) !important; */
        background: rgba(33, 186, 69, 0.45) !important;
    }

    .kiosk-select-item.prominent .item-buttons .button {
        font-size: 1.4rem !important;;
        line-height: 1.4 !important;;
    }

    .ui.dimmer .text {
        padding: 1em;
        line-height: 2;
    }

    .ui.dimmer .ui.divider {
        font-weight: normal;
    }
</style>

<style lang="scss" scoped>
    .ui.vertical.menu .halflings {
        line-height: 0.6;
        margin-right: 0.5em;
    }

    .ui.button.dimmer-swap {
        padding-left: 1em;
        padding-right: 1em;

        .halflings {
            top: 2px;
        }
    }

    /* Inverted colors */
    .menu.inverted .item .ui.button.black {
        background: #1b1c1d80;

        &:hover,
        &:active,
        &:focus {
            background: #1b1c1db0;
        }
    }

    .kiosk-select-item.disabled:not(.active) .item-text,
    .kiosk-select-item.disabled:not(.active) .item-label .ui.label.black {
        color: #686869 !important;
    }

    .ui.text.inverted {
        color: #fff;
    }

    .ui.input input,
    .subtle.quantity {
        color: lightgray;
    }

    .opacity-50 {
        opacity: 0.5;
    }
</style>

<style lang="scss">
    .header-actions {
        position: absolute;
        top: 0px;
        right: 0px;
        height: 40px;
        display: block;

        a {
            /* color: rgba(0, 0, 0, .87); */
            color: #fff;
            display: inline-block;
            width: 40px;
            text-align: center;
            margin: 0;
            padding: 13px 5px 13px 5px;
            border-left: 1px solid rgba(255, 255, 255, .08);
            transition: background .1s ease, color .1s ease;

            &:hover {
                /* color: rgba(0, 0, 0, .87); */
                /* background: rgba(0, 0, 0, .08) !important; */
                background: #2d2e2f !important;
            }

            .glyphicons,
            .halflings {
                top: 2px;
                width: 30px;
                height: 14px;
                margin: -1px 0 0 0;
            }
        }

        .negative {
            color: red;
        }
    }
</style>
