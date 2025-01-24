<template>
    <div class="ui vertical large menu fluid panel-products">

        <div v-if="!self && !swapped && selectedUsers.length == 0"
                v-on:click="hintUsers()"
                class="ui active inverted dimmer">
            <div class="ui text">
                {{ __('pages.kiosk.firstSelectUser') }}

                <div class="ui hidden divider"></div>

                <em class="opacity-50">{{ __('general.or') }}</em>

                <div class="ui hidden divider"></div>

                <a v-on:click.stop.prevent="swap()"
                        class="ui big button basic primary dimmer-swap opacity-50"
                        href="#">
                    <i class="halflings halflings-retweet"></i>
                    {{ __('pages.kiosk.productMode') }}
                </a>
            </div>
        </div>

        <h5 class="ui item header">
            <span v-if="self">{{ __('pages.bar.tapToBuyProducts') }}</span>
            <span v-else>{{ __('pages.kiosk.selectProducts') }}</span>

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

        <!-- Main product list (some top, recents, search results) -->
        <a v-for="product in products"
                v-on:click.stop.prevent="clickProduct(product)"
                href="#"
                class="green item kiosk-select-item"
                v-bind:class="{ disabled: buying || (product.exhausted && getQuantity(product) <= 0), active: getQuantity(product) > 0 }">
            <div class="item-text">
                <span v-if="getQuantity(product) > 0" class="subtle quantity">
                    <span v-if="isSelectMode()">+{{ getQuantity(product) }}</span>
                    <span v-else>{{ getQuantity(product) }}×</span>
                </span>

                <span v-if="product.exhausted && getQuantity(product) <= 0" class="halflings halflings-trash"></span>

                {{ product.name }}
            </div>

            <div class="item-label">
                <span v-if="getBuyQueueQuantity(product) > 0">
                    <span class="subtle">{{ getBuyQueueQuantity(product) }}</span>
                    &nbsp;
                    <div class="ui active inline mini loader"></div>
                </span>

                <i v-else-if="self && getQuantity(product) > 0" class="ui text positive glyphicons glyphicons-ok"></i>

                <div v-else-if="self || getQuantity(product) <= 0" class="ui label"
                        v-bind:class="{ blue: !product.exhausted, disabled: product.exhausted}">
                    {{ product.price_display }}
                </div>
            </div>

            <div v-if="!self && getQuantity(product)" class="item-buttons">
                <div class="ui two buttons">
                    <a href="#"
                            v-on:click.stop.prevent="quantityModal(product)"
                            v-bind:class="{ disabled: buying }"
                            class="ui large button">
                        <i class="glyphicons glyphicons-hash"></i>
                    </a>

                    <a href="#"
                            v-on:click.stop.prevent="setQuantity(product, 0)"
                            v-bind:class="{ red: !isSelectMode(), disabled: buying }"
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
        <i v-if="!searching && productCount == 0 && query == ''" class="item">
            {{ __('pages.products.noProducts') }}
        </i>
        <i v-if="!searching && productCount == 0 && query != ''" class="item">
            {{ __('pages.kiosk.noProductsFoundFor', {term: query}) }}
        </i>

        <!-- Selected products not in list (always show on bottom) -->
        <a v-for="product in productsBacklog"
                v-on:click.stop.prevent="clickProduct(product)"
                href="#"
                class="green item kiosk-select-item"
                v-bind:class="{ disabled: buying, active: getQuantity(product) > 0 }">
            <div class="item-text">
                <span v-if="getQuantity(product) > 0" class="subtle quantity">
                    <span v-if="isSelectMode()">+{{ getQuantity(product) }}</span>
                    <span v-else>{{ getQuantity(product) }}×</span>
                </span>

                {{ product.name }}
            </div>

            <div v-if="!self" class="item-buttons">
                <div class="ui two buttons">
                    <a href="#"
                            v-on:click.stop.prevent="quantityModal(product)"
                            v-bind:class="{ disabled: buying }"
                            class="ui large button">
                        <i class="glyphicons glyphicons-hash"></i>
                    </a>

                    <a href="#"
                            v-on:click.stop.prevent="setQuantity(product, 0)"
                            v-bind:class="{ red: !isSelectMode(), disabled: buying }"
                            class="ui large button">
                        <i class="glyphicons glyphicons-remove"></i>
                    </a>
                </div>
            </div>
        </a>

        <a v-if="self" :href="barUrl + '/products'"
                class="ui large bottom attached basic button">
            {{ __('pages.products.all') }}...
        </a>

        <!-- Product quantity selection modal -->
        <QuantityModal :initialQuantity="quantityModalQuantity" @onSubmit="onQuantityModalSubmit" />
    </div>
