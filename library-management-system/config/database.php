<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Oracle-only database configuration
    |--------------------------------------------------------------------------
    |
    | This project intentionally supports Oracle Database only. All application
    | queries are executed through the OCI8 Oracle driver.
    |
    */

    'default' => 'oracle',

    'connections' => [
        'oracle' => [
            'driver' => 'oracle',
            'tns' => env('DB_TNS', ''),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '1521'),
            'database' => env('DB_DATABASE', 'XEPDB1'),
            'service_name' => env('DB_SERVICE_NAME', env('DB_DATABASE', 'XEPDB1')),
            'username' => env('DB_USERNAME', 'library_user'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'AL32UTF8'),
            'prefix' => env('DB_PREFIX', ''),
            'prefix_schema' => env('DB_SCHEMA_PREFIX', ''),
            'edition' => env('DB_EDITION', 'ora$base'),
            'server_version' => env('DB_SERVER_VERSION', '21c'),
            'load_balance' => env('DB_LOAD_BALANCE', 'yes'),
            'max_name_len' => env('ORA_MAX_NAME_LEN', 30),
            'dynamic' => [],
            'sessionVars' => [
                'NLS_TIME_FORMAT' => 'HH24:MI:SS',
                'NLS_DATE_FORMAT' => 'YYYY-MM-DD HH24:MI:SS',
                'NLS_TIMESTAMP_FORMAT' => 'YYYY-MM-DD HH24:MI:SS',
                'NLS_TIMESTAMP_TZ_FORMAT' => 'YYYY-MM-DD HH24:MI:SS TZH:TZM',
                'NLS_NUMERIC_CHARACTERS' => '.,',
            ],
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
];
