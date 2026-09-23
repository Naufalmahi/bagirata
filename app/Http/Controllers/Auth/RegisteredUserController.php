<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $redirect = $request->input('redirect');

        if (! is_string($redirect) || $redirect === '' || ! str_starts_with($redirect, '/') || str_starts_with($redirect, '//')) {
            $redirect = null;
        }

        return $redirect
            ? redirect($redirect)->with('success', 'Akun kebikin, selamat datang!')
            : redirect()->route('dashboard')->with('success', 'Akun kebikin, selamat datang!');
    }
}
