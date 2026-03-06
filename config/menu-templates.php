<?php

/*
|--------------------------------------------------------------------------
| Navigation Menu Templates
|--------------------------------------------------------------------------
|
| Suggested navigation menus for each website type. These are read-only
| templates displayed in the Design Library "Menus" tab. Users can import
| any template into their active navigation via the Design Library.
|
| The app never writes to this file.
|
*/

return [

    'saas' => [
        'show_auth_links' => true,
        'show_account_in_footer' => true,
        'footer_slugs' => ['footer-company', 'legal'],
        'menus' => [
            [
                'slug' => 'main-navigation',
                'label' => 'Main Navigation',
                'items' => [
                    ['label' => 'Features', 'route' => 'features', 'active' => true],
                    ['label' => 'Pricing', 'route' => 'pricing', 'active' => true],
                    ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                    ['label' => 'About', 'route' => 'about', 'active' => true],
                ],
            ],
            [
                'slug' => 'footer-company',
                'label' => 'Company',
                'items' => [
                    ['label' => 'Features', 'route' => 'features', 'active' => true],
                    ['label' => 'Pricing', 'route' => 'pricing', 'active' => true],
                    ['label' => 'About', 'route' => 'about', 'active' => true],
                    ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                    ['label' => 'Contact', 'route' => 'contact', 'active' => true],
                ],
            ],
            [
                'slug' => 'legal',
                'label' => 'Legal',
                'items' => [
                    ['label' => 'Terms of Service', 'url' => '#', 'active' => true],
                    ['label' => 'Privacy Policy', 'url' => '#', 'active' => true],
                ],
            ],
        ],
    ],

    'service' => [
        'show_auth_links' => false,
        'show_account_in_footer' => true,
        'footer_slugs' => ['footer-company'],
        'menus' => [
            [
                'slug' => 'main-navigation',
                'label' => 'Main Navigation',
                'items' => [
                    ['label' => 'Services', 'route' => 'services', 'active' => true],
                    ['label' => 'Locations', 'route' => 'locations', 'active' => true],
                    ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                    ['label' => 'Contact Us', 'route' => 'contact', 'active' => true],
                ],
            ],
            [
                'slug' => 'footer-company',
                'label' => 'Company',
                'items' => [
                    ['label' => 'Services', 'route' => 'services', 'active' => true],
                    ['label' => 'Locations', 'route' => 'locations', 'active' => true],
                    ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                    ['label' => 'Contact', 'route' => 'contact', 'active' => true],
                ],
            ],
        ],
    ],

    'ecommerce' => [
        'show_auth_links' => true,
        'show_account_in_footer' => true,
        'footer_slugs' => ['footer-company'],
        'menus' => [
            [
                'slug' => 'main-navigation',
                'label' => 'Main Navigation',
                'items' => [
                    ['label' => 'Products', 'route' => 'products', 'active' => true],
                    ['label' => 'About Us', 'route' => 'about', 'active' => true],
                    ['label' => 'Contact Us', 'route' => 'contact', 'active' => true],
                ],
            ],
            [
                'slug' => 'footer-company',
                'label' => 'Company',
                'items' => [
                    ['label' => 'Products', 'route' => 'products', 'active' => true],
                    ['label' => 'About Us', 'route' => 'about', 'active' => true],
                    ['label' => 'Contact', 'route' => 'contact', 'active' => true],
                ],
            ],
        ],
    ],

    'law' => [
        'show_auth_links' => false,
        'show_account_in_footer' => true,
        'footer_slugs' => ['footer-company'],
        'menus' => [
            [
                'slug' => 'main-navigation',
                'label' => 'Main Navigation',
                'items' => [
                    ['label' => 'Practice Areas', 'route' => 'practice-areas', 'active' => true],
                    ['label' => 'About Us', 'route' => 'about', 'active' => true],
                    ['label' => 'Contact Us', 'route' => 'contact', 'active' => true],
                ],
            ],
            [
                'slug' => 'footer-company',
                'label' => 'Company',
                'items' => [
                    ['label' => 'Practice Areas', 'route' => 'practice-areas', 'active' => true],
                    ['label' => 'About Us', 'route' => 'about', 'active' => true],
                    ['label' => 'Contact', 'route' => 'contact', 'active' => true],
                ],
            ],
        ],
    ],

    'nonprofit' => [
        'show_auth_links' => false,
        'show_account_in_footer' => true,
        'footer_slugs' => ['footer-company'],
        'menus' => [
            [
                'slug' => 'main-navigation',
                'label' => 'Main Navigation',
                'items' => [
                    ['label' => 'About', 'route' => 'about', 'active' => true],
                    ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                    ['label' => 'Donate', 'route' => 'donate', 'active' => true],
                    ['label' => 'Volunteer', 'route' => 'volunteer', 'active' => true],
                ],
            ],
            [
                'slug' => 'footer-company',
                'label' => 'Company',
                'items' => [
                    ['label' => 'About', 'route' => 'about', 'active' => true],
                    ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                    ['label' => 'Donate', 'route' => 'donate', 'active' => true],
                    ['label' => 'Volunteer', 'route' => 'volunteer', 'active' => true],
                    ['label' => 'Contact', 'route' => 'contact', 'active' => true],
                ],
            ],
        ],
    ],

    'healthcare' => [
        'show_auth_links' => false,
        'show_account_in_footer' => true,
        'footer_slugs' => ['footer-company'],
        'menus' => [
            [
                'slug' => 'main-navigation',
                'label' => 'Main Navigation',
                'items' => [
                    ['label' => 'Patients', 'route' => 'patients', 'active' => true],
                    ['label' => 'Employers', 'route' => 'employers', 'active' => true],
                    ['label' => 'Locations', 'route' => 'locations', 'active' => true],
                    ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                    ['label' => 'Careers', 'route' => 'careers', 'active' => true],
                ],
            ],
            [
                'slug' => 'footer-company',
                'label' => 'Company',
                'items' => [
                    ['label' => 'Patients', 'route' => 'patients', 'active' => true],
                    ['label' => 'Employers', 'route' => 'employers', 'active' => true],
                    ['label' => 'Locations', 'route' => 'locations', 'active' => true],
                    ['label' => 'Careers', 'route' => 'careers', 'active' => true],
                    ['label' => 'Contact', 'route' => 'contact', 'active' => true],
                ],
            ],
        ],
    ],

    'custom' => [
        'show_auth_links' => false,
        'show_account_in_footer' => true,
        'footer_slugs' => ['footer-company'],
        'menus' => [
            [
                'slug' => 'main-navigation',
                'label' => 'Main Navigation',
                'items' => [
                    ['label' => 'About', 'route' => 'about', 'active' => true],
                    ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                    ['label' => 'Contact', 'route' => 'contact', 'active' => true],
                ],
            ],
            [
                'slug' => 'footer-company',
                'label' => 'Company',
                'items' => [
                    ['label' => 'About', 'route' => 'about', 'active' => true],
                    ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                    ['label' => 'Contact', 'route' => 'contact', 'active' => true],
                ],
            ],
        ],
    ],

];
