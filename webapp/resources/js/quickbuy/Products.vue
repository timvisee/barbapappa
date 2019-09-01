<template>
    <div class="ui vertical menu fluid">

        <div class="item">
            <div class="ui transparent icon input">
                <input v-model="query" type="text" :placeholder="__('pages.products.search') + '...'" ref="query" />
                <div v-if="searching" class="ui active inline tiny loader"></div>
                <i v-if="!searching" v-on:click.prevent.stop="search(query)" class="icon glyphicons glyphicons-search link"></i>
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

        <i v-if="!searching && products.length == 0" class="item">
            {{ __('pages.products.noProductsFoundFor', {term: query}) }}
        </i>

        <a :href="url + '/buy'" class="ui bottom attached button">
            {{ __('pages.bar.advancedBuy.title') }}
        </a>
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
                axios.get(window.location.href + `/buy/products?q=${encodeURIComponent(query)}`)
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
            this.$refs.query.focus();
        },
    }
</script>