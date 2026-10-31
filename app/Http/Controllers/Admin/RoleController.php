<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Support\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    // Routes wrapped in `perm:roles` — permission-set management.

    public function index(Request $request): View
    {
        $roles = Role::withCount('users')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->orderByDesc('is_admin')->orderBy('name')
            ->paginate(15)->withQueryString();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('roles.form', [
            'role' => new Role(),
            'sections' => Permissions::sections(),
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        Role::create($request->safe()->only('name', 'slug', 'description', 'is_admin', 'permissions'));

        return redirect()->route('admin.roles.index')
            ->with('flash', ['type' => 'success', 'message' => 'Role created.']);
    }

    public function edit(Role $role): View
    {
        return view('roles.form', [
            'role' => $role,
            'sections' => Permissions::sections(),
        ]);
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        // Never let the last admin role be demoted — that would lock
        // every administrator out of the CMS.
        if ($role->is_admin && ! $request->boolean('is_admin')
            && Role::where('is_admin', true)->count() === 1) {
            return back()->with('flash', ['type' => 'error', 'message' => 'This is the last administrator role — it cannot be demoted.']);
        }

        $role->update($request->safe()->only('name', 'slug', 'description', 'is_admin', 'permissions'));

        return redirect()->route('admin.roles.index')
            ->with('flash', ['type' => 'success', 'message' => 'Role updated.']);
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_admin) {
            return back()->with('flash', ['type' => 'error', 'message' => 'Administrator roles cannot be deleted.']);
        }

        if ($role->users()->exists()) {
            return back()->with('flash', ['type' => 'error', 'message' => "This role is assigned to {$role->users()->count()} account(s) — reassign them first."]);
        }

        $role->delete();

        return back()->with('flash', ['type' => 'success', 'message' => 'Role deleted.']);
    }
}
