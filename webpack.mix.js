const mix = require('laravel-mix');

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

mix.js('resources/views/assets/js/login.js', 'public/js/login.js')
    .sass('resources/views/assets/sass/login.scss', 'public/css/login.css')
    .sass('resources/views/assets/sass/reset.scss', 'public/css/reset.css')
    .sass('resources/views/assets/sass/app.scss', 'public/css/app.css')
    .js('resources/views/assets/js/app.js', 'public/js/app.js')
    .js('resources/views/assets/js/sw.js', 'public/sw.js')
    .copy('resources/views/assets/js/manifest.json', 'public/manifest.json')

    .copyDirectory('resources/views/assets/images', 'public/images')

    .options({
        processCssUrls: false
    })

    .version();
