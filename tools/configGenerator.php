<?php
/**
 * Created by PhpStorm.
 * User: neverbehave
 * Date: 2018/4/30
 * Time: 上午12:56
 */

$format = ['APP_USER', 'APP_PASS', 'ROOM_ID'];
$configs = [
    ['APP_USER', 'APP_PASS', 'ROOM_ID']
];

foreach ($configs as $config) {
    $template = file_get_contents(__DIR__ . 'configs.example');
    foreach ($format as $index => $f) {
        $template = envReplace($f, $config[$index], $template);
    }
    file_put_contents(__DIR__ . '../config/' . $config[0], $template);
}

function envReplace($key, $value, $content)
{
    return preg_replace(
        '/^' . $key . '=.*' . '/',
        $key . '=' . $value,
        $content
    );
}