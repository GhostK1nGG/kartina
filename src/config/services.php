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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'base_url' => env('TELEGRAM_API_BASE_URL', 'https://api.telegram.org'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
    ],

    'public_contacts' => [
        'email' => env('PUBLIC_CONTACT_EMAIL', 'hello@example.com'),
        'telegram' => env('PUBLIC_CONTACT_TELEGRAM', 'https://t.me/example'),
        'vk' => env('PUBLIC_CONTACT_VK', 'https://vk.com/example'),
        'instagram' => env('PUBLIC_CONTACT_INSTAGRAM', 'https://instagram.com/example'),
    ],

];
