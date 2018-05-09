<?php

/*!
 * metowolf BilibiliHelper
 * https://i-meto.com/
 *
 * Copyright 2018, metowolf
 * Released under the MIT license
 */

namespace BilibiliHelper\Plugin;

use BilibiliHelper\Lib\Log;
use BilibiliHelper\Lib\Curl;
use Wrench\Client;
use Socket\Raw\Factory;

class SocketClient extends Base
{
    const PLUGIN_NAME = 'socketClient';

    protected static function init()
    {
        if (static::data('socket') === NULL) {
            $factory = new Factory();
            try {
                $socket = $factory
                    ->createClient('tcp://' . getenv('SOCKET_SERVER_ADDR') . ':' . getenv('SOCKET_SERVER_PORT'))
                    ->setBlocking(false);
                static::data('socket', $socket);
            } catch (\Exception $e) {
                Log::warning('无法连接到指定监听服务器！将会在下一个循环重试。', [$e->getMessage()]);
            }
        }
    }

    protected static function work()
    {
        self::checkConnection();
        if (static::data('socket') !== NULL) {
            $content = self::getContent();
            $parse = self::parse($content);
            self::unpack($parse);
            Log::debug('解包完成！', $parse);
        }
    }

    protected static function checkConnection()
    {
        $socket = static::data('socket');
        try {
            $socket->write('success');
        } catch (\Exception $e) {
            // Looks like disconnect
            Log::warning('与监听服务器的连接好像断开了！', [$e->getMessage()]);
            $socket->close();
            static::$config['data'][static::PLUGIN_NAME]['socket'] = NULL;
        }
    }

    protected static function getContent()
    {
        $result = '';
        $socket = static::data('socket');
        while (true) {
            try {
                $tmp = $socket->read(1);
                $result .= $tmp;
            } catch (\Exception $e) {
                break;
                // Finished
            }
        }
        return $result;
    }

    protected static function parse($data)
    {
        $result = json_decode($data, true);
        if ($result === NULL) {
            Log::warning('监控服务器传入了无法解析的数据！', []);
            return '{}';
        }
        return $result;
    }

    protected static function unpack(array $data)
    {
        foreach ($data as $pluginName => $section) { // Key => section
            foreach ($section as $key => $value) {
                self::setValue($pluginName, $section, $key, $value);
            }
        }
    }

    protected static function setValue($pluginName, $section, $key, $value)
    {
        static::$config['data'][$pluginName][$section][$key] = $value;
    }
}
