<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $minimumRole): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $required = collect(Role::cases())->firstWhere('name', ucfirst($minimumRole)) ?? Role::Standard;

        if (! $user->previewIsAtLeast($required)) {
            return redirect()->route('dashboard')
                ->with('error', __('You do not have permission to access that page.'));
        }

        return $next($request);
    }
}
