<?php

if (file_exists(__DIR__.'/env.php')) {
    include __DIR__.'/env.php';
}

function get_env($varname)
{
    return getenv($varname, true) ?: getenv($varname);
}

$config = array(
    'appId' => get_env('LEANCLOUD_APP_ID'),
    'appKey' => get_env('LEANCLOUD_APP_KEY'),
    'apiUrl' => get_env('LEANCLOUD_API_URL') ?: 'https://avoscloud.com/1.1/',
    'cdnImage' => get_env('APP_CDN_IMAGE') ?: 'https://www.bing.com',
    'cdnVideo' => get_env('APP_CDN_VIDEO') ?: 'https://az29176.vo.msecnd.net',
    'size' => get_env('APP_SIZE_IMAGE') ?: '1920x1080',
    'sizePreview' => get_env('APP_SIZE_PREVIEW') ?: '1366x768',
    'perPage' => get_env('APP_PERPAGE') ?: 15,
    'cacheDriver' => get_env('APP_CACHE_DRIVER') ?: 'Redis',
    'cacheConfig' => get_env('APP_CACHE_CONFIG') ?: null,
);
