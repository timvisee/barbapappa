<template>
    <div class="ui vertical large menu fluid panel-products">

        <div v-if="selectedUsers.length == 0" class="ui inverted active dimmer">
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
                class="green inverted item"
                v-bind:class="{ disabled: buying, active: getQuantity(product) > 0 }">
            <span v-if="getQuantity(product) > 0" class="subtle">{{ getQuantity(product) }}×</span>

            {{ product.name }}

            <a href="#"
                    v-if="getQuantity(product)"
                    v-on:click.stop.prevent="deselect(product)"
                    v-bind:class="{ disabled: buying }"
                    class="ui red compact button action-button">×</a>

            <div v-if="getQuantity(product)"
                    v-on:click.stop.prevent="select(product, 5 - getQuantity(product) % 5)"
                    v-bind:class="{ disabled: buying }"
                    class="ui compact button action-button">+{{ 5 - getQuantity(product) % 5 }}</div>

            <div v-if="getQuantity(product) == 0"
                    class="ui blue label">{{ product.price_display }}</div>
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
            <span v-if="getQuantity(product) > 0" class="subtle">{{ getQuantity(product.product) }}×</span>

            {{ product.product.name }}

            <div v-if="getQuantity(product)"
                    v-on:click.stop.prevent="deselect(product)"
                    v-bind:class="{ disabled: buying }"
                    class="ui red compact button action-button">×</div>

            <div v-if="getQuantity(product)"
                    v-on:click.stop.prevent="select(product, 5 - getQuantity(product) % 5)"
                    v-bind:class="{ disabled: buying }"
                    class="ui compact button action-button">+{{ 5 - getQuantity(product.product) % 5 }}</div>
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
        },
    }
</script>

<style>
    .reset {
        color: red;
        float: right;
        line-height: 1 !important;
    }

    .dimmer .text {
        padding: 1em;
        line-height: 2;
    }

    .item {
        white-space: nowrap;
    }

    .item .action-button {
        float: right;
        text-align: center;
        border-radius: 0;
        margin: -1em -1.2em 0 1.2em;
        padding: 1em 1em !important;

        /* TODO: do not use fixed height here */
        width: 43px;
        height: 43px;
    }
</style>
