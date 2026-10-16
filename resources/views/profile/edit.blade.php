@extends('layouts.app')

@section('title', 'Kustomisasi Profil')

@section('content')
<div class="mx-auto max-w-xl p-4 sm:p-6">
  <div class="rounded-3xl border border-theme-border bg-theme-card p-6 sm:p-8 shadow-2xl">
    {{-- Header Form --}}
    <div class="flex items-center justify-between border-b border-theme-border/40 pb-5 mb-6">
      <div>
        <h1 class="text-2xl font-black text-theme-main tracking-tight">Kustomisasi Profil</h1>
        <p class="text-xs sm:text-sm text-theme-muted mt-0.5">
          Sesuaikan nama panggilan, bio, foto avatar, dan nomor rekening patungan kamu.
        </p>
      </div>
      <a href="{{ route('profile.show') }}" class="rounded-2xl border border-theme-border bg-theme-elevated px-4 py-2 text-xs font-bold text-theme-muted hover:text-theme-main hover:bg-theme-elevated/80 transition">
        Batal
      </a>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
      @csrf
      @method('PUT')

      {{-- Avatar Section --}}
      <div class="flex items-center gap-4 p-4 rounded-2xl bg-theme-elevated border border-theme-border">
        <div class="relative shrink-0">
          @if ($user->avatar_path)
            <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-2xl object-cover ring-2 ring-[#FF2A85]/40 shadow-sm">
          @else
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] text-2xl font-black text-white ring-2 ring-[#5D549C]/40 shadow-sm">
              {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
          @endif
        </div>
        <div class="flex-1 min-w-0">
          <label class="block text-xs font-bold uppercase tracking-wider text-theme-subtle mb-1" for="avatar">
            Foto Avatar / Profil
          </label>
          <input type="file" id="avatar" name="avatar" accept="image/*" class="block w-full text-xs text-theme-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#5D549C] file:text-white hover:file:bg-[#4e4587] cursor-pointer">
          <p class="text-[11px] text-theme-subtle mt-1">Format: JPG, PNG, WEBP (maks. 2MB)</p>
          @error('avatar')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      {{-- Nama Panggilan --}}
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-theme-subtle mb-1.5" for="name">
          Nama Panggilan / Display Name <span class="text-[#EB9998]">*</span>
        </label>
        <input class="w-full rounded-2xl border border-theme-border bg-theme-elevated px-4 py-2.5 text-sm text-theme-main placeholder:text-theme-subtle focus:border-[#5D549C] focus:outline-none focus:ring-1 focus:ring-[#5D549C]/30"
          type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="100">
        @error('name')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Username Handle --}}
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-theme-subtle mb-1.5" for="username">
          Username / Handle (seperti @username)
        </label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-theme-subtle">@</span>
          <input class="w-full rounded-2xl border border-theme-border bg-theme-elevated pl-8 pr-4 py-2.5 text-sm text-theme-main placeholder:text-theme-subtle focus:border-[#5D549C] focus:outline-none focus:ring-1 focus:ring-[#5D549C]/30"
            type="text" id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="namapengguna" maxlength="30">
        </div>
        <p class="text-[11px] text-theme-subtle mt-1">Hanya huruf, angka, tanda strip (-) dan garis bawah (_).</p>
        @error('username')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Bio Profil --}}
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-theme-subtle mb-1.5" for="bio">
          Bio Profil (Instagram & Discord Style)
        </label>
        <textarea class="w-full rounded-2xl border border-theme-border bg-theme-elevated px-4 py-2.5 text-sm text-theme-main placeholder:text-theme-subtle focus:border-[#5D549C] focus:outline-none focus:ring-1 focus:ring-[#5D549C]/30"
          id="bio" name="bio" rows="3" maxlength="300" placeholder="Tuliskan bio singkat kamu, moto nongkrong, dll...">{{ old('bio', $user->bio) }}</textarea>
        @error('bio')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Nomor WhatsApp / Telepon --}}
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-theme-subtle mb-1.5" for="phone">
          Nomor WhatsApp / HP
        </label>
        <input class="w-full rounded-2xl border border-theme-border bg-theme-elevated px-4 py-2.5 text-sm text-theme-main placeholder:text-theme-subtle focus:border-[#5D549C] focus:outline-none focus:ring-1 focus:ring-[#5D549C]/30"
          type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="081234567890">
        @error('phone')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Rekening & E-Wallet --}}
      <div class="rounded-2xl border border-theme-border bg-theme-elevated p-4 space-y-3">
        <p class="text-xs font-bold uppercase tracking-wider text-theme-subtle">Rekening Tujuan Patungan</p>

        <div class="grid gap-3 sm:grid-cols-2">
          <div>
            <label class="block text-xs font-medium text-theme-muted mb-1" for="bank_name">
              Nama Bank / E-Wallet
            </label>
            <input class="w-full rounded-xl border border-theme-border bg-theme-card px-3 py-2 text-xs text-theme-main placeholder:text-theme-subtle focus:border-[#5D549C] focus:outline-none focus:ring-1 focus:ring-[#5D549C]/30"
              type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" placeholder="BCA / Mandiri / GoPay / DANA">
          </div>

          <div>
            <label class="block text-xs font-medium text-theme-muted mb-1" for="bank_account">
              Nomor Rekening / No. E-Wallet
            </label>
            <input class="w-full rounded-xl border border-theme-border bg-theme-card px-3 py-2 text-xs text-theme-main placeholder:text-theme-subtle focus:border-[#5D549C] focus:outline-none focus:ring-1 focus:ring-[#5D549C]/30"
              type="text" id="bank_account" name="bank_account" value="{{ old('bank_account', $user->bank_account) }}" placeholder="1234567890">
          </div>
        </div>
      </div>

      {{-- Tombol Aksi --}}
      <div class="pt-2 flex items-center justify-end gap-3">
        <a href="{{ route('profile.show') }}" class="rounded-2xl border border-theme-border bg-theme-elevated px-5 py-3 text-sm font-bold text-theme-muted hover:text-theme-main hover:bg-theme-elevated/80 transition">
          Batal
        </a>
        <button type="submit" class="pushable">
          <span class="shadow"></span>
          <span class="edge"></span>
          <span class="front">Simpan Perubahan</span>
        </button>
      </div>
    </form>

    {{-- Zona Keluar Akun (Logout Section) --}}
    <div class="mt-8 pt-6 border-t border-theme-border/40 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-bold text-rose-500">Keluar dari Akun</h3>
        <p class="text-xs text-theme-subtle mt-0.5">Sesi akun kamu di perangkat ini akan diakhiri.</p>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-2 text-xs font-bold text-rose-500 hover:bg-rose-500 hover:text-white transition">
          <i class="fa-solid fa-right-from-bracket mr-1.5"></i>
          Keluar Akun
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
