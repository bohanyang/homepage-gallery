<?php

date_default_timezone_set('Asia/Shanghai');

include __DIR__.'/Routes.php';
include __DIR__.'/Ctrls.php';

function notfound()
{
    http_response_code(404);
    echo 'File not found.'."\n";
    exit;
}

function parse_path_info($path_info = null)
{
    if (preg_match('/^\/(?:(.*[^\/])(\/?))?$/', $path_info, $captured) && !empty($captured[1])) {
        $exploded = explode('/', $captured[1]);
        if (empty($captured[2])) {
            return [$exploded, false];
        }

        return [$exploded, true];
    }

    return ['', true];
}

$path = parse_path_info(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '')[0];

if (empty($path[0])) {
    $path = $routes['_default'];
}

$params = null;

foreach ($routes as $route => $to) {
    if ($path[0] == $route) {
        if (!empty($to[1])) {
            foreach ($to[1] as $n => $rule) {
                $param = isset($path[$n + 1]) ? $path[$n + 1] : null;
                if ($param === null || !preg_match($rule, $param)) {
                    notfound();
                } else {
                    $params[$n] = $param;
                }
            }
        }

        if (empty($to[2])) {
            $ctrls[$to[0]]($params);
            exit;
        } else {
            header('Location: /'.$to[2].'/'.($params ? (implode('/', $params).'/') : ''), true, 301);
            exit;
        }
    }
}

notfound();
