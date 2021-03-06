<?php

include __DIR__.'/Config.php';

if (!empty($_COOKIE['image']) and !empty($_COOKIE['imagepreview'])) {
    $config['size'] = $_COOKIE['image'];
    $config['sizePreview'] = $_COOKIE['imagepreview'];
}

include __DIR__.'/Model.php';

$model = new Model(
    $config['appId'],
    $config['appKey'],
    $config['apiUrl']
);

include __DIR__.'/Classes/Cache'.$config['cacheDriver'].'.php';

$cache = new Cache($config['cacheConfig']);

include __DIR__.'/Renderer.php';

$ctrls = [
    'image' => function ($params) use ($renderer, $model, $config, $cache) {
        $expireTime = $cache->dailyExpire('16:04:00');

        $raw = $cache->func('Raw:Image:'.$params[0], function () use ($model, $params) {
            return json_encode($model->image($params[0]));
        }, $expireTime);

        echo $renderer['image'](
            json_decode($raw, true),
            $config['cdnImage'],
            $config['cdnVideo'],
            $config['size']
        );
    },

    'archive' => function ($params) use ($renderer, $model, $config, $cache) {
        $expireTime = $cache->dailyExpire('16:04:00');

        $raw = $cache->func('Raw:Archive:'.$params[0].':'.$params[1], function () use ($model, $params) {
            return json_encode($model->archive($params[0], $params[1]));
        }, $expireTime);

        echo $renderer['archive'](
            json_decode($raw, true),
            $config['cdnImage'],
            $config['cdnVideo'],
            $config['size']
        );
    },

    'browse' => function ($params) use ($renderer, $model, $config, $cache) {
        $expireTime = $cache->dailyExpire('16:04:00');

        $raw = $cache->func('Raw:Browse:'.$params[0], function () use ($model, $params, $config) {
            return json_encode($model->images($params[0], $config['perPage']));
        }, $expireTime);

        echo $renderer['images'](
            json_decode($raw, true),
            $params[0],
            $config['cdnImage'],
            $config['sizePreview']
        );
    },

    'date' => function ($params) use ($renderer, $model, $config, $cache) {
        $expireTime = $cache->dailyExpire('16:04:00');

        $raw = $cache->func('Raw:Date:'.$params[0], function () use ($model, $params) {
            return json_encode($model->archives($params[0]));
        }, $expireTime);

        echo $renderer['date'](
            json_decode($raw, true),
            $params[0],
            $config['cdnImage'],
            $config['sizePreview']
        );
    },

    'clear' => function () use ($cache) {
        var_dump($cache->redis->flushdb());
    },

    'settings' => function () use ($renderer, $config) {
        if (!empty($_POST['wp']) and !empty($_POST['thumb'])) {
            setcookie('image', $_POST['wp'], time() + 3600 * 24 * 365 * 5, '/');
            setcookie('imagepreview', $_POST['thumb'], time() + 3600 * 24 * 365 * 5, '/');
            if (empty($_POST['goto'])) {
                header('Location: /', true, 303);
            } else {
                header('Location: '.$_POST['goto'], true, 303);
            }
        } else {
            $goto = false;
            if (!empty($_SERVER['HTTP_REFERER'])) {
                $goto = $_SERVER['HTTP_REFERER'];
            }
            echo $renderer['settings']($goto);
        }
    },
];
