<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/lumen.log'),
            'level' => 'debug',
        ],

        'debug' => [
            'driver' => 'daily',
            'path' => storage_path('logs/debug.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/lumen.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        //mq脚本错误日志
        'mqerror' => [
            'driver' => 'daily',
            'path' => storage_path('logs/mqerror.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        //http业务致命错误【重要】
        'corefailed' => [
            'driver' => 'daily',
            'path' => storage_path('logs/corefailed.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        //记录请求响应性能数据，仅在 debug 打开
        'profiler' => [
            'driver' => 'daily',
            'path' => storage_path('logs/profiler.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        //记录邮件告警相关日志，包含邮件不能发出时的错误，以及因告警频次限制不能发送的内容
        'mail-alert' => [
            'driver' => 'daily',
            'path' => storage_path('logs/mail-alert.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        //外部依赖相关日志
        'dependency' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dependency.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        //内置调度器调度日志，记录命令执行时间等信息
        'schedule' => [
            'driver' => 'daily',
            'path' => storage_path('logs/schedule.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Lumen Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],
        //记录 SQL 查询日志，包含性能相关信息
        'query' => [
            'driver' => 'daily',
            'path' => storage_path('logs/query.log'),
        ],
        //命令行程序直接相关日志
        'console' => [
            'driver' => 'daily',
            'path' => storage_path('logs/console.log')
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => ['stream' => 'php://stderr',],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        //错误类日志
        'error' => [
            'driver' => 'errorlog',
            'level' => 'debug',
            'path' => storage_path('logs/error.log'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],
    ],

];
