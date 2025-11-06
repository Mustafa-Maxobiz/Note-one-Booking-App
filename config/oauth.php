<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OAuth2 Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OAuth2 providers including WordPress
    |
    */

    'wordpress' => [
        'client_id' => env('WP_OAUTH_CLIENT_ID'),
        'client_secret' => env('WP_OAUTH_CLIENT_SECRET'),
        'redirect_uri' => env('WP_OAUTH_REDIRECT_URI'),
        'server_url' => env('WP_OAUTH_SERVER'),
        'authorize_url' => env('WP_OAUTH_SERVER') . '/oauth/authorize',
        'token_url' => env('WP_OAUTH_SERVER') . '/oauth/token',
        'user_url' => env('WP_OAUTH_SERVER') . '/oauth/me',
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect_uri' => env('FACEBOOK_REDIRECT_URI'),
    ],
];