</template>

<script>
    import axios from 'axios';

    const QuantityModal = require('./QuantityModal.vue').default;

    export default {
        components: {
            QuantityModal,
        },
        props: [
            'apiUrl',
            'barUrl',
            'self',
            'swapped',
            'selectedUsers',
            'selectedProducts',
            'cart',
            'buying',
            'buyCounts',
            '_buyProduct',
            '_getUserCart',
            '_getSelectCart',
            '_getCartQuantity',
            '_setCartQuantity',
            '_addCartQuantity',
            '_getCartSize',
            '_removeCart',
            '_getBuyQueueQuantity',
        ],
        data() {
            return {
                query: '',
                searching: true,
                products: [],
                quantityModalQuantity: null,
                quantityModalCallback: null,
            };
        },
        computed: {
            productCount: function() {
                return this.products != null
                        ? this.products.length
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
        },
        methods: {
            // If we're currently in user selection mode.
            isSelectMode() {
                return !this.self && this.swapped;
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
                if(this.self) {
                    let id = product.id;
                    return (id in this.buyCounts) ? this.buyCounts[id] : 0;
                } else {
                    return this._getCartQuantity(this.getCart(), product);
                }
            },

            // Set product quantity in user cart.
            setQuantity(product, quantity) {
                return this._setCartQuantity(this.getCart(true), product, quantity);
            },

            // Get the selection quantity for a given product
            getBuyQueueQuantity(product) {
                return this._getBuyQueueQuantity === undefined
                    ? 0
                    : this._getBuyQueueQuantity(product);
            },

            // Called when clicking on a product
            clickProduct(product) {
                if(this.self)
                    this.userBuyProduct(product);
                else
                    this.changeQuantity(product, 1);
            },

            // Immediately buy product by current user
            userBuyProduct(product) {
                this._buyProduct(product);
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
            search(query = '') {
                // Fetch a list of products, set the searching state
                this.searching = true;
                this._searchOnline(query)
                    // Handle result, only update if still searching same query
                    .then(products => {
                        if(query == this.query)
                            this.products = products;
                    })
                    // Handle error
                    .catch(err => {
                        alert('An error occurred while listing products');
                        console.error(err);
                    })
                    // Stop searching state, if still searching same query
                    .finally(() => {
                        if(query == this.query)
                            this.searching = false;
                    });
            },

            // Search products with the given query online.
            _searchOnline(query = '') {
                return this._searchRequest(query, false);
            },

            // Do a search request.
            _searchRequest(query = '', all = false) {
                // Build URL
                let url = new URL(this.apiUrl + '/products');
                if(query != null && query.length > 0)
                    url.searchParams.append('q', query);
                if(all)
                    url.searchParams.append('all', 'true');

                // Fetch a list of products
                return axios.get(url.toString()).then(res => res.data);
            },

            // Check if given product is in current search result list
            isProductInResult(product) {
                return this.products.filter(p => p.id == product.id).length > 0;
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
        line-height: 2.4;
        padding: 0 1.125em;
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
        /* background: rgba(0, 182, 240, 0.45) !important; */
        background: rgba(0, 182, 240, 0.2) !important;
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

    .item-label .ui.label {
        padding-top: 0.3em;
        padding-bottom: 0.3em;
    }

    /* Inverted colors
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
    */
</style>

<style lang="scss">
    .header-actions {
        position: absolute;
        top: 0px;
        right: 0px;
        height: 40px;
        display: block;

        a {
            color: rgba(0, 0, 0, .87);
            display: inline-block;
            width: 40px;
            text-align: center;
            margin: 0;
            padding: 13px 5px 13px 5px;
            border-left: 1px solid rgba(255, 255, 255, .08);
            transition: background .1s ease, color .1s ease;

            &:hover {
                /* color: rgba(0, 0, 0, .87); */
                background: rgba(0, 0, 0, .08) !important;
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
