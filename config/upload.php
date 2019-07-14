<?php

return [

    'storage' => [

        /*
        |
        | The storage disk where to upload the files.
        |
        | By default, the "uploads" storage disk is used.
        | The "uploads" disk is automatically defined upon running "php artisan varbox:install".
        |
        | If the disk is not yet defined, you will have to define it yourself.
        | To do this, follow these steps:
        |
        | 1. Go to config/filesystems under "disks" and create a new disk called "uploads", with the following options.
        | - 'driver' => 'local';
        | - 'root' => storage_path('uploads');
        | - 'url' => env('APP_URL').'/uploads';
        | - 'visibility' => 'public';
        |
        | 2. Verify if the /storage/uploads directory exists and if not, create it with a .gitignore file inside.
        |
        | 3. Run the command: "php artisan varbox:uploads-link", to create a symlink between the storage and public directories.
        |
        */
        'disk' => 'uploads',

        /*
        |
        | Flag indicating that on record upload, to keep or remove both old uploaded file (and it's dependencies) and the database record.
        |
        | Set this to true in order to keep old files and database records when uploading a new file for the same model entity field.
        | Set this to false in order to remove old files from disk and also delete the database record for the old file.
        |
        |
        | Notice: setting this to false is discouraged.
        |
        */
        'keep_old' => true,

        /*
        |
        | Flag indicating that on file upload, to take into consideration if the existing file is in the same full path.
        |
        | Given you attempt to store a file on disk with the full path of "x/y/z.ext".
        |
        | Having this option set to false, will check if the actual full path file exists.
        | If it does exist, it simply skips the custom image and video styles generation.
        | Because it will be safe to assume they are the same, given that the original file already exists.
        |
        | Having this option set to true, will force the script to upload the newly generated image and video styles.
        | The force style generation will happen regardless the fact that there was already an existing original file with that full path.
        |
        | Note that this option only applies to image and video files.
        | The original and thumbnails will be uploaded no matter what.
        |
        */
        'override_dependencies' => false,

        /*
        |
        | Flag indicating a file's visibility type.
        |
        | Please note that this option is used only when opting for an "s3" storage.
        | The value of the visibility key may be any visibility type supported by Amazon S3 and Laravel's Filesystem storage.
        |
        */
        'visibility' => 'public',

    ],

    'database' => [

        /*
        |
        | Determine if the uploaded files' details will be saved to the database.
        |
        | This is encouraged, every uploaded file would have more details about it.
        | Also, if this is saved, applying the HasUploads trait on models, unlocks certain query scopes.
        |
        | To disable this, set the value to "false".
        |
        */
        'save' => true,

    ],

    'images' => [

        /*
        |
        | The maximum size allowed for uploaded image files in MB.
        |
        | If any integer value is specified, files larger than this defined size won't be uploaded.
        | To allow all image files of all sizes to be uploaded, specify the "null" value for this option.
        |
        */
        'max_size' => 50,

        /*
        |
        | The allowed extensions for image files.
        | All image extensions can be found in \Varbox\Media\Services\UploadService::getImageExtensions().
        |
        | You can specify allowed extensions by using an array, or a comma "," separated string of extensions.
        | To allow uploading any image files, specify the "null" value for this option.
        |
        */
        'allowed_extensions' => [
            'jpeg', 'jpg', 'png', 'gif', 'bmp',
        ],

        /*
        |
        | The quality at which to save the uploaded images.
        |
        | Here you can specify an integer value between 1 and 100.
        | If no value is specified (eg. null), then the uploaded image's quality will be set to 90.
        |
        */
        'quality' => 75,

        /*
        |
        | Flag that on image upload to generate one thumbnail as well (true | false).
        |
        | This generated thumbnail will interlace with the image's styles.
        | So be careful not to override it by defining an image style called "thumbnail".
        |
        */
        'generate_thumbnail' => true,

        /*
        |
        | The size at which the generated thumbnail will be saved.
        | Please note that the thumbnail will be automatically fitted, keeping the ratio of the original image.
        | Not specifying this option's width and height will force the generated thumbnail to resize itself to 100x100px.
        |
        */
        'thumbnail_style' => [
            'width' => 150,
            'height' => 150
        ],

        /*
        |
        | The styles to create from the original uploaded image.
        | You can specify multiple styles, as an array.
        |
        | Specify the "ratio" = true individually on each style, to let the uploader know you want to preserve the original ratio.
        |
        | If ratio preserving is enabled, the image will first be re-sized and then cropped.
        | If ratio preserving is disabled, the image will only be re-sized at the width and height specified.
        |
        | Also, not specifying the ratio for a style, it will consider the ratio as enabled.
        | The only way to disable the ratio, is to set it to false.
        |
        | IMPORTANT
        | ------------------------------------------------------------------------------------------------------------------------
        | You should specify this option in the model, using the HasUploads trait method: getUploadConfig().
        | Note that the getUploadConfig() method is capable of overwriting the config values from this file.
        | With that said, keep in mind that you can specify other options, not just the image styles.
        |
        | To specify the image styles, return an array like: [images => [styles => [field => [name => [width, height, ratio]]]]
        |
        | It is good to know that you can also specify multiple fields using regex.
        | metadata[items][*][image] => applies to metadata[items][1][image], metadata[items][two][image], etc.
        |
        */
        'styles' => [],

        /*
        |
        | When uploading images, they will automatically be optimized.
        | When calling `optimize` the upload functionality will automatically determine which optimizers should run for the given image.
        |
        | This configuration option will be used to override the original "config/image-optimizer.php" config file.
        | For more details, please see "Spatie - Image Optimizer" (https://github.com/spatie/image-optimizer).
        |
        */
        'optimizers' => [

            \Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
                '-m85',                 // set maximum quality to 85%
                '--strip-all',          // this strips out all text information such as comments and EXIF data
                '--all-progressive',    // this will make sure the resulting image is a progressive one
            ],

            \Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
                '--force',              // required parameter for this package
            ],

            \Spatie\ImageOptimizer\Optimizers\Optipng::class => [
                '-i0',                  // this will result in a non-interlaced, progressive scanned image
                '-o2',                  // this set the optimization level to two (multiple IDAT compression trials)
                '-quiet',               // required parameter for this package
            ],

            \Spatie\ImageOptimizer\Optimizers\Svgo::class => [
                '--disable=cleanupIDs', // disabling because it is known to cause troubles
            ],

            \Spatie\ImageOptimizer\Optimizers\Gifsicle::class => [
                '-b',                   // required parameter for this package
                '-O3',                  // this produces the slowest but best results
            ],
        ],
    ],

    'videos' => [

        /*
        |
        | The maximum size allowed for uploaded video files in MB.
        |
        | If any integer value is specified, files larger than this defined size won't be uploaded.
        | To allow all video files of all sizes to be uploaded, specify the "null" value for this option.
        |
        */
        'max_size' => 50,

        /*
        |
        | The allowed extensions for video files.
        | All video extensions can be found in \Varbox\Media\Services\UploadService::getVideoExtensions().
        |
        | You can specify allowed extensions by using an array, or a comma "," separated string of extensions.
        | To allow uploading any video files, specify the "null" value for this option.
        |
        */
        'allowed_extensions' => [
            'mp4', 'flv', 'avi', 'mov', 'webm', 'mpeg', 'mpg', 'mkv', 'acc',
        ],

        /*
        |
        | Flag that on video upload to generate thumbnails as well (true | false).
        |
        | Thumbnail will be generated from the first second of the uploaded video.
        | All thumbnails will be stored as images having the name {video_file}_thumbnail_{number}.jpg.
        |
        */
        'generate_thumbnails' => true,

        /*
        |
        | How many thumbnails should be generated for a video.
        |
        | Keep in mind that if this option is invalid (ex: 0, null, ''), thumbnails won't be generated.
        | This is happening regardless the "generate_thumbnails" options.
        |
        */
        'thumbnails_number' => 3,

        /*
        |
        | The styles to create from the original uploaded video.
        | You can specify multiple styles, as an array.
        |
        | IMPORTANT
        | ------------------------------------------------------------------------------------------------------------------------
        | You should specify this option in the model, using the HasUploads trait method: getUploadConfig().
        | Note that the getUploadConfig() method is capable of overwriting the config values from this file.
        | With that said, keep in mind that you can specify other options, not just the video styles.
        |
        | To specify the video styles, return an array like: [videos => [styles => [field => [name => [width, height]]]]
        |
        */
        'styles' => [],

        /*
       |
       | FFMpeg & FFProbe binaries path.
       | Only used if you try to generate video thumbnails and/or styles.
       |
       | This configuration options will be used to override the original "config/laravel-ffmpeg.php" config file.
       | For more details please see "Pbmedia - Laravel FFMpeg" (https://github.com/pascalbaljetmedia/laravel-ffmpeg)
       |
       */
        'binaries' => [

            'ffmpeg' => '/usr/bin/ffmpeg',
            'ffprobe' => '/usr/bin/ffprobe',

        ],

    ],

    'audios' => [

        /*
        |
        | The maximum size allowed for uploaded audio files in MB.
        |
        | If any integer value is specified, files larger than this defined size won't be uploaded.
        | To allow all audio files of all sizes to be uploaded, specify the "null" value for this option.
        |
        */
        'max_size' => 30,

        /*
        |
        | The allowed extensions for audio files.
        | All audio extensions can be found in \Varbox\Media\Services\UploadService::getAudioExtensions().
        |
        | You can specify allowed extensions by using an array, or a comma "," separated string of extensions.
        | To allow uploading any audio files, specify the "null" value for this option.
        |
        */
        'allowed_extensions' => [
            'mp3', 'aac', 'wav', 'wma', 'oga', 'flac',
        ],

    ],

    'files' => [

        /*
        |
        | The maximum size allowed for uploaded normal files in MB.
        |
        | If any integer value is specified, files larger than this defined size won't be uploaded.
        | To allow all files of all sizes to be uploaded, specify the "null" value for this option.
        |
        */
        'max_size' => 10,

        /*
        |
        | The allowed extensions for normal files.
        |
        | You can specify allowed extensions by using an array, or a comma "," separated string of extensions.
        | To allow uploading any audio files, specify the "null" value for this option.
        |
        */
        'allowed_extensions' => null,

    ],

];
