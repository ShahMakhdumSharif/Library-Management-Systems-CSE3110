<?php

return [
    'default' => 'database',

    'stores' => [
        'database' => [
            'driver' => 'database',
            'connection' => 'oracle',
            'table' => 'cache',
            'lock_connection' => 'oracle',
            'lock_table' => 'cache_locks',
        ],
    ],

    'prefix' => env('CACHE_PREFIX', 'central-library-cache-'),
];
