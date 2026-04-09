<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bakong Account ID
    |--------------------------------------------------------------------------
    |
    | Your Bakong account ID. This is the account that will receive payments.
    | Find it in the Bakong app: Profile > My QR (e.g. 0123456789@nbcq)
    |
    */
    'account_id' => env('BAKONG_ACCOUNT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Merchant Name
    |--------------------------------------------------------------------------
    |
    | The merchant name displayed on the QR code and in the Bakong app
    | when a customer scans the code.
    |
    */
    'merchant_name' => env('BAKONG_MERCHANT_NAME', 'E-Kampot Shop'),

    /*
    |--------------------------------------------------------------------------
    | Merchant City
    |--------------------------------------------------------------------------
    |
    | The city of the merchant, displayed on the KHQR code.
    |
    */
    'merchant_city' => env('BAKONG_MERCHANT_CITY', 'KAMPOT'),

    /*
    |--------------------------------------------------------------------------
    | Acquiring Bank
    |--------------------------------------------------------------------------
    |
    | The acquiring bank name (optional, used for merchant QR type).
    |
    */
    'acquiring_bank' => env('BAKONG_ACQUIRING_BANK', ''),

    /*
    |--------------------------------------------------------------------------
    | API Token
    |--------------------------------------------------------------------------
    |
    | A valid Bakong API token is required to check transaction status.
    | Register at: https://api-bakong.nbc.gov.kh/register
    | Tokens expire every 90 days.
    |
    */
    'api_token' => env('BAKONG_API_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | API Environment
    |--------------------------------------------------------------------------
    |
    | Set to 'sandbox' for testing or 'production' for live payments.
    | sandbox  => https://sit-api-bakong.nbc.gov.kh
    | production => https://api-bakong.nbc.gov.kh
    |
    */
    'api_env' => env('BAKONG_API_ENV', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | QR Expiration (minutes)
    |--------------------------------------------------------------------------
    |
    | How long the generated QR code is valid before it expires.
    |
    */
    'qr_expiration_minutes' => env('BAKONG_QR_EXPIRATION', 15),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Default currency for QR payments. 'USD' or 'KHR'.
    |
    */
    'currency' => env('BAKONG_CURRENCY', 'USD'),

];
