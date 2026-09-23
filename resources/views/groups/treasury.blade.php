@extends('layouts.app')

@section('title', 'Kas ' . $group->name)

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('groups.show', $group) }}" class="text-sm font-semibold text-indigo-600 hover:underline">← Group</a>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Kas Grup: {{ $group->name }}</h1>
            <p class="text-sm text-slate-500">Pencatatan saldo finansial bersama</p>
        </div>
    </div>

    {{-- Dashboard Summary --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Recorded Balance</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400">Saldo yang seharusnya ada</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Cash In</p>
            <p class="mt-1 text-2xl font-extrabold text-emerald-600">Rp {{ number_format($inTotal, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Cash Out</p>
            <p class="mt-1 text-2xl font-extrabold text-rose-600">Rp {{ number_format($outTotal, 0, ',', '.') }}</p>
        </div>
    </div>

    <div x-data="{ openModal: false, entryType: 'in' }">
        {{-- Aksi --}}
        <div class="mb-6 flex gap-3">
            <button @click="openModal = true; entryType = 'in'" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                + Kas Masuk
            </button>
            <button @click="openModal = true; entryType = 'out'" class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-700">
                + Kas Keluar
            </button>
        </div>

        {{-- Pending Approvals --}}
        @if($pendingEntries->isNotEmpty())
            <div class="mb-8 rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <h2 class="font-bold text-amber-900">Nunggu Persetujuan</h2>
                <div class="mt-4 space-y-3">
                    @foreach($pendingEntries as $pending)
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow-sm border border-amber-100">
                            <div>
                                <p class="text-sm font-bold text-slate-900">
                                    <span class="{{ $pending->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $pending->type === 'in' ? 'Masuk' : 'Keluar' }}
                                    </span> 
                                    · Rp {{ number_format($pending->amount, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-slate-500">{{ $pending->category }} · Oleh {{ $pending->user->name }}</p>
                                @if($pending->description)
                                    <p class="mt-1 text-xs italic text-slate-400">"{{ $pending->description }}"</p>
                                @endif
                            </div>
                            @if($isManager)
                                <div class="flex gap-2">
                                    <form action="{{ route('groups.treasury.approve', [$group, $pending]) }}" method="POST">
                                        @csrf
                                        <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Setujui</button>
                                    </form>
                                    <form action="{{ route('groups.treasury.reject', [$group, $pending]) }}" method="POST">
                                        @csrf
                                        <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Tolak</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Ledger / History --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm overflow-hidden">
            <h2 class="font-bold text-slate-900 mb-4">Riwayat Mutasi</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-100 text-xs font-semibold uppercase text-slate-400">
                        <tr>
                            <th class="pb-3 pr-4">Tanggal</th>
                            <th class="pb-3 pr-4">Tipe</th>
                            <th class="pb-3 pr-4">Kategori</th>
                            <th class="pb-3 pr-4">Nominal</th>
                            <th class="pb-3 pr-4">Oleh</th>
                            <th class="pb-3 pr-4">Status</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($entries as $entry)
                            <tr class="group">
                                <td class="py-3 pr-4 text-slate-500">{{ $entry->created_at->format('d/m/y H:i') }}</td>
                                <td class="py-3 pr-4">
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $entry->type === 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                        {{ $entry->type }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-slate-700 font-medium capitalize">{{ $entry->category }}</td>
                                <td class="py-3 pr-4">
                                    <span class="font-bold {{ $entry->status === 'approved' ? ($entry->type === 'in' ? 'text-emerald-600' : 'text-rose-600') : 'text-slate-400' }}">
                                        {{ $entry->type === 'in' ? '+' : '-' }}Rp {{ number_format($entry->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-slate-600">{{ $entry->user->name }}</td>
                                <td class="py-3 pr-4">
                                    <span class="text-xs font-semibold {{ $entry->status === 'approved' ? 'text-emerald-500' : ($entry->status === 'pending' ? 'text-amber-500' : 'text-slate-400') }}">
                                        {{ ucfirst($entry->status) }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    @if($entry->receipt_photo)
                                        <a href="{{ url('storage/' . $entry->receipt_photo) }}" target="_blank" class="text-indigo-600 hover:underline">Lampiran</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400 italic">Belum ada mutasi kas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $entries->links() }}
            </div>
        </section>

        {{-- Modal Input --}}
        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div @click.away="openModal = false" class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900" x-text="entryType === 'in' ? 'Catat Kas Masuk' : 'Catat Kas Keluar'"></h3>
                    <form :action="'{{ route('groups.treasury.store', $group) }}'" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="type" :value="entryType">
                        
                        <div>
                            <label class="text-xs font-bold uppercase text-slate-400">Kategori</label>
                            <select name="category" required class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="iuran">Iuran Bulanan</option>
                                <option value="konsumsi">Makan & Minum</option>
                                <option value="sewa">Sewa Tempat</option>
                                <option value="logistik">Logistik / Barang</option>
                                <option value="donasi">Donasi / Tambahan</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold uppercase text-slate-400">Nominal (Rp)</label>
                            <input type="number" name="amount" required min="1" placeholder="Contoh: 50000" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="text-xs font-bold uppercase text-slate-400">Keterangan</label>
                            <textarea name="description" rows="2" placeholder="Beli apa atau dari siapa..." class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <div>
                            <label class="text-xs font-bold uppercase text-slate-400">Lampiran Bukti (Opsional)</label>
                            <input type="file" name="proof_photo" accept="image/*" class="mt-1 block w-full text-xs text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button type="button" @click="openModal = false" class="flex-1 rounded-xl border border-slate-200 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="flex-1 rounded-xl py-2.5 text-sm font-semibold text-white" :class="entryType === 'in' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="fixed inset-0 -z-10 bg-slate-900/40 backdrop-blur-sm"></div>
        </div>
    </div>
@endsection
