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
|   nav              - primary navigation items (always shown)
|   show_auth_links  - whether login/register/dashboard appear in the nav
|   footer_company   - links shown in the footer "Company" column
|
*/

return [

    'saas' => [
        'nav' => [
            ['label' => 'Features', 'route' => 'features', 'active' => true],
            ['label' => 'Pricing', 'route' => 'pricing', 'active' => true],
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
            ['label' => 'About', 'route' => 'about', 'active' => true],
            ['label' => 'Test', 'route' => 'test', 'active' => true],
        ],
        'show_auth_links' => true,
        'footer_company' => [
            ['label' => 'Features', 'route' => 'features', 'active' => true],
            ['label' => 'Pricing', 'route' => 'pricing', 'active' => true],
            ['label' => 'About', 'route' => 'about', 'active' => true],
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
            ['label' => 'Contact', 'route' => 'contact', 'active' => true],
        ],
    ],

    'service' => [
        'nav' => [
            ['label' => 'Services', 'route' => 'services', 'active' => true],
            ['label' => 'Locations', 'route' => 'locations', 'active' => true],
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
            ['label' => 'Contact Us', 'route' => 'contact', 'active' => true],
        ],
        'show_auth_links' => false,
        'footer_company' => [
            ['label' => 'Services', 'route' => 'services', 'active' => true],
            ['label' => 'Locations', 'route' => 'locations', 'active' => true],
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
            ['label' => 'Contact', 'route' => 'contact', 'active' => true],
        ],
    ],

    'ecommerce' => [
        'nav' => [
            ['label' => 'Products', 'route' => 'products', 'active' => true],
            ['label' => 'About Us', 'route' => 'about', 'active' => true],
            ['label' => 'Contact Us', 'route' => 'contact', 'active' => true],
        ],
        'show_auth_links' => true,
        'footer_company' => [
            ['label' => 'Products', 'route' => 'products', 'active' => true],
            ['label' => 'About Us', 'route' => 'about', 'active' => true],
            ['label' => 'Contact', 'route' => 'contact', 'active' => true],
        ],
    ],

    'law' => [
        'nav' => [
            ['label' => 'Practice Areas', 'route' => 'practice-areas', 'active' => true],
            ['label' => 'About Us', 'route' => 'about', 'active' => true],
            ['label' => 'Contact Us', 'route' => 'contact', 'active' => true],
        ],
        'show_auth_links' => false,
        'footer_company' => [
            ['label' => 'Practice Areas', 'route' => 'practice-areas', 'active' => true],
            ['label' => 'About Us', 'route' => 'about', 'active' => true],
            ['label' => 'Contact', 'route' => 'contact', 'active' => true],
        ],
    ],

    'nonprofit' => [
        'nav' => [
            ['label' => 'About', 'route' => 'about', 'active' => true],
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
            ['label' => 'Donate', 'route' => 'donate', 'active' => true],
            ['label' => 'Volunteer', 'route' => 'volunteer', 'active' => true],
        ],
        'show_auth_links' => false,
        'footer_company' => [
            ['label' => 'About', 'route' => 'about', 'active' => true],
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
            ['label' => 'Donate', 'route' => 'donate', 'active' => true],
            ['label' => 'Volunteer', 'route' => 'volunteer', 'active' => true],
            ['label' => 'Contact', 'route' => 'contact', 'active' => true],
        ],
    ],

    'healthcare' => [
        'nav' => [
            ['label' => 'Patients', 'route' => 'patients', 'active' => true],
            ['label' => 'Employers', 'route' => 'employers', 'active' => true],
            ['label' => 'Locations', 'route' => 'locations', 'active' => true],
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
            ['label' => 'Careers', 'route' => 'careers', 'active' => true],
        ],
        'show_auth_links' => false,
        'footer_company' => [
            ['label' => 'Patients', 'route' => 'patients', 'active' => true],
            ['label' => 'Employers', 'route' => 'employers', 'active' => true],
            ['label' => 'Locations', 'route' => 'locations', 'active' => true],
            ['label' => 'Careers', 'route' => 'careers', 'active' => true],
            ['label' => 'Contact', 'route' => 'contact', 'active' => true],
        ],
    ],

    'custom' => [
        'nav' => [
            ['label' => 'About', 'route' => 'about', 'active' => true],
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
            ['label' => 'Contact', 'route' => 'contact', 'active' => true],
        ],
        'show_auth_links' => false,
        'footer_company' => [
            ['label' => 'About', 'route' => 'about', 'active' => true],
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
            ['label' => 'Contact', 'route' => 'contact', 'active' => true],
        ],
    ],

];
