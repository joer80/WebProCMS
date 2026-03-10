<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rebuild Assets Locally
    |--------------------------------------------------------------------------
    |
    | When enabled, the RebuildAssets job will also fire in non-production
    | environments when a page is saved with class changes. Useful for local
    | development when you are not running `composer run dev`.
    |
    */
    'rebuild_assets_locally' => env('REBUILD_ASSETS_LOCALLY', false),

    /*
    |--------------------------------------------------------------------------
    | NPM Path
    |--------------------------------------------------------------------------
    |
    | Full path to the npm binary. On production servers npm is typically in
    | PATH and the default works fine. Locally with Herd/nvm, PHP spawns
    | processes without the shell PATH so the full path is required.
    |
    | Find yours with: which npm
    |
    */
    'npm_path' => env('NPM_PATH', 'npm'),

];
