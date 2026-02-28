<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Schema.org Structured Data (Organization / LocalBusiness)
    |--------------------------------------------------------------------------
    |
    | Output on every page as JSON-LD. Set type to 'LocalBusiness'
    | (or a more specific sub-type like 'Restaurant') if you have a physical
    | location, otherwise leave as 'Organization'.
    |
    */

    'schema' => [
        'type' => 'Organization',
        'name' => env('APP_NAME', ''),
        'url' => env('APP_URL', ''),
        'logo' => '',
        'description' => 'Business Name helps you manage your web content efficiently.',
        'phone' => config('business.phone', ''),
        'email' => config('business.email', ''),
        'address' => [
            'street' => config('business.address_street', ''),
            'city' => 'Austin',
            'region' => 'TX',
            'postal_code' => '78701',
            'country' => 'US',
        ],
        'hours' => config('business.hours', ''),
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
        'default_image' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitter / X Card
    |--------------------------------------------------------------------------
    */

    'twitter' => [
        'handle' => '@businessname',
    ],

];
