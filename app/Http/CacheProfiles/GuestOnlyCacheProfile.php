<?php

namespace App\Http\CacheProfiles;

use Illuminate\Http\Request;
use Spatie\ResponseCache\CacheProfiles\CacheAllSuccessfulGetRequests;

class GuestOnlyCacheProfile extends CacheAllSuccessfulGetRequests
{
    public function shouldCacheRequest(Request $request): bool
    {
        if ($request->user()) {
            return false;
        }

        return parent::shouldCacheRequest($request);
    }

    public function useCacheNameSuffix(Request $request): string
    {
        return '';
    }
}
