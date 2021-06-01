import axios from 'axios';

window.Vue = require('vue');

const Buy = require('./Buy').default;

/**
 * Replace the static quick buy element with a smart interactive Vue version.
 */
function showSmartQuickBuy() {
    // Get the API URL and query
    let apiUrl = window.barapp_quickbuy_api_url;
    let query = event.target.value;
    if(!apiUrl)
        console.error('Failed to get API URL');

    // Configure some language mixins
    Vue.mixin({
        methods: {
            __: (key, values) => Lang.get(key, values),
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
                apiUrl,
                query,
            };
        },
        template: '<Buy :apiUrl="apiUrl" :query="query" />',
    });
};

window.addEventListener('load', function() {
    let quickBuySearchEl = document.getElementById('quickbuy-search');
    quickBuySearchEl.onclick = showSmartQuickBuy;
    quickBuySearchEl.onfocus = showSmartQuickBuy;
    quickBuySearchEl.onkeyup = showSmartQuickBuy;
});
