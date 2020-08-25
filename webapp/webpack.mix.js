// noinspection JSAnnotator
let mix = require('laravel-mix');
const WebpackShellPlugin = require('webpack-shell-plugin');
const {GenerateSW} = require('workbox-webpack-plugin');

// Add shell command plugin configured to create JavaScript language file
mix.webpackConfig({
    plugins: [
        new WebpackShellPlugin({
            onBuildStart:['php artisan lang:js --compress --quiet'],
            onBuildEnd:[],
        }),
        new GenerateSW(),
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

// Static assets.
mix.copyDirectory(
    'resources/assets/img',
    'public/img',
);

// Compile application assets
mix.js(
    'resources/js/app.js',
    'public/js',
).js(
    'resources/js/quickbuy/quickbuy.js',
    'public/js',
).js(
    'resources/js/advancedbuy/advancedbuy.js',
    'public/js',
).sass(
    'resources/sass/app.scss',
    'public/css',
);

// Package jQuery and related resources.
mix.scripts([
    'resources/assets/vendor/jquery/jquery-2.1.4.js'
], 'public/js/jquery-packed.js');

// Glyphicons resources
mix.copyDirectory(
    'resources/assets/vendor/glyphicons/fonts',
    'public/fonts',
).copyDirectory(
    'resources/assets/vendor/glyphicons-halflings/fonts',
    'public/fonts',
).styles([
    'resources/assets/vendor/glyphicons/css/glyphicons.css',
    'resources/assets/vendor/glyphicons-halflings/css/glyphicons-halflings.css',
], 'public/css/glyphicons-packed.css');

// Flag icon resources
mix.sass(
    'node_modules/flag-icon-css/sass/flag-icon.scss',
    'public/css/flag-icon.css',
).copyDirectory(
    'node_modules/flag-icon-css/flags',
    'public/flags',
);

// Semantic UI resources
mix.copy(
    'node_modules/semantic-ui-css/semantic.min.css',
    'public/css/semantic.min.css',
).copy(
    'node_modules/semantic-ui-css/semantic.min.js',
    'public/js/semantic.min.js',
);
