<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Business Contact Information
    |--------------------------------------------------------------------------
    |
    | These values are used across the application wherever business contact
    | details are displayed. Update the corresponding environment variables
    | to change them without modifying any code.
    |
    */

    'phone' => env('BUSINESS_PHONE'),

    'email' => env('BUSINESS_EMAIL'),

    'address' => [
        'street' => env('BUSINESS_ADDRESS_STREET'),
        'city_state_zip' => env('BUSINESS_ADDRESS_CITY_STATE_ZIP'),
    ],

    'hours' => env('BUSINESS_HOURS'),

];
