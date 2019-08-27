import axios from 'axios';

window.Vue = require('vue');

const Buy = require('./Buy').default;

window.addEventListener('load', function() {
    document.getElementById('quickbuy-search').onkeyup = function() {
        // Get current query
        let query = event.target.value;

        // Configure some language mixins
        Vue.mixin({
            methods: {
                __: (key, values) => Lang.get(key, values),
                langGet: (key, values) => Lang.get(key, values),
                langChoice: (key, count, values) => Lang.choice(key, count, values),
            }
        });

        // Build the app
        const app = new Vue({
            el: '#quickbuy',
            components: {
                Buy,
            },
            data() {
                return {
                    query,
                };
            },
            template: '<Buy :query="query" />',
        });
    };
});
