<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'map_api_key' => env('MAP_API_KEY', ''),

    'tax_id' => env('TAX_ID', '0115535008949'),
    'suffix' => env('SUFFIX', '05'),

    'account_api' => [
        [
            'username' => 'carpark',
            'password' => env('API_CAR_PARK_PASSWORD', 'password'),
            'abilities' => ['car-park'],
            'token_name' => 'CAR-PARK-API',
            'status' => STATUS_ACTIVE
        ],
        [
            'username' => 'application',
            'password' => env('API_TEAM_DOS_PASSWORD', 'password'),
            'abilities' => ['customer-application'],
            'token_name' => 'CUSTOMER-APPLICATION-API',
            'status' => STATUS_ACTIVE
        ],
    ],

    'gps' => [
        'endpoint' => env('GPS_ENDPOINT', 'https://kratosapigateway.azure-api.net'),
        'key' => env('GPS_KEY', ''),
    ],

    '2c2p' => [
        'mid' => env('2C2P_MID', ''),
        'secret_key' => env('2C2P_SECRET_KEY', ''),
        'qp_endpoint' => env('2C2P_QP_ENDPOINT', ''),
        'payment_endpoint' => env('2C2P_PAYMENT_ENDPOINT', ''),
        'version' => env('2C2P_VERSION', ''),
    ]
];
