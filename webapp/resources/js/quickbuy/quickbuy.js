import axios from 'axios';

window.Vue = require('vue');

const Buy = require('./Buy').default;

/**
 * Replace the static quick buy element with a smart interactive Vue version.
 */
function showSmartQuickBuy() {
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

window.addEventListener('load', function() {
    let quickBuySearchEl = document.getElementById('quickbuy-search');
    quickBuySearchEl.onclick = showSmartQuickBuy;
    quickBuySearchEl.onfocus = showSmartQuickBuy;
    quickBuySearchEl.onkeyup = showSmartQuickBuy;
});
