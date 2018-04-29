<?php

/*!
 * metowolf BilibiliHelper
 * https://i-meto.com/
 * Version 18.04.25 (0.7.3)
 *
 * Copyright 2018, metowolf
 * Released under the MIT license
 */

require 'vendor/autoload.php';

use Dotenv\Dotenv;
use metowolf\Bilibili\Daily;
use metowolf\Bilibili\GiftSend;
use metowolf\Bilibili\Heart;
use metowolf\Bilibili\Login;
use metowolf\Bilibili\Silver;
use metowolf\Bilibili\Task;
use metowolf\Bilibili\Log;

// timezone
date_default_timezone_set('Asia/Shanghai');

// load config
$dotenv = new Dotenv(__DIR__, '.env');
$dotenv->load();

// Log User config
$config = 'config';

if (!empty($argv[1])) {
    $config = $argv[1];
    Log::debug('从命令行参数读取配置文件！', [$config]);
} else {
    Log::debug('没有检测到命令行参数，使用默认参数', ['config']);
}

$dotenv = new Dotenv(__DIR__ . '/config/', $config);
$dotenv->load();

// Check ENV_NAME
if (strcmp(getenv('ENV_NAME'), $config)) {
    Log::debug('环境文件内置变量与文件名不符，修改中……', []);
    file_put_contents(__DIR__ . '/../config/' . $config, preg_replace(
        '/^' . 'ENV_NAME' . '=' . getenv('ENV_NAME') . '/m',
        'ENV_NAME' . '=' . $config,
        file_get_contents(__DIR__ . '/../config/' . $config)
    ));
}

Log::info('配置文件读取完毕！', [$config]);
$dotenv->overload();

// load ACCESS_KEY
Login::run();
$dotenv->overload();

Log::info('登陆完成！准备进入循环啦XD', []);
// run
while (true) {
    if (!Login::check()) {
        $dotenv->overload();
    }
    Daily::run();
    GiftSend::run();
    Heart::run();
    Silver::run();
    Task::run();
    sleep(10);
}