<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Brand;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    // Routes wrapped in `perm:staff` — accounts need the permission
    // plus an admin role to touch another admin.

    public function index(Request $request): View
    {
        $users = User::with('brands', 'role')
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($qq) => $qq
                ->where('name', 'like', "%{$request->q}%")
                ->orWhere('email', 'like', "%{$request->q}%")))
            ->when($request->filled('role_id'), fn ($q) => $q->where('role_id', $request->integer('role_id')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        return view('users.form', [
            'user' => new User(),
            'brands' => Brand::orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create($request->safe()->except('brands'));
        $this->syncBrands($user, $request);

        return redirect()->route('admin.users.index')
            ->with('flash', ['type' => 'success', 'message' => 'User created.']);
    }

    public function edit(User $user): View
    {
        $user->load('brands');

        return view('users.form', [
            'user' => $user,
            'brands' => Brand::orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->except(['brands', 'password']);

        // Blank password on edit = keep the existing one.
        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        $user->update($data);
        $this->syncBrands($user, $request);

        return redirect()->route('admin.users.index')
            ->with('flash', ['type' => 'success', 'message' => 'User updated.']);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->with('flash', ['type' => 'error', 'message' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return back()->with('flash', ['type' => 'success', 'message' => 'User deleted.']);
    }

    /**
     * Admin accounts bypass brand restrictions — keep their
     * assignment empty so semantics stay honest.
     */
    private function syncBrands(User $user, UserRequest $request): void
    {
        $brandIds = $user->isAdmin() ? [] : $request->input('brands', []);
        $user->brands()->sync($brandIds);
    }
}
