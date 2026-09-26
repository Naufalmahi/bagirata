@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="card p-6 sm:p-8">
            <h1 class="page-title text-2xl sm:text-2xl">Bikin akun</h1>
            <p class="muted mt-1">Santai, cuma butuh 3 data doang.</p>

            <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
                @csrf
                @if (request('redirect'))
                    <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                @endif

                <div>
                    <label class="label" for="name">Nama panggilan</label>
                    <input class="input" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label" for="email">Email</label>
                    <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label" for="password">Password</label>
                    <input class="input" id="password" type="password" name="password" required>
                    @error('password')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label" for="password_confirmation">Ulangi password</label>
                    <input class="input" id="password_confirmation" type="password" name="password_confirmation" required>
                </div>

                <button class="btn btn-primary btn-lg w-full">Daftar</button>
            </form>

            <p class="mt-5 text-center text-sm text-ink-muted">
                Udah punya akun?
                <a href="{{ route('login', ['redirect' => request('redirect')]) }}" class="font-semibold text-brand-text hover:underline">Masuk</a>
            </p>
        </div>
    </div>
@endsection
