<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rebuild Assets Locally
    |--------------------------------------------------------------------------
    |
    | When enabled, the RebuildAssets job fires in non-production environments
    | when a page is saved with class changes. Runs via defer() so the save
    | response is instant. Defaults to true so composer run dev is not required.
    |
    */
    'rebuild_assets_locally' => env('REBUILD_ASSETS_LOCALLY', true),

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

    /*
    |--------------------------------------------------------------------------
    | Releases API URL
    |--------------------------------------------------------------------------
    |
    | The URL to check for available CMS updates. Should return JSON with a
    | "version" key (e.g. "1.0.1") and optional "notes" key. Also supports
    | GitHub Releases API format using "tag_name" (e.g. "v1.0.1").
    |
    | Example: https://api.github.com/repos/your-org/webprocms/releases/latest
    |
    */
    'releases_api_url' => env('CMS_RELEASES_API_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Git Branch
    |--------------------------------------------------------------------------
    |
    | The git branch to pull from when running a CMS update.
    |
    */
    'git_branch' => env('CMS_GIT_BRANCH', 'main'),

    /*
    |--------------------------------------------------------------------------
    | Admin Email
    |--------------------------------------------------------------------------
    |
    | The email address for the initial super admin account created during
    | the first migration. Set BUSINESS_ADMIN_EMAIL in your .env file.
    |
    */
    'admin_email' => env('BUSINESS_ADMIN_EMAIL', 'root@localhost'),

];
