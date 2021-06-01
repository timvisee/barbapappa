<template>
    <div class="ui vertical menu fluid">

        <div class="item">
            <div class="ui transparent icon input">
                <input v-model="query"
                        @input="e => query = e.target.value"
                        @focus="e => e.target.select()"
                        id="quickbuy-search"
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

        <form v-for="product in products"
                method="POST"
                :action="url + '/quick-buy'"
                accept-charset="UTF-8">
            <input name="_token" type="hidden" :value="csrfToken">
            <input name="product_id" type="hidden" :value="product.id">
            <a href="#" onclick="event.preventDefault(); this.parentNode.submit()" class="item">
                {{ product.name }}
                <div class="ui blue label">{{ product.price_display }}</div>
            </a>
        </form>

        <i v-if="searching && products.length == 0 && query != ''" class="item">
            {{ __('pages.products.searchingFor', {term: query}) }}...
        </i>
        <i v-if="searching && products.length == 0 && query == ''" class="item">
            {{ __('misc.loading') }}...
        </i>
        <i v-if="!searching && products.length == 0" class="item">
            {{ __('pages.products.noProductsFoundFor', {term: query}) }}.
        </i>

        <a :href="url + '/products'" class="ui bottom attached button">
            {{ __('misc.catalog') }}
        </a>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                searching: true,
                products: [],
                url: undefined,
                csrfToken: undefined,
            };
        },
        props: [
            'query',
        ],
        watch: {
            query: function() {
                this.search(this.query);
            },
        },
        methods: {
            // Search products with the given query
            search(query = '') {
                // Fetch a list of products, set the searching state
                // TODO: set fixed URL here
                this.searching = true;
                axios.get(window.location.href + `/buy/api/products?q=${encodeURIComponent(query)}`)
                    .then(res => this.products = res.data)
                    .catch(err => {
                        alert('An error occurred while listing products');
                        console.error(err);
                    })
                    .finally(() => this.searching = false);
            },
        },
        mounted: function() {
            // Obtain the CSRF token for POST requests
            this.url = window.location.href;
            this.csrfToken =
                document.head.querySelector("meta[name=csrf-token]").content;

            // Search on load and focus the search field
            this.search(this.query);
            $('#quickbuy-search').focus();
        },
    }
</script>
