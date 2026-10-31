<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'locale']);

        // Blank password = keep the existing one.
        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        $request->user()->update($data);

        return back()->with('flash', ['type' => 'success', 'message' => __('Profile updated.')]);
    }
}
