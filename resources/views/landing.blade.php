@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="flex min-h-[70vh] flex-col items-center justify-center text-center">
        <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-4 py-1.5 text-sm font-semibold text-indigo-700">
            Mode Nongkrong Fun · alpha
        </span>

        <h1 class="max-w-2xl text-4xl font-extrabold leading-tight text-slate-900 md:text-5xl">
            Patungan makin gampang,<br>
            <span class="text-indigo-600">nggak ada lagi yang "tinggal taruh dulu".</span>
        </h1>

        <p class="mt-4 max-w-xl text-slate-600">
            Bayar makan, bensin, sewa tempat, terus bagi-bagian otomatis. Bikin group kayak grup ngumpul
            kalian, undang temen lewat link, tinggal catat pengeluarannya — utang-piutang langsung keitung.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('register') }}" class="rounded-2xl bg-indigo-600 px-6 py-3 font-semibold text-white shadow hover:bg-indigo-700">Gas, daftar</a>
            <a href="{{ route('login') }}" class="rounded-2xl border border-slate-300 bg-white px-6 py-3 font-semibold text-slate-700 hover:bg-slate-50">Udah punya akun</a>
        </div>

        <div class="mt-12 grid w-full max-w-3xl gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-left">
                <div class="text-2xl">🎲</div>
                <h2 class="mt-2 font-bold">Patungan dadakan</h2>
                <p class="mt-1 text-sm text-slate-500">Nggak usah ribet bikin group. Langsung ajak temen, catat, beres.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-left">
                <div class="text-2xl">👥</div>
                <h2 class="mt-2 font-bold">Group kayak Disc-nya</h2>
                <p class="mt-1 text-sm text-slate-500">Channel, role, permission, invite link. Lengkap banget buat gang lu.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-left">
                <div class="text-2xl">⚖️</div>
                <h2 class="mt-2 font-bold">Bagi rata & utang</h2>
                <p class="mt-1 text-sm text-slate-500">Bisa rata, nominal bebas, atau persen. Utang keitung otomatis.</p>
            </div>
        </div>
    </div>
@endsection