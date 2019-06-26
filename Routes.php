<?php

$routes = [
    "images" => ["image", ["/^\w+$/"]],
    "Image" => [null, ["/^\w+$/"], "images"],
    "archives" => ["archive", ["/^[a-z]{2}-[A-Z]{2}$/","/^[0-9]{8}$/"]],
    "Archive" => [null, ["/^[a-z]{2}-[A-Z]{2}$/","/^[0-9]{8}$/"], "archives"],
    "p" => ["browse", ["/^[0-9]+$/"]],
    "Page" => [null, ["/^[0-9]+$/"], "p"],
    "d" => ["date", ["/^[0-9]{8}$/"]],
    "Date" => [null, ["/^[0-9]{8}$/"], "d"],
    "settings" => ["settings", null],
    "Settings" => [null, null, "settings"],
    // "_" . md5("_clear" . date("Ymd") . "uQjQpzZs49Em6kvtRt") => ["clear", null],
    "_default" => ["archives", "zh-CN", date("Ymd", strtotime('yesterday'))]
];
