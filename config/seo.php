<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Schema.org Structured Data (Organization / LocalBusiness)
    |--------------------------------------------------------------------------
    |
    | Output on every page as JSON-LD. Set SEO_SCHEMA_TYPE to 'LocalBusiness'
    | (or a more specific sub-type like 'Restaurant') if you have a physical
    | location, otherwise leave as 'Organization'.
    |
    */

    'schema' => [
        'type' => env('SEO_SCHEMA_TYPE', 'Organization'),
        'name' => env('APP_NAME', ''),
        'url' => env('APP_URL', ''),
        'logo' => env('SEO_SCHEMA_LOGO', ''),
        'description' => env('SEO_SCHEMA_DESCRIPTION', ''),
        'phone' => env('BUSINESS_PHONE', ''),
        'email' => env('BUSINESS_EMAIL', ''),
        'address' => [
            'street' => env('BUSINESS_ADDRESS_STREET', ''),
            'city' => env('SEO_SCHEMA_ADDRESS_CITY', ''),
            'region' => env('SEO_SCHEMA_ADDRESS_REGION', ''),
            'postal_code' => env('SEO_SCHEMA_ADDRESS_POSTAL_CODE', ''),
            'country' => env('SEO_SCHEMA_ADDRESS_COUNTRY', 'US'),
        ],
        'hours' => env('BUSINESS_HOURS', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Open Graph Defaults
    |--------------------------------------------------------------------------
    |
    | Fallback image used when a post has no featured image and no custom OG
    | image. Should be an absolute URL to a 1200×630px image.
    |
    */

    'og' => [
        'default_image' => env('SEO_OG_DEFAULT_IMAGE', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitter / X Card
    |--------------------------------------------------------------------------
    */

    'twitter' => [
        'handle' => env('SEO_TWITTER_HANDLE', ''),
    ],

];
