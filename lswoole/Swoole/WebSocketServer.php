<?php

namespace LSwoole\Swoole;

use LSwoole\Illuminate\Laravel;
use LSwoole\Swoole\ServerMonitor\WebSocketServerMonitor;
use Swoole\WebSocket\Server as SwooleServer;

class WebSocketServer
{
    const HOST = "0.0.0.0";
    const PORT = 9090;
    const WORKER_NUM = 4;
    const TASK_WORKER_NUM = 2;
    const IS_ENABLE_STATIC_HANDLER = true;
    const STATIC_ROOT = __DIR__ . '/../../resources/live';
    const ROOT_PATH = __DIR__ . '/../../';

    public $server = null;

    protected $http_kernel;
    /**
     * @var Laravel $laravel
     */
    protected $laravel;

    /**
     * WebServer constructor.
     */
    public function __construct()
    {
        $this->server = new SwooleServer(self::HOST, self::PORT);

        $this->server->set([
            'enable_static_handler' => self::IS_ENABLE_STATIC_HANDLER,
            'document_root'         => self::STATIC_ROOT,
            'worker_num'            => self::WORKER_NUM,
            'task_worker_num'       => self::TASK_WORKER_NUM,
            'daemonize'             => 1,
            'log_file'              => __DIR__ . '/../../storage/logs/swoole.log',
            'log_level'             => 1,
        ]);

        $config = [
            'root_path' => self::ROOT_PATH,
        ];

        $this->laravel = Laravel::create($config, $this->server)->initLaravel();
    }

    /**
     * 启动 server
     */
    public function run()
    {

        WebSocketServerMonitor::monitor($this->server, $this->laravel);

        // 启动
        $this->server->start();
    }


}