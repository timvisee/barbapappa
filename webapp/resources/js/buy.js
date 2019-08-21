window.Vue = require('vue');

// TODO: compile to single file?

const Buy = require('./components/Buy.vue').default;

window.addEventListener('load', function() {
    // Build the app
    const app = new Vue({
        el: '#app',
        components: {
            Buy,
        },
        template: '<Buy></Buy>',
    });
});
