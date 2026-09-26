@extends('layouts.app')

@section('title', 'Bikin Nongkrong')

@section('content')
    <div class="mx-auto max-w-xl">
        <a href="{{ $group ? route('groups.show', $group) : route('nongkrong.index') }}" class="back-link">← Balik</a>
        <h1 class="page-title mt-1">Bikin nongkrong / patungan</h1>
        <p class="muted mt-1">
            @if ($group)
                Patungan di <strong class="text-ink">{{ $group->name }}</strong> — lo otomatis ikut.
            @else
                Patungan dadakan, nggak perlu group. Lo otomatis ikut, tinggal milih temen.
            @endif
        </p>

        <form method="POST" action="{{ route('nongkrong.store') }}" class="card mt-6 space-y-5 p-6">
            @csrf
            @if ($group)
                <input type="hidden" name="group_id" value="{{ $group->id }}">
            @endif

            <div>
                <label class="label" for="ses-name">Nama nongkrong</label>
                <input class="input" id="ses-name" type="text" name="name" value="{{ old('name') }}" required maxlength="120"
                    placeholder="cth: Bakso rame di Pakde">
                @error('name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="label" for="ses-date">Tanggal</label>
                <input class="input" id="ses-date" type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                @error('date')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="label" for="ses-desc">Catatan <span class="font-normal text-ink-subtle">(opsional)</span></label>
                <textarea class="textarea" id="ses-desc" name="description" rows="2" maxlength="500"
                    placeholder="ceritanya mau ngapain">{{ old('description') }}</textarea>
            </div>

            @if ($group)
                <div>
                    <label class="label" for="ses-channel">Channel <span class="font-normal text-ink-subtle">(opsional)</span></label>
                    <select class="select" id="ses-channel" name="channel_id">
                        <option value="">Level group (no channel)</option>
                        @foreach ($channels as $channel)
                            <option value="{{ $channel->id }}" @selected((string) $channel->id === (string) request('channel'))>{{ $channel->name }}</option>
                        @endforeach
                    </select>
                    @error('channel_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                {{-- Group mode: pilih member group --}}
                <div>
                    <span class="label">Siapa aja yang ikut (&ge; 1 temen)</span>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach ($group->members as $member)
                            @if ($member->user_id !== auth()->id())
                                <label class="row-toggle flex-nowrap text-sm hover:bg-sunken">
                                    <input type="checkbox" name="member_ids[]" value="{{ $member->user_id }}" class="checkbox"
                                        @checked(in_array($member->user_id, old('member_ids', [])))>
                                    {{ $member->user->name }}
                                </label>
                            @endif
                        @endforeach
                    </div>
                    @error('member_ids')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            @else
                {{-- Adhoc mode: cari temen --}}
                <div>
                    <span class="label">Ajak temen (&ge; 1 orang)</span>
                    <div x-data="friendPicker('{{ url('/api/v1') }}', {{ auth()->id() }})">
                        <div class="flex items-center gap-2 rounded-control border border-line-strong bg-raised px-3 py-2.5 focus-within:border-brand">
                            <svg class="h-4 w-4 shrink-0 text-ink-subtle" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                            </svg>
                            <input type="text" x-model="q" @input.debounce.250ms="search()" aria-label="Cari teman"
                                placeholder="Cari nama/email temen, min 2 huruf..."
                                class="w-full border-0 bg-transparent p-0 text-sm text-ink placeholder:text-ink-subtle focus:ring-0">
                        </div>

                        <ul x-show="results.length" x-cloak
                            class="mt-2 overflow-hidden rounded-card border border-line bg-raised shadow-pop">
                            <template x-for="r in results" :key="r.id">
                                <li>
                                    <button type="button" @click="add(r)"
                                        class="flex w-full items-center gap-2 px-3 py-2.5 text-left text-sm transition hover:bg-sunken">
                                        <span class="avatar" x-text="r.name.charAt(0).toUpperCase()"></span>
                                        <span class="min-w-0">
                                            <span class="block truncate font-semibold text-ink" x-text="r.name"></span>
                                            <span class="block truncate text-xs text-ink-subtle" x-text="r.email"></span>
                                        </span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                        <p x-show="loading" x-cloak class="hint">Mencari...</p>
                        <p x-show="error" x-cloak class="field-error" x-text="error"></p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <template x-for="s in selected" :key="s.id">
                                <span class="badge badge-brand gap-1.5 py-1.5">
                                    <span x-text="s.name"></span>
                                    <button type="button" @click="remove(s.id)" class="text-brand-text/60 hover:text-brand-text"
                                        :aria-label="'Hapus ' + s.name">✕</button>
                                </span>
                            </template>
                        </div>

                        <template x-for="s in selected" :key="'input' + s.id">
                            <input type="hidden" name="member_ids[]" :value="s.id">
                        </template>
                    </div>
                    @error('member_ids')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            @endif

            <button class="btn btn-primary btn-lg w-full">Gas, bikin!</button>
        </form>
    </div>
@endsection
