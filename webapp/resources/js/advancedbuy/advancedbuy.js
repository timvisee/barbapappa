window.Vue = require('vue');

const Buy = require('./Buy').default;

window.addEventListener('load', function() {
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
        template: '<Buy />',
    });
});
