@extends('layouts.app')

@section('title', 'Kustomisasi Profil')

@section('content')
<div class="mx-auto max-w-xl">
    <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-200/50">
        {{-- Header Form --}}
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kustomisasi Profil</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Sesuaikan nama panggilan, bio, foto avatar, dan nomor rekening patungan kamu.
                </p>
            </div>
            <a href="{{ route('profile.show') }}" class="rounded-xl border border-slate-200 px-3.5 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                Batal
            </a>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Avatar Section --}}
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="relative shrink-0">
                    @if ($user->avatar_path)
                        <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-2xl object-cover ring-2 ring-indigo-200">
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-2xl font-black text-white ring-2 ring-indigo-200">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1" for="avatar">
                        Foto Avatar / Profil
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP (maks. 2MB)</p>
                    @error('avatar')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Nama Panggilan --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="name">
                    Nama Panggilan / Display Name <span class="text-rose-500">*</span>
                </label>
                <input class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="100">
                @error('name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Username Handle --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="username">
                    Username / Handle (seperti @username)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-400">@</span>
                    <input class="w-full rounded-2xl border border-slate-300 bg-white pl-8 pr-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        type="text" id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="namapengguna" maxlength="30">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Hanya huruf, angka, tanda strip (-) dan garis bawah (_).</p>
                @error('username')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Bio Profil --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="bio">
                    Bio Profil (Instagram & Discord Style)
                </label>
                <textarea class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    id="bio" name="bio" rows="3" maxlength="300" placeholder="Tuliskan bio singkat kamu, moto nongkrong, dll...">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Nomor WhatsApp / Telepon --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="phone">
                    Nomor WhatsApp / HP
                </label>
                <input class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="081234567890">
                @error('phone')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Rekening & E-Wallet --}}
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50/40 p-4 space-y-3">
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-900">Rekening Tujuan Patungan</p>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1" for="bank_name">
                            Nama Bank / E-Wallet
                        </label>
                        <input class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" placeholder="BCA / Mandiri / GoPay / DANA">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1" for="bank_account">
                            Nomor Rekening / No. E-Wallet
                        </label>
                        <input class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            type="text" id="bank_account" name="bank_account" value="{{ old('bank_account', $user->bank_account) }}" placeholder="1234567890">
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('profile.show') }}" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="rounded-2xl bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 px-6 py-3 text-sm font-bold text-white shadow-md shadow-indigo-300/40 hover:from-purple-700 hover:to-blue-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
