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

mix.js('resources/js/app.js', 'public/js');

mix.scripts([
   'resources/js/analytics-tracking.js',
   'resources/js/faq.js',
   'resources/js/form-validator.js',
   'resources/js/lesson-picker.js',
   'resources/js/scroll.js',
   'resources/js/swipe.js'
], 'public/js/legacy.js');

mix.scripts([
   'resources/js/admin/customer-manager.js',
   'resources/js/admin/testimonials-manager.js',
   'resources/js/form-validator.js',
   'resources/js/scroll.js'
], 'public/js/admin.js');

mix.copy('node_modules/jquery-touchswipe/jquery.touchSwipe.min.js', 'public/js/vendor/jquery.touchSwipe.min.js');

mix.less('resources/less/app.less', 'public/css')
   .less('resources/less/admin.less', 'public/css');

if (mix.inProduction()) {
   mix.version(['public/css/app.css', 'public/js/app.js']);
}
