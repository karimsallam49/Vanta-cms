<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    // Routes wrapped in `admin` middleware — only admins manage staff.

    public function index(): View
    {
        $users = User::with('brands')->orderBy('name')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.form', [
            'user' => new User(),
            'brands' => Brand::orderBy('name')->get(),
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
