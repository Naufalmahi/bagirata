@extends('layouts.app')

@section('title', 'Bikin Group')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="page-title">Bikin group</h1>
        <p class="muted mt-1">Buat circle lu (gang ngaji, squad futsal, temen main, etc). Setelah ini langsung dapet link undangan.</p>

        <form method="POST" action="{{ route('groups.store') }}" class="card mt-6 space-y-4 p-6">
            @csrf

            <div>
                <label class="label" for="group-name">Nama group</label>
                <input class="input" id="group-name" type="text" name="name" value="{{ old('name') }}" required maxlength="60"
                    placeholder="cth: Geng Rawon">
                @error('name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="label" for="group-desc">Deskripsi <span class="font-normal text-ink-subtle">(opsional)</span></label>
                <textarea class="textarea" id="group-desc" name="description" rows="3" maxlength="500"
                    placeholder="Boleh cerita singkat, ato kode rahasia gang.">{{ old('description') }}</textarea>
                @error('description')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <button class="btn btn-primary btn-lg w-full">Bikin!</button>
        </form>
    </div>
@endsection
