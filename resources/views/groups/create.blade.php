@extends('layouts.app')

@section('title', 'Bikin Group')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-extrabold text-slate-900">Bikin group</h1>
        <p class="mt-1 text-sm text-slate-500">Buat circle lu (gang ngaji, squad futsal, temen main, etc). Setelah ini langsung dapet link undangan.</p>

        <form method="POST" action="{{ route('groups.store') }}" class="mt-6 space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label class="mb-1 block text-sm font-semibold">Nama group</label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="60" placeholder="cth: Geng Rawon"
                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Deskripsi <span class="font-normal text-slate-400">(opsional)</span></label>
                <textarea name="description" rows="3" maxlength="500" placeholder="Boleh cerita singkat, ato kode rahasia gang."
                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white hover:bg-indigo-700">Bikin!</button>
        </form>
    </div>
@endsection