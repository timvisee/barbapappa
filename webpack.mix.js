let mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')

    /**
     * Package jQuery and jQuery mobile components.
     * Copy the jQuery mobile resources.
     */
    .scripts([
        'resources/assets/libs/jquery/jquery-2.1.4.js',
        'resources/assets/js/jquery.mobile.settings.js',
        'resources/assets/libs/jquery-mobile/jquery.mobile-1.4.5.js'
    ], 'public/js/jquery-package.js')
    .styles([
        'resources/assets/libs/jquery-mobile/jquery.mobile-1.4.5.css'
    ], 'public/css/jquery-mobile.css')
    .copyDirectory(
        'resources/assets/libs/jquery-mobile/images',
        'public/css/images'
    );
