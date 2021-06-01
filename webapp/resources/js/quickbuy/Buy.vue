<template>
    <Products :apiUrl="apiUrl" :query="query" />
</template>

<script>
    import axios from 'axios';

    const Products = require('./Products.vue').default;

    export default {
        components: {
            Products,
        },
        data() {
            return {
                apiUrl: window.barapp_quickbuy_api_url,
            };
        },
        props: [
            'query',
        ],
    }
</script>
