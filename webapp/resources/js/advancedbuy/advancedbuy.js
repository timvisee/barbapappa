import Vue from 'vue';

import Buy from './Buy.vue';

window.addEventListener('load', function() {
    // Get the API URL
    let apiUrl = window.barapp_advancedbuy_api_url;
    if(!apiUrl)
        console.error('Failed to get API URL');

    // Configure some language mixins
    Vue.mixin({
        methods: {
            __: (key, values) => Lang.get(key, values),
            langChoice: (key, count, values) => Lang.choice(key, count, values),
        }
    });

    // Build the app
    const app = new Vue({
        el: '#advancedbuy',
        components: {
            Buy,
        },
        data() {
            return {
                apiUrl,
            };
        },
        template: '<Buy :apiUrl="apiUrl" />',
    });
});
