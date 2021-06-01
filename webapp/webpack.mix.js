// noinspection JSAnnotator
let mix = require('laravel-mix');
const WebpackShellPlugin = require('webpack-shell-plugin');
const { GenerateSW } = require('workbox-webpack-plugin');

// Add shell command plugin configured to create JavaScript language file
mix.webpackConfig({
    plugins: [
        new WebpackShellPlugin({
            onBuildStart:['php artisan lang:js --compress --quiet -- public/js/app/lang.js'],
            onBuildEnd:[],
        }),
        new GenerateSW({
            // TODO: do not exclude common files
            exclude: [
                /.*\.(js|css)/
            ],
            swDest: 'sw.js',
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
    'public/LICENSE',
);

// App
mix.js(
    ['public/js/app/lang.js', 'resources/js/app.js'],
    'public/js/app.js',
).js(
    'resources/js/quickbuy/quickbuy.js',
    'public/js',
).js(
    'resources/js/advancedbuy/advancedbuy.js',
    'public/js',
).js(
    'resources/js/kioskbuy/kioskbuy.js',
    'public/js',
).sass(
    'resources/sass/app.scss',
    'public/css',
);

// jQuery
vendorScripts.push('resources/assets/vendor/jquery/jquery-2.1.4.js');

// Flag icons
mix.sass(
    'node_modules/flag-icon-css/sass/flag-icon.scss',
    'public/css/vendor/flag-icon.css',
);
vendorStyles.push('public/css/vendor/flag-icon.css');
mix.copyDirectory(
    'node_modules/flag-icon-css/flags',
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

// Enable assert versioning for cache busting
if(mix.inProduction()) {
    mix.version();
}
