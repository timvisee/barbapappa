import Vue from 'vue';

import Main from './Main.vue';

window.addEventListener('load', function() {
    // Get the API URL
    let apiUrl = window.barapp_barbuy_api_url;
    let barUrl = window.barapp_bar_url;
    if(!apiUrl)
        console.error('Failed to get API URL');
    if(!barUrl)
        console.error('Failed to get bar URL');

    // Configure some language mixins
    Vue.mixin({
        methods: {
            __: (key, values) => Lang.get(key, values),
            langChoice: (key, count, values) => Lang.choice(key, count, values),
        }
    });

    // Build the app
    const app = new Vue({
        el: '#barbuy',
        components: {
            Main,
        },
        data() {
            return {
                apiUrl,
                barUrl,
            };
        },
        template: '<Main :apiUrl="apiUrl" :barUrl="barUrl" />',
    });
});
