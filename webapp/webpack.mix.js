const path = require('path');

let mix = require('laravel-mix');

const WebpackShellPluginNext = require('webpack-shell-plugin-next');
const {GenerateSW} = require('workbox-webpack-plugin');

const DAY_SECONDS = 86400;
const MONTH_SECONDS = 2592000;

// Add shell command plugin configured to create JavaScript language file
mix.webpackConfig({
    plugins: [
        new WebpackShellPluginNext({
            onBuildStart: {
                scripts: ['php artisan lang:js --compress --quiet -- public/js/app/lang.js'],
                blocking: true,
            },
        }),
        new GenerateSW({
            exclude: [
                /js/,
                /images\/vendor\/flag-icons/,
            ],
            swDest: 'sw.js',
        }),
        new GenerateSW({
            // Claim clients as soon as possible
            clientsClaim: true,
            exclude: [
                /js/,
                /images\/vendor\/flag-icons/,
            ],
            navigationPreload: true,
            runtimeCaching: [
                // Cache request fetching all users/products
                {
                    urlPattern: ({url}) => {
                        return url.pathname.startsWith('/kiosk/api')
                            && url.searchParams.has('all');
                    },
                    handler: 'StaleWhileRevalidate',
                    options: {
                        cacheName: 'kiosk-api-required',
                        expiration: {
                            maxAgeSeconds: MONTH_SECONDS,
                            // Likely will never be more than 2
                            maxEntries: 5,
                        },
                    },
                },
                // Cache regular user and product searches
                // Always respond with cache but bump cache in the background
                {
                    urlPattern: ({url}) => url.pathname.startsWith('/kiosk/api'),
                    handler: 'StaleWhileRevalidate',
                    options: {
                        cacheName: 'kiosk-api',
                        expiration: {
                            // Expire in a week, not super important
                            maxAgeSeconds: 7 * DAY_SECONDS,
                            // May be a lot
                            maxEntries: 250,
                        },
                    },
                },
                {
                    urlPattern: ({url}) => url.pathname == '' || url.pathname == '/' || url.pathname.startsWith('/kiosk'),
                    handler: 'NetworkFirst',
                    options: {
                        cacheName: 'kiosk-app',
                        expiration: {
                            maxAgeSeconds: MONTH_SECONDS,
                            // Likely will never be more than 3
                            maxEntries: 10,
                        },
                    },
                },
                {
                    urlPattern: ({url}) => {
                        return url.pathname.startsWith('/js/')
                            || url.pathname.startsWith('/css/')
                            || url.pathname.startsWith('/fonts/')
                            || url.pathname.startsWith('/img/logo/');
                    },
                    handler: 'CacheFirst',
                    options: {
                        cacheName: 'assets',
                        expiration: {
                            maxAgeSeconds: MONTH_SECONDS,
                            // May be a lot
                            maxEntries: 100,
                        },
                    },
                },
            ],
            swDest: 'sw-kiosk.js',
        }),
    ]
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// Build list of vendor scripts and styles to bundle
let vendorScripts = [];
let vendorStyles = [];

// Static assets
mix.copyDirectory(
    'resources/assets/img',
    'public/img',
).copy(
    '../LICENSE',
    'public/',
);

// App
vendorScripts.push('public/js/app/lang.js');
mix.js(
    'resources/js/app.js',
    'public/js/app.js',
).sass(
    'resources/sass/app.scss',
    'public/css',
);

// App widgets
mix.js(
    'resources/js/quickbuy/quickbuy.js',
    'public/js/widget',
).js(
    'resources/js/advancedbuy/advancedbuy.js',
    'public/js/widget',
).js(
    'resources/js/kioskbuy/kioskbuy.js',
    'public/js/widget',
).vue({ version: 2 });

// jQuery
vendorScripts.push('resources/assets/vendor/jquery/jquery-2.1.4.js');

// Flag icons
mix.sass(
    'node_modules/flag-icons/sass/flag-icons.scss',
    'public/css/vendor/flag-icons.css',
);
vendorStyles.push('public/css/vendor/flag-icons.css');
mix.copyDirectory(
    'node_modules/flag-icons/flags',
    'public/flags',
);

// Glyphicons
vendorStyles.push('resources/assets/vendor/glyphicons/css/glyphicons.css');
vendorStyles.push('resources/assets/vendor/glyphicons-halflings/css/glyphicons-halflings.css');
mix.copyDirectory([
        'resources/assets/vendor/glyphicons/fonts',
        'resources/assets/vendor/glyphicons-halflings/fonts',
    ],
    'public/fonts',
);

// Semantic UI
vendorScripts.push('node_modules/semantic-ui-css/semantic.min.js');
vendorStyles.push('node_modules/semantic-ui-css/semantic.min.css');
mix.copy(
    'node_modules/semantic-ui-css/themes/default',
    'public/css/themes/default',
);

// Bundle vendor scripts and styles
mix.scripts(vendorScripts, 'public/js/vendor.js');
mix.styles(vendorStyles, 'public/css/vendor.css');

// Chart.js bundle
mix.scripts([
    'node_modules/chart.js/dist/chart.js',
    'node_modules/moment/moment.js',
    'node_modules/chartjs-adapter-moment/dist/chartjs-adapter-moment.js',
], 'public/js/vendor/chart.js');

// Enable assert versioning for cache busting
if(mix.inProduction()) {
    mix.version();
}
