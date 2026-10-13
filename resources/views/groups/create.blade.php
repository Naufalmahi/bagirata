@extends('layouts.app')

@section('title', 'Bikin Group Baru')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="page-title">Bikin Group Baru</h1>
        <p class="muted mt-1">Buat circle kamu (gang nongkrong, squad futsal, rekan kantor, dll). Setelah ini langsung dapet link undangan.</p>

        <form method="POST" action="{{ route('groups.store') }}" enctype="multipart/form-data" class="card mt-6 space-y-5 p-6">
            @csrf

            {{-- Avatar Grup --}}
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white shadow-sm">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1" for="avatar">Foto Profil Grup</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP (maks. 2MB)</p>
                    @error('avatar')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="label" for="group-name">Nama Group</label>
                <input class="input" id="group-name" type="text" name="name" value="{{ old('name') }}" required maxlength="60" placeholder="cth: Geng Rawon">
                @error('name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="label" for="group-desc">Deskripsi <span class="font-normal text-ink-subtle">(opsional)</span></label>
                <textarea class="textarea" id="group-desc" name="description" rows="3" maxlength="500" placeholder="Cerita singkat atau tujuan grup circle ini.">{{ old('description') }}</textarea>
                @error('description')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Visibility --}}
            <div>
                <label class="label">Tipe Grup</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 cursor-pointer transition hover:border-indigo-300 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/60">
                        <input type="radio" name="visibility" value="public" class="hidden peer" {{ old('visibility', 'public') === 'public' ? 'checked' : '' }}>
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Public</p>
                            <p class="text-[11px] text-slate-500">Bisa dicari & diikuti siapa saja</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 cursor-pointer transition hover:border-indigo-300 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/60">
                        <input type="radio" name="visibility" value="private" class="hidden peer" {{ old('visibility') === 'private' ? 'checked' : '' }}>
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Private</p>
                            <p class="text-[11px] text-slate-500">Hanya bisa masuk lewat undangan</p>
                        </div>
                    </label>
                </div>
                @error('visibility')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <button class="btn btn-primary btn-lg w-full">Bikin Group!</button>
        </form>
    </div>
@endsection
