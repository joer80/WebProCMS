<?php

/*
|--------------------------------------------------------------------------
| Website Type Navigation
|--------------------------------------------------------------------------
|
| Defines the public navigation and footer links for each website type.
| Set WEBSITE_TYPE in your .env to activate the appropriate config.
|
| Each type has:
|   show_auth_links  - whether login/register/dashboard appear in the nav
|   footer_slugs     - slugs of menus rendered as footer columns (in order)
|   menus            - all menus; templates request them by slug
|
*/

return [

    'saas' => [
        'show_auth_links' => true,
        'footer_slugs' => ['legal'],
        'menus' => [
        [
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Features', 'route' => 'features', 'active' => true],
                ['label' => 'Pricing', 'route' => 'pricing', 'active' => true],
                ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                ['label' => 'Test', 'route' => 'test', 'active' => false],
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
