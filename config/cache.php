<?php

return [

    'stores' => [

        'redis' => [
            'driver' => 'redis',
            'client' => 'predis', // Use Predis client
            'cluster' => env('REDIS_CLUSTER', false), // Enable Redis clustering if needed

            // Cluster configuration (optional)
            'clusters' => [
                'default' => [
                    'scheme' => env('REDIS_SCHEME', 'tcp'),
                    'host' => env('REDIS_HOST', 'localhost'),
                    'password' => env('REDIS_PASSWORD', null),
                    'port' => env('REDIS_PORT', 6379),
                    'database' => env('REDIS_DATABASE', 0),
                ],
            ],

            // Additional options
            'options' => [
                'cluster' => 'redis', // This is typically used with Redis clusters
            ],

            // Parameters for connection
            'parameters' => [
                'password' => env('REDIS_PASSWORD', null),
                'scheme' => env('REDIS_SCHEME', 'tcp'), // Default to 'tcp' scheme
                'ssl' => [
                    'verify_peer' => false, // Disable SSL verification (if using SSL)
                ],
            ],
        ],

    ],

];
