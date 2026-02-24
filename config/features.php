<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Toggle application features on or off via environment variables.
    | Set the corresponding variable to true/false in your .env file.
    |
    */

    'dashboard' => env('DASHBOARD_ENABLED', true),

    'website_type' => env('WEBSITE_TYPE', 'saas'),

];
