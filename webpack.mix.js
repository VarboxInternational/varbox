let mix = require('laravel-mix');

mix.options({
    terser: {
        extractComments: false,
    }
});

mix
    .copyDirectory('design/fonts', 'public/fonts')
    .copyDirectory('design/images', 'public/images')

    .sass('design/sass/app.scss', 'public/css/app.css')
    .js('design/js/app.js', 'public/js/app.js');

    /*.styles([
        'design/css/plugins/dropzone.css',
        'design/css/plugins/jcrop.css',
        'design/css/plugins/jstree.css',
        'design/css/plugins/quilljs.css',
        'design/css/plugins/select2.css',
        'design/css/plugins/select2-bootstrap.css',

        'design/css/app.css',
        'design/css/custom.css',
    ], 'public/css/app.css')*/

    /*.scripts([
        //'./vendor/proengsoft/laravel-jsvalidation/public/js/jsvalidation.min.js',

        /!*'design/dist/bootstrap.js',
        'design/js/app.js',*!/

        'design/js/app.min.js',
    ], 'public/js/app.js');*/
