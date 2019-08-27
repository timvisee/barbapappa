<template>
    <Products :query="query" />
</template>

<script>
    import axios from 'axios';

    const Products = require('./Products.vue').default;

    export default {
        components: {
            Products,
        },
        props: [
            'query',
        ],
    }
</script>
