@extends('layouts.app')

@section('title', 'Bikin Circle Baru · BagiRata')

@section('content')
  <div class="mx-auto max-w-xl py-4 sm:py-6">
    <h1 class="page-title text-white">Bikin Circle Baru </h1>
    <p class="muted mt-1 text-[#a19db0]">Buat tongkrongan baru lu (geng ngopi, squad futsal, circle mabar, dll). Begitu kelar langsung dapet link invite buat anak-anak.</p>

    <form method="POST" action="{{ route('groups.store') }}" enctype="multipart/form-data" class="glass-card rounded-3xl border border-white/10 mt-6 space-y-5 p-6 sm:p-8 text-white shadow-xl">
      @csrf

      {{-- Avatar Grup --}}
      <div class="flex items-center gap-4 p-4 rounded-2xl bg-[#181224] border border-white/10">
        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-[#5D549C] text-white shadow-md border border-[#A373D3]/30">
          <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1" for="avatar">Avatar / Logo Circle</label>
          <input type="file" id="avatar" name="avatar" accept="image/*" class="block w-full text-xs text-[#a19db0] file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#5D549C] file:text-white hover:file:bg-[#7E3A63] cursor-pointer">
          <p class="text-[11px] text-[#6F70A2] mt-1">Format: JPG, PNG, WEBP (maks. 2MB)</p>
          @error('avatar')<p class="text-xs text-[#EB9998] mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      <div>
        <label class="label text-white" for="group-name">Nama Circle Tongkrongan</label>
        <input class="input" id="group-name" type="text" name="name" value="{{ old('name') }}" required maxlength="60" placeholder="cth: Geng Kopi Santuy">
        @error('name')<p class="field-error text-[#EB9998]">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="label text-white" for="group-desc">Deskripsi / Motto <span class="font-normal text-[#6F70A2]">(opsional)</span></label>
        <textarea class="textarea" id="group-desc" name="description" rows="3" maxlength="500" placeholder="Cerita singkat atau motto seru circle lu.">{{ old('description') }}</textarea>
        @error('description')<p class="field-error text-[#EB9998]">{{ $message }}</p>@enderror
      </div>

      {{-- Visibility --}}
      <div>
        <label class="label text-white">Privasi Circle</label>
        <div class="grid grid-cols-2 gap-3">
          <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-[#181224] p-4 cursor-pointer transition hover:border-[#A373D3] has-[:checked]:border-[#A373D3] has-[:checked]:bg-[#5D549C]/30">
            <input type="radio" name="visibility" value="public" class="hidden peer" {{ old('visibility', 'public') === 'public' ? 'checked' : '' }}>
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <p class="text-sm font-bold text-white">Publik</p>
              <p class="text-[11px] text-[#6F70A2]">Bisa dicari & join siapa saja</p>
            </div>
          </label>
          <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-[#181224] p-4 cursor-pointer transition hover:border-[#A373D3] has-[:checked]:border-[#A373D3] has-[:checked]:bg-[#5D549C]/30">
            <input type="radio" name="visibility" value="private" class="hidden peer" {{ old('visibility') === 'private' ? 'checked' : '' }}>
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
            <div>
              <p class="text-sm font-bold text-white">Privat</p>
              <p class="text-[11px] text-[#6F70A2]">Khusus via link invite</p>
            </div>
          </label>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 pt-3 border-t border-white/5">
        <a href="{{ route('groups.index') }}" class="btn btn-secondary">Skip Dulu</a>
        <button type="submit" class="pushable">
          <span class="shadow"></span>
          <span class="edge"></span>
          <span class="front">Gas Bikin Circle </span>
        </button>
      </div>
    </form>
  </div>
@endsection
