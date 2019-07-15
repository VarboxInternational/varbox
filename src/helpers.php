<?php

if (!function_exists('query_cache')) {
    /**
     * @return \Varbox\Contracts\QueryCacheServiceContract
     */
    function query_cache()
    {
        return app('query_cache.service');
    }
}

if (!function_exists('form')) {
    /**
     * @return \Collective\Html\FormBuilder
     */
    function form()
    {
        return app('form');
    }
}

if (!function_exists('form_admin')) {
    /**
     * @return \Varbox\Contracts\AdminFormHelperContract
     */
    function form_admin()
    {
        return app('admin_form.helper');
    }
}

if (!function_exists('menu_admin')) {
    /**
     * @return \Varbox\Contracts\AdminMenuHelperContract
     */
    function menu_admin()
    {
        return app('admin_menu.helper');
    }
}

if (!function_exists('flash')) {
    /**
     * @param string|null $type
     * @return \Varbox\Contracts\FlashHelperContract
     */
    function flash($type = null)
    {
        return app('flash.helper', [
            'type' => $type
        ]);
    }
}

if (!function_exists('meta')) {
    /**
     * @return \Varbox\Helpers\MetaHelper
     */
    function meta()
    {
        return app('meta.helper');
    }
}

if (!function_exists('validation')) {
    /**
     * @param string|null $type
     * @return \Varbox\Contracts\ValidationHelperContract
     */
    function validation($type = null)
    {
        return app('validation.helper', [
            'type' => $type
        ]);
    }
}

if (!function_exists('button')) {
    /**
     * @return \Varbox\Contracts\ButtonHelperContract
     */
    function button()
    {
        return app('button.helper');
    }
}

if (!function_exists('breadcrumbs')) {
    /**
     * @return \DaveJamesMiller\Breadcrumbs\BreadcrumbsManager
     */
    function breadcrumbs()
    {
        return app(DaveJamesMiller\Breadcrumbs\BreadcrumbsManager::class);
    }
}

if (!function_exists('relation')) {
    /**
     * @return \Varbox\Helpers\RelationHelper
     */
    function relation()
    {
        return new \Varbox\Helpers\RelationHelper;
    }
}

if (!function_exists('upload')) {
    /**
     * @param $file
     * @param  \Illuminate\Database\Eloquent\Model|null $model
     * @param null $field
     * @return \Varbox\Contracts\UploadServiceContract
     */
    function upload($file, Illuminate\Database\Eloquent\Model $model = null, $field = null)
    {
        return app('upload.service', [
            'file' => $file,
            'model' => $model,
            'field' => $field
        ]);
    }
}

if (!function_exists('uploaded')) {
    /**
     * @param string $file
     * @return \Varbox\Contracts\UploadedHelperContract
     */
    function uploaded($file)
    {
        return app('uploaded.helper', [
            'file' => $file
        ]);
    }
}

if (!function_exists('uploader')) {
    /**
     * @return \Varbox\Contracts\UploaderHelperContract
     */
    function uploader()
    {
        return app('uploader.helper');
    }
}

if (!function_exists('is_json_format')) {
    /**
     * @param $string
     * @return bool
     */
    function is_json_format($string)
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('array_depth')) {
    /**
     * @param array $array
     * @return int
     */
    function array_depth(array $array) {
        $maxDepth = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = array_depth($value) + 1;

                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }

        return $maxDepth;
    }
}

if (!function_exists('array_search_key_recursive')) {
    /**
     * @param string|int $needle
     * @param array $haystack
     * @param bool $regexp
     * @return mixed|null
     */
    function array_search_key_recursive($needle, array $haystack = [], $regexp = false)
    {
        $array = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($haystack),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($array as $key => $value) {
            if ($regexp ? str_is($key, $needle) : $key === $needle) {
                return $value;
            }
        }

        return null;
    }
}

if (!function_exists('get_object_vars_recursive')) {

    function get_object_vars_recursive($object)
    {
        $result = [];
        $vars = is_object($object) ? get_object_vars($object) : $object;

        foreach ($vars as $key => $value) {
            $value = (is_array($value) || is_object($value)) ? get_object_vars_recursive($value) : $value;
            $result[$key] = $value;
        }

        return $result;
    }
}