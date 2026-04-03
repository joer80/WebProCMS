<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PreviewRoleController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:standard,manager,admin'],
        ]);

        $user = $request->user();

        if (! $user || $user->role !== Role::Super) {
            abort(403);
        }

        session()->put('preview_role', $validated['role']);

        return redirect()->back();
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->role !== Role::Super) {
            abort(403);
        }

        session()->forget('preview_role');

        return redirect()->back();
    }
}
