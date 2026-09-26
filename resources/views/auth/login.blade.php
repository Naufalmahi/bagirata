@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="card p-6 sm:p-8">
            <h1 class="page-title text-2xl sm:text-2xl">Masuk dulu yaa</h1>
            <p class="muted mt-1">Biar catatan patungan nyambung ke akun lu.</p>

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf
                @if (request('redirect'))
                    <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                @endif

                <div>
                    <label class="label" for="email">Email</label>
                    <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label" for="password">Password</label>
                    <input class="input" id="password" type="password" name="password" required>
                    @error('password')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-ink-muted">
                    <input class="checkbox" type="checkbox" name="remember" value="1">
                    Ingat aku
                </label>

                <button class="btn btn-primary btn-lg w-full">Login</button>
            </form>

            <p class="mt-5 text-center text-sm text-ink-muted">
                Belum punya akun?
                <a href="{{ route('register', ['redirect' => request('redirect')]) }}" class="font-semibold text-brand-text hover:underline">Daftar gratis</a>
            </p>
        </div>
    </div>
@endsection
