<template>
    <div class="ui vertical menu fluid">

        <!-- TODO: translate this somehow -->
        <h5 class="ui item header">Select products</h5>

        <div class="item">
            <div class="ui transparent icon input">
                <!-- TODO: translate this somehow -->
                <input v-model="query" type="text" name="q" placeholder="Search products..." />
                <div v-if="searching" class="ui active inline tiny loader"></div>
                <i v-if="!searching" v-on:click.prevent.stop="search(query)" class="icon glyphicons glyphicons-search link"></i>
            </div>
        </div>

        <a v-for="product in products"
                v-on:click.stop.prevent="select(product)"
                href="#"
                class="green inverted item"
                v-bind:class="{ active: getQuantity(product) > 0 }">
            <span v-if="getQuantity(product) > 0" class="subtle">{{ getQuantity(product) }}×</span>

            {{ product.name }}

            <div v-if="getQuantity(product)"
                    v-on:click.stop.prevent="deselect(product)"
                    class="ui red label">×</div>

            <div class="ui blue label">{{ product.price_display }}</div>
        </a>

        <a v-for="product in selected"
                v-if="!isProductInResult(product.product)"
                v-on:click.stop.prevent="select(product.product)"
                href="#"
                class="green inverted item"
                v-bind:class="{ active: getQuantity(product.product) > 0 }">
            <span v-if="getQuantity(product.product) > 0" class="subtle">{{ getQuantity(product.product) }}×</span>

            {{ product.product.name }}

            <div v-if="getQuantity(product.product)"
                    v-on:click.stop.prevent="deselect(product.product)"
                    class="ui red label">×</div>

            <div class="ui blue label">{{ product.product.price_display }}</div>
        </a>
    </div>
</template>

<script>
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
                this.searching = true;
                // TODO: set fixed URL here
                // TODO: handle search failures!
                fetch(`./buy/products?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(res => {
                        this.products = res;
                        this.searching = false;
                    });
            },

            // Check if given product is in current search result list
            isProductInResult(product) {
                return this.products.filter(p => p.id == product.id).length > 0;
            },
        },
        mounted: function() {
            this.search();
        },
        props: [
            'selected',
        ]
    }
</script>
