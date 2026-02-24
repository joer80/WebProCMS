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
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
            ['label' => 'Blog', 'route' => 'blog.index'],
            ['label' => 'About', 'route' => 'about'],
            ['label' => 'Test', 'route' => 'test'],
        ],
        'show_auth_links' => true,
        'footer_company' => [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
            ['label' => 'About', 'route' => 'about'],
            ['label' => 'Blog', 'route' => 'blog.index'],
            ['label' => 'Contact', 'route' => 'contact'],
        ],
    ],

    'service' => [
        'nav' => [
            ['label' => 'Services', 'route' => 'services'],
            ['label' => 'Locations', 'route' => 'locations'],
            ['label' => 'Blog', 'route' => 'blog.index'],
            ['label' => 'Contact Us', 'route' => 'contact'],
        ],
        'show_auth_links' => false,
        'footer_company' => [
            ['label' => 'Services', 'route' => 'services'],
            ['label' => 'Locations', 'route' => 'locations'],
            ['label' => 'Blog', 'route' => 'blog.index'],
            ['label' => 'Contact', 'route' => 'contact'],
        ],
    ],

    'ecommerce' => [
        'nav' => [
            ['label' => 'Products', 'route' => 'products'],
            ['label' => 'About Us', 'route' => 'about'],
            ['label' => 'Contact Us', 'route' => 'contact'],
        ],
        'show_auth_links' => true,
        'footer_company' => [
            ['label' => 'Products', 'route' => 'products'],
            ['label' => 'About Us', 'route' => 'about'],
            ['label' => 'Contact', 'route' => 'contact'],
        ],
    ],

    'law' => [
        'nav' => [
            ['label' => 'Practice Areas', 'route' => 'practice-areas'],
            ['label' => 'About Us', 'route' => 'about'],
            ['label' => 'Contact Us', 'route' => 'contact'],
        ],
        'show_auth_links' => false,
        'footer_company' => [
            ['label' => 'Practice Areas', 'route' => 'practice-areas'],
            ['label' => 'About Us', 'route' => 'about'],
            ['label' => 'Contact', 'route' => 'contact'],
        ],
    ],

    'nonprofit' => [
        'nav' => [
            ['label' => 'About', 'route' => 'about'],
            ['label' => 'Blog', 'route' => 'blog.index'],
            ['label' => 'Donate', 'route' => 'donate'],
            ['label' => 'Volunteer', 'route' => 'volunteer'],
        ],
        'show_auth_links' => false,
        'footer_company' => [
            ['label' => 'About', 'route' => 'about'],
            ['label' => 'Blog', 'route' => 'blog.index'],
            ['label' => 'Donate', 'route' => 'donate'],
            ['label' => 'Volunteer', 'route' => 'volunteer'],
            ['label' => 'Contact', 'route' => 'contact'],
        ],
    ],

    'healthcare' => [
        'nav' => [
            ['label' => 'Patients', 'route' => 'patients'],
            ['label' => 'Employers', 'route' => 'employers'],
            ['label' => 'Locations', 'route' => 'locations'],
            ['label' => 'Blog', 'route' => 'blog.index'],
            ['label' => 'Careers', 'route' => 'careers'],
        ],
        'show_auth_links' => false,
        'footer_company' => [
            ['label' => 'Patients', 'route' => 'patients'],
            ['label' => 'Employers', 'route' => 'employers'],
            ['label' => 'Locations', 'route' => 'locations'],
            ['label' => 'Careers', 'route' => 'careers'],
            ['label' => 'Contact', 'route' => 'contact'],
        ],
    ],

    'custom' => [
        'nav' => [
            ['label' => 'About', 'route' => 'about'],
            ['label' => 'Blog', 'route' => 'blog.index'],
            ['label' => 'Contact', 'route' => 'contact'],
        ],
        'show_auth_links' => false,
        'footer_company' => [
            ['label' => 'About', 'route' => 'about'],
            ['label' => 'Blog', 'route' => 'blog.index'],
            ['label' => 'Contact', 'route' => 'contact'],
        ],
    ],

];
