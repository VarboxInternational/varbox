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
        'max_size' => 5,

        /*
        |
        | The allowed extensions for image files.
        | All image extensions can be found in \Varbox\Services\UploadService::getImageExtensions().
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
            'width' => 100,
            'height' => 100,
        ],

        /*
        |
        | The maximum resolution in pixels the original image can have.
        |
        | If the uploaded image is bigger, it will be resized (keeping aspect ratio).
        | To ignore this, pass "null" to both "width" and "height" options.
        |
        */
        'max_resolution' => [
            'width' => 1600,
            'height' => 1600,
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
        'max_size' => 10,

        /*
        |
        | The allowed extensions for video files.
        | All video extensions can be found in \Varbox\Services\UploadService::getVideoExtensions().
        |
        | You can specify allowed extensions by using an array, or a comma "," separated string of extensions.
        | To allow uploading any video files, specify the "null" value for this option.
        |
        */
        'allowed_extensions' => [
            'mp4', 'flv', 'avi',
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
        | FFMpeg & FFProbe binaries path.
        | Only used if you try to generate video thumbnails and/or styles.
        |
        | This configuration options will be used to override the original "config/laravel-ffmpeg.php" config file.
        | For more details please see "Pbmedia - Laravel FFMpeg" (https://github.com/pascalbaljetmedia/laravel-ffmpeg)
        |
        */
        'binaries' => [

            'ffmpeg' => env('FFMPEG_PATH', 'ffmpeg'),
            'ffprobe' => env('FFPROBE_PATH', 'ffprobe'),

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
        'max_size' => 5,

        /*
        |
        | The allowed extensions for audio files.
        | All audio extensions can be found in \Varbox\Services\UploadService::getAudioExtensions().
        |
        | You can specify allowed extensions by using an array, or a comma "," separated string of extensions.
        | To allow uploading any audio files, specify the "null" value for this option.
        |
        */
        'allowed_extensions' => [
            'mp3', 'wav', 'aac', 'ogg',
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
        'max_size' => 2,

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
