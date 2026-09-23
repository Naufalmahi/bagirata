@extends('layouts.app')

@section('title', 'Gabung Group')

@section('content')
    <div class="mx-auto max-w-md">
        @if (!$invite || !$usable)
            <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                <div class="text-4xl">🔗</div>
                <h1 class="mt-3 text-xl font-extrabold text-slate-900">Link undangan nggak bisa dipakai</h1>
                <p class="mt-2 text-sm text-slate-500">{{ $reasonLabel }}</p>
                <a href="{{ route('home') }}" class="mt-5 inline-block rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Ke beranda</a>
            </div>
        @else
            @php $group = $invite->group; @endphp
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-indigo-600 text-2xl font-extrabold text-white">{{ strtoupper(substr($group->name, 0, 1)) }}</span>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-900">{{ $group->name }}</h1>
                        <p class="text-sm text-slate-500">diajak {{ $group->owner->name }} · {{ $group->members->count() }} orang udah di dalam</p>
                    </div>
                </div>

                @if ($group->description)
                    <p class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">{{ $group->description }}</p>
                @endif

                <p class="mt-3 text-xs text-slate-400">Link ini {{ $invite->lifetimeLabel() }}.</p>

                <div class="mt-6">
                    @auth
                        @if ($alreadyMember)
                            <div class="rounded-2xl bg-emerald-50 px-4 py-4 text-center">
                                <p class="text-sm font-semibold text-emerald-700">Lu udah jadi member group ini, gas terus.</p>
                                <a href="{{ route('groups.show', $group) }}" class="mt-3 inline-block w-full rounded-xl bg-emerald-600 px-4 py-3 font-semibold text-white hover:bg-emerald-700">Ke halaman group</a>
                            </div>
                        @else
                            <form method="POST" action="{{ route('join.store', $invite->token) }}">
                                @csrf
                                <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white hover:bg-indigo-700">Gabung group ini</button>
                            </form>
                            <p class="mt-2 text-center text-xs text-slate-400">Login sebagai {{ auth()->user()->name }}</p>
                        @endif
                    @else
                        <p class="mb-3 text-center text-sm text-slate-500">Login atau daftar dulu biar bisa gabung.</p>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" class="rounded-xl border border-slate-300 px-4 py-3 text-center font-semibold text-slate-700 hover:bg-slate-50">Login</a>
                            <a href="{{ route('register', ['redirect' => request()->getRequestUri()]) }}" class="rounded-xl bg-indigo-600 px-4 py-3 text-center font-semibold text-white hover:bg-indigo-700">Daftar</a>
                        </div>
                    @endauth
                </div>
            </div>
        @endif
    </div>
@endsection