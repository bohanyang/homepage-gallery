<?php

require 'vendor/autoload.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('Views/');
$twig = new Twig_Environment($loader);

$filters = [
    "md" => function ($date) {
        return date('Y/n/j', strtotime($date));
    },
    "pd" => function ($date) {
        return date('Ymd', strtotime($date . ' -1 day'));
    },
    "nd" => function ($date) {
        return date('Ymd', strtotime($date . ' +1 day'));
    },
    "rn" => function ($region) {
        $regions = array(
            'zh-CN' => '中国',
            'en-US' => '美国',
            'en-GB' => '英国',
            'en-AU' => '澳大利亚',
            'en-CA' => '加拿大 (英语)',
            'fr-FR' => '法国',
            'de-DE' => '德国',
            'pt-BR' => '巴西',
            'ja-JP' => '日本',
            'fr-CA' => '加拿大 (法语)',
            'en-IN' => '印度'
        );
        return $regions[$region];
    },
    "flag" => function ($a) {
        $flags = array(
            'zh-CN' => 'https://cdn.jsdelivr.net/gh/brentybh/homepage-gallery@5f92ffa5957159a5810d70a646ff7e805a98c4dd/assets/images/5bfe46198a0f5.png',
            'en-US' => 'https://cdn.jsdelivr.net/gh/brentybh/homepage-gallery@5f92ffa5957159a5810d70a646ff7e805a98c4dd/assets/images/5bfe461b2f231.png',
            'en-GB' => 'https://cdn.jsdelivr.net/gh/brentybh/homepage-gallery@5f92ffa5957159a5810d70a646ff7e805a98c4dd/assets/images/5bfe46191e2f3.png',
            'en-AU' => 'https://cdn.jsdelivr.net/gh/brentybh/homepage-gallery@5f92ffa5957159a5810d70a646ff7e805a98c4dd/assets/images/5bfe461988908.png',
            'en-CA' => 'https://cdn.jsdelivr.net/gh/brentybh/homepage-gallery@5f92ffa5957159a5810d70a646ff7e805a98c4dd/assets/images/5bfe461a269d3.png',
            'fr-FR' => 'https://cdn.jsdelivr.net/gh/brentybh/homepage-gallery@5f92ffa5957159a5810d70a646ff7e805a98c4dd/assets/images/5bfe461a8c16a.png',
            'de-DE' => 'https://cdn.jsdelivr.net/gh/brentybh/homepage-gallery@5f92ffa5957159a5810d70a646ff7e805a98c4dd/assets/images/5bfe46198703c.png',
            'pt-BR' => 'https://cdn.jsdelivr.net/gh/brentybh/homepage-gallery@5f92ffa5957159a5810d70a646ff7e805a98c4dd/assets/images/5bfe4619217f5.png',
            'ja-JP' => 'https://cdn.jsdelivr.net/gh/brentybh/homepage-gallery@5f92ffa5957159a5810d70a646ff7e805a98c4dd/assets/images/5bfe461a1faf8.png',
            'fr-CA' => 'https://cdn.jsdelivr.net/gh/brentybh/homepage-gallery@5f92ffa5957159a5810d70a646ff7e805a98c4dd/assets/images/5bfe478326de0.png',
            'en-IN' => 'https://cdn.jsdelivr.net/gh/brentybh/homepage-gallery@5f92ffa5957159a5810d70a646ff7e805a98c4dd/assets/images/5bfe46191fbec.png'
        );
        return $flags[$a];
    }
];

foreach ($filters as $name => $func) {
    $twig->addFilter(new Twig_SimpleFilter($name, $func));
}

$renderer = [
    "archive" => function ($archive, $mirror, $v_mirror, $res) use ($twig) {
        $params = [
            "archive" => $archive,
            "mirror" => $mirror,
            "res" => $res
        ];
        if (isset($archive['image']['vid'])) {
            $params['video'] = preg_replace(
                '/\/\/az29176\.vo\.msecnd\.net/',
                $v_mirror,
                $archive['image']['vid']['sources'][1][2]
            );
        } else {
            $params['video'] = false;
        }
        return $twig->render('archive.twig', $params);
    },

    "date" => function ($list, $date, $mirror, $thumbres) use ($twig) {
        $params = array(
            'list' => $list,
            'date' => $date,
            'mirror' => $mirror,
            'thumbres' => $thumbres
        );
        return $twig->render('date.twig', $params);
    },

    "images" => function ($images, $page, $mirror, $thumbres) use ($twig) {
        $params = array(
            'images' => $images,
            'page' => $page,
            'title' => '第 ' . $page . ' 页',
            'mirror' => $mirror,
            'thumbres' => $thumbres
        );
        return $twig->render('images.twig', $params);
    },

    "image" => function ($image, $mirror, $v_mirror, $res) use ($twig) {
        $params = [
            "image" => $image,
            "mirror" => $mirror,
            "res" => $res
        ];
        if (isset($image[0]['image']['vid'])) {
            $params['video'] = preg_replace(
                '/\/\/az29176\.vo\.msecnd\.net/',
                $v_mirror,
                $image[0]['image']['vid']['sources'][1][2]
            );
        } else {
            $params['video'] = false;
        }
        return $twig->render('image.twig', $params);
    },

    "settings" => function ($goto) use ($twig) {
        if (!empty($goto)) {
            $params = array('goto' => $goto);
        } else {
            $params = [];
        }
        return $twig->render('settings.twig', $params);
    }
];
