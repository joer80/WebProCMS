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

    'phone' => env('BUSINESS_PHONE', '+1 (555) 123-4567'),

    'email' => env('BUSINESS_EMAIL', 'hello@getrows.com'),

    'address' => [
        'street' => env('BUSINESS_ADDRESS_STREET', '123 Maple Street, Suite 400'),
        'city_state_zip' => env('BUSINESS_ADDRESS_CITY_STATE_ZIP', 'Austin, TX 78701'),
    ],

    'hours' => env('BUSINESS_HOURS', 'Monday – Friday, 9am – 5pm CT'),

];
