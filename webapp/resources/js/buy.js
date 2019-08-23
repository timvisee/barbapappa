window.Vue = require('vue');

// TODO: compile to single file?

const Buy = require('./components/Buy.vue').default;

window.addEventListener('load', function() {
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
        el: '#app',
        components: {
            Buy,
        },
        template: '<Buy></Buy>',
    });
});
