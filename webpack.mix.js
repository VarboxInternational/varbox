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

mix
    .copyDirectory('design/fonts', 'public/fonts')
    .copyDirectory('design/images', 'public/images')

    .styles([
        'design/css/plugins/dropzone.css',
        'design/css/plugins/froala.css',
        'design/css/plugins/jcrop.css',
        'design/css/plugins/jstree.css',
        'design/css/plugins/select2.css',
        'design/css/plugins/select2-bootstrap.css',

        'design/css/app.css',
        'design/css/custom.css',
    ], 'public/css/app.css')

    .scripts([
        'design/js/vendors/jquery.min.js',
        'design/js/vendors/jquery-ui.min.js',
        'design/js/vendors/bootstrap.min.js',

        'design/js/plugins/bootbox.min.js',
        'design/js/plugins/dropzone.min.js',
        'design/js/plugins/froala.min.js',
        'design/js/plugins/generator.min.js',
        'design/js/plugins/inputmask.min.js',
        'design/js/plugins/jcrop.min.js',
        'design/js/plugins/jstree.min.js',
        'design/js/plugins/select2.min.js',
        'design/js/plugins/tablednd.min.js',
        'design/js/plugins/upload.min.js',

        './vendor/proengsoft/laravel-jsvalidation/public/js/jsvalidation.min.js',

        'design/js/helpers.js',
        'design/js/app.js',
    ], 'public/js/app.js');
