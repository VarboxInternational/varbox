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

    .styles([
        'design/css/plugins/dropzone.css',
        'design/css/plugins/jcrop.css',
        'design/css/plugins/jstree.css',
        'design/css/plugins/quilljs.css',
        'design/css/plugins/select2.css',
        'design/css/plugins/select2-bootstrap.css',

        'design/css/app.css',
        'design/css/custom.css',
    ], 'public/css/app.css')

    .scripts([
        'design/js/vendors/bootstrap.js',
        'design/js/vendors/jquery.js',
        'design/js/plugins/bootbox.js',
        'design/js/plugins/dropzone.js',
        'design/js/plugins/fileupload.js',
        'design/js/plugins/inputmask.js',
        'design/js/plugins/jcrop.js',
        'design/js/plugins/jstree.js',
        'design/js/plugins/pgenerator.js',
        'design/js/plugins/quill.js',
        'design/js/plugins/quill-image.js',
        'design/js/plugins/select2.js',
        'design/js/plugins/tablednd.js',

        //'./vendor/proengsoft/laravel-jsvalidation/public/js/jsvalidation.min.js',


        'design/js/app.js',
    ], 'public/js/app.js');
