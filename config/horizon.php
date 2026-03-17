<?php

use Illuminate\Support\Str;

return [
    'name' => env('HORIZON_NAME', env('APP_NAME', 'LearnFlow')),

    'domain' => env('HORIZON_DOMAIN'),

    'path' => env('HORIZON_PATH', 'horizon'),

    /*
     * Horizon stores its own metadata (supervisors, metrics, failed jobs, etc.)
     * in this Redis connection.
     */
    'use' => env('HORIZON_REDIS_CONNECTION', 'default'),

    'prefix' => env(
        'HORIZON_PREFIX',
        Str::slug((string) env('APP_NAME', 'learnflow'), '_').'_horizon:'
    ),

    'middleware' => ['web'],

    'waits' => [
        'redis:default' => 60,
        'redis:notifications' => 30,
        'redis:media' => 300,
        'redis:webhooks' => 30,
    ],

    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 10080,
    ],

    'silenced' => [
        //
    ],

    'metrics' => [
        'trim_snapshots' => [
            'job' => 24,
            'queue' => 24,
        ],
    ],

    'fast_termination' => env('HORIZON_FAST_TERMINATION', true),

    'memory_limit' => (int) env('HORIZON_MEMORY_LIMIT', 64),

    /*
     * Named queues for LearnFlow.
     */
    'defaults' => [
        'default' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'simple',
            'maxProcesses' => 2,
            'memory' => 128,
            'tries' => 1,
            'timeout' => 60,
            'nice' => 0,
        ],

        'notifications' => [
            'connection' => 'redis',
            'queue' => ['notifications'],
            'balance' => 'simple',
            'maxProcesses' => 3,
            'memory' => 128,
            'tries' => 1,
            'timeout' => 30,
            'nice' => 0,
        ],

        'media' => [
            'connection' => 'redis',
            'queue' => ['media'],
            'balance' => 'simple',
            'maxProcesses' => 1,
            'memory' => 256,
            'tries' => 1,
            'timeout' => 300,
            'nice' => 0,
        ],

        'webhooks' => [
            'connection' => 'redis',
            'queue' => ['webhooks'],
            'balance' => 'simple',
            'maxProcesses' => 2,
            'memory' => 128,
            'tries' => 3,
            'timeout' => 30,
            'nice' => 0,
        ],
    ],

    'environments' => [
        'production' => [
            'default' => [
                'maxProcesses' => 2,
            ],
            'notifications' => [
                'maxProcesses' => 3,
            ],
            'media' => [
                'maxProcesses' => 1,
            ],
            'webhooks' => [
                'maxProcesses' => 2,
                'tries' => 3,
            ],
        ],

        'local' => [
            'default' => [
                'maxProcesses' => 2,
            ],
            'notifications' => [
                'maxProcesses' => 3,
            ],
            'media' => [
                'maxProcesses' => 1,
            ],
            'webhooks' => [
                'maxProcesses' => 2,
                'tries' => 3,
            ],
        ],
    ],

    'watch' => [
        'app',
        'bootstrap',
        'config/**/*.php',
        'database/**/*.php',
        'public/**/*.php',
        'resources/**/*.php',
        'routes',
        'composer.lock',
        'composer.json',
        '.env',
    ],
];

