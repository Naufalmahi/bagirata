@extends('layouts.app')

@section('title', 'Groups')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-extrabold text-slate-900">Groups</h1>
        <a href="{{ route('groups.create') }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">+ Bikin group</a>
    </div>

    @if ($groups->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <div class="text-4xl">🏕️</div>
            <p class="mt-3 font-semibold text-slate-700">Belum ada group nih.</p>
            <p class="mt-1 text-sm text-slate-500">Bikin group buat circle lu biar patungannya rapi & punya channel sendiri.</p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($groups as $group)
                <a href="{{ route('groups.show', $group) }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-indigo-300 hover:shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <h2 class="font-bold text-slate-900">{{ $group->name }}</h2>
                        <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">{{ $group->members_count }} orang</span>
                    </div>
                    @if ($group->description)
                        <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $group->description }}</p>
                    @endif
                    <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
                        <span>by {{ $group->owner->name }}</span>
                        <span>{{ $group->sessions_count }} patungan</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection