<template>
    <div class="ui large vertical menu fluid">

        <h5 class="ui item header">{{ __('pages.products.select') }}</h5>

        <div class="item">
            <div class="ui transparent icon input">
                <input v-model="query"
                        @input="e => query = e.target.value"
                        @focus="e => e.target.select()"
                        type="search"
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
                v-bind:class="{ active: getQuantity(product) > 0, disabled: product.exhausted && getQuantity(product) <= 0 }">
            <span v-if="getQuantity(product) > 0" class="subtle">+{{ getQuantity(product) }}</span>

            <span v-if="product.exhausted && getQuantity(product) <= 0" class="halflings halflings-trash"></span>

            {{ product.name }}

            <a href="#"
                    v-if="getQuantity(product)"
                    v-on:click.stop.prevent="deselect(product)"
                    class="ui red compact button action-button">×</a>

            <div v-if="getQuantity(product) == 0"
                v-bind:class="{ blue: !product.exhausted }"
                class="ui label">{{ product.price_display }}</div>
        </a>

        <i v-if="searching && products.length == 0 && query != ''" class="item">
            {{ __('pages.products.searchingFor', {term: query}) }}...
        </i>
        <i v-if="searching && products.length == 0 && query == ''" class="item">
            {{ __('misc.loading') }}...
        </i>
        <i v-if="!searching && products.length == 0" class="item">
            {{ __('pages.products.noProductsFoundFor', {term: query}) }}.
        </i>

        <a v-for="product in selected"
                v-if="!isProductInResult(product.product)"
                v-on:click.stop.prevent="select(product.product)"
                href="#"
                class="green inverted item"
                v-bind:class="{ active: getQuantity(product.product) > 0, disabled: product.exhausted && getQuantity(product.product) <= 0}">

            <span v-if="getQuantity(product.product) > 0" class="subtle">+{{ getQuantity(product.product) }}</span>

            {{ product.product.name }}

            <a href="#"
                    v-if="getQuantity(product.product)"
                    v-on:click.stop.prevent="deselect(product.product)"
                    class="ui red compact button action-button">×</a>

            <div class="ui label"
                v-bind:class="{ blue: !product.exhausted }">{{ product.product.price_display }}</div>
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
        props: [
            'apiUrl',
            'selected',
        ],
        watch: {
            query: function() {
                this.search(this.query);
            },
        },
        methods: {
            // Select the given product, add 1 to desired quantity
            select(product) {
                let item = this.selected.filter(p => p.id == product.id);
                if(item.length > 0)
                    item[0].quantity += 1;
                else
                    this.selected.push({
                        id: product.id,
                        quantity: 1,
                        product,
                    });
            },

            // Fully deselect the given product
            deselect(product) {
                this.selected.splice(
                    this.selected.findIndex(p => p.id == product.id),
                    1,
                );
            },

            // Get the selection quantity for a given product
            getQuantity(product) {
                let item = this.selected.filter(p => p.id == product.id);
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
        mounted: function() {
            this.search();
        },
    }
</script>

<style scoped>
    .item .action-button {
        float: right;
        border-radius: 0;
        margin: -.95em -1.3em 0 1.2em;
        padding: 1em 1em !important;

        /* TODO: do not use fixed height here */
        width: 42.8667px;
        height: 42.8667px;
    }

    .ui.vertical.menu .halflings {
        line-height: 0.6;
        top: 3px;
        margin-right: 0.5em;
    }
</style>
