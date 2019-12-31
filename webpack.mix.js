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

mix
    .setPublicPath('build')
    .setResourceRoot('.') // Relative
    .js('resources/js/route-browser.js', 'build')
    .sass('resources/sass/route-browser.scss', 'build')
    .copy('resources/img/favicon.png', 'build');
