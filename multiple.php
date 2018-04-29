<?php
/**
 * Created by PhpStorm.
 * User: NeverBehave
 * Date: 2018/4/29
 * Time: 下午11:17
 */

$service = [];

foreach (glob(__DIR__ . "/config/*") as $fileFullPath) {
    $filename = basename($fileFullPath);
    print_r($filename . " has loaded!\r\n");
    passthru(<<<EOF
screen -dmS $filename bash -c 'while true; do php index.php $fileFullPath; done'
EOF
    );
}

