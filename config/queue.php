<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | The Laravel queue API supports a variety of back-ends via an unified
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "null", "sync", "database", "beanstalkd", "sqs", "redis"
    |
    */

    'default' => env('QUEUE_DRIVER', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'default' => [
            'driver'     => 'redis',
            'connection' => 'default',
            'queue'      => 'default',
            'expire'     => 60,
        ],

        // For channel integration jobs.
        'integrations' => [
            'driver'     => 'redis',
            'connection' => 'default',
            'queue'      => 'integrations',
            'expire'     => 60 * 60 * 6, // 6 hours
        ],

        // For channel integration jobs.
        'integration_amazon' => [
            'driver'     => 'redis',
            'connection' => 'default',
            'queue'      => 'integration_amazon',
            'expire'     => 60 * 60 * 24, // 24 hours
        ],

        // For asynchronous photo handling.
        'photos' => [
            'driver'     => 'redis',
            'connection' => 'default',
            'queue'      => 'photos',
            'expire'     => 30,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host'   => 'localhost',
            'queue'  => 'default',
            'ttr'    => 60,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key'    => 'your-public-key',
            'secret' => 'your-secret-key',
            'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
            'queue'  => 'your-queue-name',
            'region' => 'us-east-1',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table'    => 'failed_jobs',
    ],

];
