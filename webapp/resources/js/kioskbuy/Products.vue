<template>
    <div class="ui vertical huge menu fluid panel-products">

        <div v-if="selectedUsers.length == 0"
                v-on:click="hintUsers()"
                class="ui inverted active dimmer">
            <div class="ui text">{{ __('pages.kiosk.firstSelectUser') }}</div>
        </div>

        <h5 class="ui item header">
            {{ __('pages.kiosk.selectProducts') }}

            <a v-if="getCartSize() > 0 && !buying"
                    v-on:click.stop.prevent="removeCart(); query = ''"
                    href="#"
                    class="reset">
                {{ __('misc.reset') }}
            </a>
        </h5>

        <div class="item">
            <div class="ui transparent icon input">
                <input v-model="query"
                        @input="e => query = e.target.value"
                        @focus="e => e.target.select()"
                        type="text"
                        :placeholder="__('pages.products.search') + '...'" />
                <div v-if="searching" class="ui active inline tiny loader"></div>
                <i v-if="!searching && !query" v-on:click.prevent.stop="search(query)" class="icon link">
                    <span class="glyphicons glyphicons-search"></span>
                </i>
                <i v-if="!searching && query" v-on:click.prevent.stop="query = ''" class="icon link">
                    <span class="glyphicons glyphicons-remove"></span>
                </i>
            </div>
        </div>

        <a v-for="product in products"
                v-on:click.stop.prevent="select(product)"
                href="#"
                class="green inverted item kiosk-select-item"
                v-bind:class="{ disabled: buying, active: getQuantity(product) > 0 }">
            <div class="item-text">
                <span v-if="getQuantity(product) > 0" class="subtle quantity">{{ getQuantity(product) }}×</span>

                {{ product.name }}
            </div>

            <div v-if="getQuantity(product) == 0" class="item-label">
                <div class="ui blue label">{{ product.price_display }}</div>
            </div>

            <div v-if="getQuantity(product)" class="item-buttons">
                <div class="ui two buttons">
                    <a href="#"
                            v-on:click.stop.prevent="select(product, 5 - getQuantity(product) % 5)"
                            v-bind:class="{ disabled: buying }"
                            class="ui large button">+{{ 5 - getQuantity(product) % 5 }}</a>

                    <a href="#"
                            v-on:click.stop.prevent="deselect(product)"
                            v-bind:class="{ disabled: buying }"
                            class="ui red large button">×</a>
                </div>
            </div>
        </a>

        <i v-if="searching && products.length == 0 && query != ''" class="item">
            {{ __('pages.products.searchingFor', {term: query}) }}...
        </i>
        <i v-if="searching && products.length == 0 && query == 0" class="item">
            {{ __('misc.loading') }}...
        </i>
        <i v-if="!searching && products.length == 0" class="item">
            {{ __('pages.products.noProductsFoundFor', {term: query}) }}.
        </i>

        <!-- Always show selected products if not part of query results -->
        <a v-for="product in (getUserCart() && getUserCart().products || [])"
                v-if="!isProductInResult(product.product)"
                v-on:click.stop.prevent="select(product)"
                href="#"
                class="green inverted item"
                v-bind:class="{ disabled: buying, active: getQuantity(product) > 0 }">
            <div class="item-text">
                <span v-if="getQuantity(product) > 0" class="subtle quantity">{{ getQuantity(product.product) }}×</span>

                {{ product.product.name }}
            </div>

            <div class="item-buttons">
                <div class="ui two buttons">
                    <a href="#"
                            v-on:click.stop.prevent="select(product, 5 - getQuantity(product) % 5)"
                            v-bind:class="{ disabled: buying }"
                            class="ui large button">+{{ 5 - getQuantity(product.product) % 5 }}</a>

                    <a href="#"
                            v-on:click.stop.prevent="deselect(product)"
                            v-bind:class="{ disabled: buying }"
                            class="ui red large button">×</a>
                </div>
            </div>
        </a>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                query: '',
                searching: true,
                products: [],
            };
        },
        watch: {
            query: function() {
                this.search(this.query);
            },
        },
        mounted: function() {
            this.search();
        },
        props: [
            'apiUrl',
            'selectedUsers',
            'cart',
            'buying',
        ],
        methods: {
            // Get cart instance for user
            getUserCart(user, create = false) {
                // Get current user if not given
                if(user == null || user == undefined) {
                    // Get user and cart, user must be selected
                    user = this.selectedUsers[0];
                    if(user == null)
                        return;
                }

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

            // Select the given product, add 1 to desired quantity
            select(product, quantity = 1) {
                // Get user and cart, user must be selected
                let user = this.selectedUsers[0];
                if(user == null)
                    return;
                let userCart = this.getUserCart(user, true);

                // Add products
                let item = userCart.products.filter(p => p.id == product.id);
                if(item.length > 0)
                    item[0].quantity += quantity;
                else
                    userCart.products.push({
                        id: product.id,
                        quantity: quantity,
                        product,
                    });
            },

            // Fully deselect the given product
            deselect(product) {
                // Get user and cart, user must be selected
                let user = this.selectedUsers[0];
                if(user == null)
                    return;
                let userCart = this.getUserCart(user);
                if(userCart == null)
                    return;

                // Remove product from cart
                userCart.products.splice(
                    userCart.products.findIndex(p => p.id == product.id),
                    1,
                );

                // If user does not have products anymore, remove cart
                if(this.getCartSize() <= 0)
                    this.removeCart();
            },

            // Get the number of products in the current user cart
            getCartSize() {
                // Get user and cart, user must be selected
                let user = this.selectedUsers[0];
                if(user == null)
                    return 0;
                let userCart = this.getUserCart(user);
                if(userCart == null)
                    return 0;

                return userCart.products.reduce((sum, product) => product.quantity + sum, 0);
            },

            // Remove cart for the current user
            removeCart() {
                // Get user and cart, user must be selected
                let user = this.selectedUsers[0];
                if(user == null)
                    return 0;

                let i = this.cart.findIndex(c => c.user.id == user.id);
                if(i >= 0)
                    this.cart.splice(i, 1);
            },

            // Get the selection quantity for a given product
            getQuantity(product) {
                // Get user and cart, user must be selected
                let user = this.selectedUsers[0];
                if(user == null)
                    return 0;
                let userCart = this.getUserCart(user);
                if(userCart == null)
                    return 0;

                let item = userCart.products.filter(p => p.id == product.id);
                return item.length > 0 ? item[0].quantity : 0;
            },

            // Search products with the given query
            search(query = '') {
                // Fetch a list of products, set the searching state
                this.searching = true;
                axios.get(this.apiUrl + `/products?q=${encodeURIComponent(query)}`)
                    .then(res => this.products = res.data)
                    .catch(err => {
                        alert('An error occurred while listing products');
                        console.error(err);
                    })
                    .finally(() => this.searching = false);
            },

            // Check if given product is in current search result list
            isProductInResult(product) {
                return this.products.filter(p => p.id == product.id).length > 0;
            },

            // Hint to select a user first
            hintUsers() {
                if(this.selectedUsers > 0)
                    return;
                this.$emit('highlightUsers');
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

    .reset {
        color: red;
        float: right;
        line-height: 1 !important;
    }

    .dimmer .text {
        padding: 1em;
        line-height: 2;
    }

    .quantity,
    .item.active {
        font-weight: bold !important;
    }
</style>
