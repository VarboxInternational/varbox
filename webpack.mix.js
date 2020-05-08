let mix = require('laravel-mix');

mix.options({
    terser: {
        extractComments: false,
    }
});

mix
    .copyDirectory('resources/fonts', 'public/fonts')
    .copyDirectory('resources/images', 'public/images')

    .styles([
        'resources/css/plugins/dropzone.css',
        'resources/css/plugins/jcrop.css',
        'resources/css/plugins/jstree.css',
        'resources/css/plugins/quill.css',
        'resources/css/plugins/select2.css',
        'resources/css/plugins/select2-bootstrap.css',

        'resources/css/app.css',
        'resources/css/custom.css',
    ], 'public/css/app.css')

    .scripts([
        'resources/js/vendors/jquery.js',
        'resources/js/vendors/jquery-ui.js',
        'resources/js/vendors/bootstrap.js',
        'resources/js/plugins/bootbox.js',
        'resources/js/plugins/dropzone.js',
        'resources/js/plugins/fileupload.js',
        'resources/js/plugins/inputmask.js',
        'resources/js/plugins/jcrop.js',
        'resources/js/plugins/jstree.js',
        'resources/js/plugins/pgenerator.js',
        'resources/js/plugins/quill.js',
        'resources/js/plugins/quill-image.js',
        'resources/js/plugins/select2.js',
        'resources/js/plugins/tablednd.js',

        'resources/js/app.js',
    ], 'public/js/app.js');
