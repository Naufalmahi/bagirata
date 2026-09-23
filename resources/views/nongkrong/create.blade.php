@extends('layouts.app')

@section('title', 'Bikin Nongkrong')

@section('content')
    <div class="mx-auto max-w-xl">
        <a href="{{ $group ? route('groups.show', $group) : route('nongkrong.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">← Balik</a>
        <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Bikin nongkrong / patungan</h1>
        <p class="mt-1 text-sm text-slate-500">
            @if ($group)
                Patungan di <strong>{{ $group->name }}</strong> — lo otomatis ikut.
            @else
                Patungan dadakan, nggak perlu group. Lo otomatis ikut, tinggal milih temen.
            @endif
        </p>

        <form method="POST" action="{{ route('nongkrong.store') }}" class="mt-6 space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($group)
                <input type="hidden" name="group_id" value="{{ $group->id }}">
            @endif

            <div>
                <label class="mb-1 block text-sm font-semibold">Nama nongkrong</label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="120" placeholder="cth: Bakso rame di Pakde"
                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Tanggal</label>
                <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required
                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('date')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Catatan <span class="font-normal text-slate-400">(opsional)</span></label>
                <textarea name="description" rows="2" maxlength="500" placeholder="ceritanya mau ngapain"
                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            @if ($group)
                <div>
                    <label class="mb-1 block text-sm font-semibold">Channel <span class="font-normal text-slate-400">(opsional)</span></label>
                    <select name="channel_id" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Level group (no channel)</option>
                        @foreach ($channels as $channel)
                            <option value="{{ $channel->id }}" @selected((string) $channel->id === (string) request('channel'))>{{ $channel->name }}</option>
                        @endforeach
                    </select>
                    @error('channel_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Group mode: pilih member group --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold">Siapa aja yang ikut (&ge; 1 temen)</label>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach ($group->members as $member)
                            @if ($member->user_id !== auth()->id())
                                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm hover:bg-slate-50">
                                    <input type="checkbox" name="member_ids[]" value="{{ $member->user_id }}"
                                        @checked(in_array($member->user_id, old('member_ids', [])))
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    {{ $member->user->name }}
                                </label>
                            @endif
                        @endforeach
                    </div>
                    @error('member_ids')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            @else
                {{-- Adhoc mode: cari temen --}}
                <div>
                    <label class="mb-2 block text-sm font-semibold">Ajak temen (&ge; 1 orang)</label>
                    <div x-data="friendPicker('{{ url('/api/v1') }}', {{ auth()->id() }})">
                        <div class="flex items-center gap-2 rounded-xl border border-slate-300 px-3 py-2 focus-within:border-indigo-500">
                            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                            <input type="text" x-model="q" @input.debounce.250ms="search()" placeholder="Cari nama/email temen, min 2 huruf..."
                                class="w-full border-0 bg-transparent p-0 focus:ring-0">
                        </div>

                        <ul x-show="results.length" x-cloak class="mt-2 overflow-hidden rounded-xl border border-slate-200 bg-white shadow">
                            <template x-for="r in results" :key="r.id">
                                <li>
                                    <button type="button" @click="add(r)" class="flex w-full items-center gap-2 px-3 py-2.5 text-left text-sm hover:bg-indigo-50">
                                        <span class="grid h-7 w-7 place-items-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600" x-text="r.name.charAt(0).toUpperCase()"></span>
                                        <span>
                                            <span class="block font-semibold" x-text="r.name"></span>
                                            <span class="block text-xs text-slate-400" x-text="r.email"></span>
                                        </span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                        <p x-show="loading" x-cloak class="mt-2 text-xs text-slate-400">Mencari...</p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <template x-for="s in selected" :key="s.id">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1.5 text-sm font-medium text-indigo-700">
                                    <span x-text="s.name"></span>
                                    <button type="button" @click="remove(s.id)" class="text-indigo-400 hover:text-indigo-700">✕</button>
                                </span>
                            </template>
                        </div>

                        <template x-for="s in selected" :key="'input' + s.id">
                            <input type="hidden" name="member_ids[]" :value="s.id">
                        </template>
                    </div>
                    @error('member_ids')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            @endif

            <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white hover:bg-indigo-700">Gas, bikin!</button>
        </form>
    </div>
@endsection