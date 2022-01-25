<?php

declare (strict_types=1);

namespace funnymudpee\thinkphp\log\driver;

use think\App;
use think\contract\LogHandlerInterface;
use think\Log;


class Syslog implements LogHandlerInterface
{
    /**
     * 配置参数
     * @var array
     */
    protected $config = [
        'endpoint' => [
            'address' => '127.0.0.1',
            'port' => 20226,
        ],
        'msg' => [
            'hostname' => '',
            'tag' => 'teacup',
            'handler' => null,
        ]
    ];


    public function __construct(App $app, $config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * @param array $log
     * @return bool
     */
    public function save(array $log): bool
    {
        if (!extension_loaded('sockets')) {
            return true;
        }
        $s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        foreach ($log as $type => $val) {
            switch ($type) {
                case Log::ERROR:
                    // 1*8+3
                    $pri = 11;
                    break;
                case Log::DEBUG:
                    // 1*8+3
                    $pri = 15;
                    break;
                default:
                    $pri = 11;
            }
            foreach ($val as $msg) {
                $pri = '<' . $pri . '>';

                $header = date('M j H:i:s');
                if (!empty($this->config['msg']['hostname'])) {
                    $header .= ' ' . $this->config['msg']['hostname'];
                }

                $content = $this->config['msg']['tag'] . ': ';
                if (is_callable($this->config['msg']['handler'])) {
                    $content .= call_user_func_array($this->config['msg']['handler'], [$msg]);
                } else {
                    if (!is_string($msg)) {
                        $msg = var_export($msg, true);
                    }
                    $content .= $msg;
                }

                $syslogMsg = $pri . $header . ' ' . $content;
                $len = strlen($syslogMsg);

                socket_sendto($s, $syslogMsg, $len, 0, $this->config['endpoint']['address'], $this->config['endpoint']['port']);
            }
        }

        socket_close($s);

        return true;
    }
}
