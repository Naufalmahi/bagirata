@extends('layouts.app')

@section('title', 'Groups')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="page-title">Groups</h1>
        <a href="{{ route('groups.create') }}" class="btn btn-primary">+ Bikin group</a>
    </div>

    @if ($groups->isEmpty())
        <div class="empty-panel">
            <div class="text-4xl">🏕️</div>
            <p class="mt-3 font-semibold text-ink">Belum ada group nh.</p>
            <p class="mt-1 text-sm text-ink-muted">Bikin group buat circle lu biar patungannya rapi & punya channel sendiri.</p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($groups as $group)
                <a href="{{ route('groups.show', $group) }}" class="card card-hover block">
                    <div class="flex items-start justify-between gap-2">
                        <h2 class="font-bold text-ink">{{ $group->name }}</h2>
                        <span class="badge badge-neutral shrink-0">{{ $group->members_count }} orang</span>
                    </div>
                    @if ($group->description)
                        <p class="mt-1 line-clamp-2 text-sm text-ink-muted">{{ $group->description }}</p>
                    @endif
                    <div class="mt-3 flex items-center justify-between text-xs text-ink-subtle">
                        <span>by {{ $group->owner->name }}</span>
                        <span>{{ $group->sessions_count }} patungan</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
