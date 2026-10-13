<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request, ?User $user = null)
    {
        $profileUser = $user ?? $request->user();
        $isOwner = $request->user()?->id === $profileUser->id;

        $profileUser->loadCount(['groups', 'nongkrongSessions']);

        return view('profile.show', compact('profileUser', 'isOwner'));
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'alpha_dash', 'max:30', Rule::unique('users')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:300'],
            'phone' => ['nullable', 'string', 'max:25'],
            'bank_name' => ['nullable', 'string', 'max:50'],
            'bank_account' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_path'] = $path;
        }

        unset($validated['avatar']);

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profil kamu berhasil diperbarui!');
    }
}
