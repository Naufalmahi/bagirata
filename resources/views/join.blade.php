@extends('layouts.app')

@section('title', 'Gabung Group')

@section('content')
    <div class="mx-auto max-w-md">
        @if (!$invite || !$usable)
            <div class="card p-8 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
                <h1 class="mt-3 text-xl font-extrabold text-ink">Link undangan nggak bisa dipakai</h1>
                <p class="mt-2 text-sm text-ink-muted">{{ $reasonLabel }}</p>
                <a href="{{ route('home') }}" class="btn btn-secondary mt-5">Ke beranda</a>
            </div>
        @else
            @php $group = $invite->group; @endphp
            <div class="card p-8">
                <div class="flex items-center gap-3">
                    @if ($group->avatar_path)
                        <img src="{{ asset('storage/' . $group->avatar_path) }}" alt="{{ $group->name }}" class="h-14 w-14 shrink-0 rounded-2xl object-cover ring-2 ring-indigo-200">
                    @else
                        <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-2xl font-extrabold text-white">
                            {{ strtoupper(substr($group->name, 0, 1)) }}
                        </span>
                    @endif
                    <div class="min-w-0">
                        <h1 class="text-2xl font-extrabold text-ink">{{ $group->name }}</h1>
                        <p class="text-sm text-ink-muted">diajak {{ $group->owner->name }} · {{ $group->members->count() }} orang udah di dalam</p>
                    </div>
                </div>

                @if ($group->description)
                    <p class="card-sunken mt-4 text-sm text-ink-muted">{{ $group->description }}</p>
                @endif

                <p class="mt-3 text-xs text-ink-subtle">Link ini {{ $invite->lifetimeLabel() }}.</p>

                <div class="mt-6">
                    @auth
                        @if ($alreadyMember)
                            <div class="rounded-card bg-success-soft px-4 py-4 text-center">
                                <p class="text-sm font-semibold text-success-text">Lu udah jadi member group ini, gas terus.</p>
                                <a href="{{ route('groups.show', $group) }}" class="btn btn-primary mt-3 w-full">Ke halaman group</a>
                            </div>
                        @else
                            <form method="POST" action="{{ route('join.store', $invite->token) }}">
                                @csrf
                                <button class="btn btn-primary btn-lg w-full">Gabung group ini</button>
                            </form>
                            <p class="mt-2 text-center text-xs text-ink-subtle">Login sebagai {{ auth()->user()->name }}</p>
                        @endif
                    @else
                        <p class="mb-3 text-center text-sm text-ink-muted">Login atau daftar dulu biar bisa gabung.</p>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" class="btn btn-secondary">Login</a>
                            <a href="{{ route('register', ['redirect' => request()->getRequestUri()]) }}" class="btn btn-primary">Daftar</a>
                        </div>
                    @endauth
                </div>
            </div>
        @endif
    </div>
@endsection
