<?php

return [
    'default' => 'database',

    'connections' => [
        'database' => [
            'driver' => 'database',
            'connection' => 'oracle',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'after_commit' => false,
        ],
    ],

    'batching' => [
        'database' => 'oracle',
        'table' => 'job_batches',
    ],

    'failed' => [
        'driver' => 'database-uuids',
        'database' => 'oracle',
        'table' => 'failed_jobs',
    ],
];
