<?php

/*
|--------------------------------------------------------------------------
| Navigation
|--------------------------------------------------------------------------
|
| Defines the public navigation and footer links for the site.
| Edit these menus via the dashboard at /dashboard/menus.
|
| Keys:
|   show_auth_links         - whether login/register/dashboard appear in the nav
|   show_account_in_footer  - whether an Account column appears in the footer
|   footer_slugs            - slugs of menus rendered as footer columns (in order)
|   menus                   - all menus; templates request them by slug
|
*/

return [

    'show_auth_links' => true,
    'show_account_in_footer' => true,
    'footer_slugs' => ['footer-company', 'legal'],
    'menus' => [
        [
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
            ['label' => 'Contact', 'route' => 'contact', 'active' => true],
            ],
        ],
        [
            'slug' => 'footer-company',
            'label' => 'Company',
            'items' => [
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
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

];
