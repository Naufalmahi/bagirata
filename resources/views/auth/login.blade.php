@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="text-2xl font-extrabold text-slate-900">Masuk dulu yaa</h1>
            <p class="mt-1 text-sm text-slate-500">Biar catatan patungan nyambung ke akun lu.</p>

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf
                @if (request('redirect'))
                    <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                @endif

                <div>
                    <label class="mb-1 block text-sm font-semibold">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold">Password</label>
                    <input type="password" name="password" required
                        class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Ingat aku
                </label>

                <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white hover:bg-indigo-700">
                    Login
                </button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-500">
                Belum punya akun?
                <a href="{{ route('register', ['redirect' => request('redirect')]) }}" class="font-semibold text-indigo-600 hover:underline">Daftar gratis</a>
            </p>
        </div>
    </div>
@endsection