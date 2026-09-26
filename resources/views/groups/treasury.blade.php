@extends('layouts.app')

@section('title', 'Kas ' . $group->name)

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('groups.show', $group) }}" class="back-link">← Group</a>
            <h1 class="page-title mt-1">Kas Grup: {{ $group->name }}</h1>
            <p class="muted">Pencatatan saldo finansial bersama</p>
        </div>
    </div>

    {{-- Dashboard Summary --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <div class="stat stat-neutral">
            <p class="stat-label">Recorded Balance</p>
            <p class="stat-value money">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
            <p class="text-xs text-ink-subtle">Saldo yang seharusnya ada</p>
        </div>
        <div class="stat stat-success">
            <p class="stat-label">Total Cash In</p>
            <p class="stat-value money">Rp {{ number_format($inTotal, 0, ',', '.') }}</p>
        </div>
        <div class="stat stat-danger">
            <p class="stat-label">Total Cash Out</p>
            <p class="stat-value money">Rp {{ number_format($outTotal, 0, ',', '.') }}</p>
        </div>
    </div>

    <div x-data="{ openModal: false, entryType: 'in' }">
        {{-- Aksi --}}
        <div class="mb-6 flex flex-wrap gap-3">
            <button @click="openModal = true; entryType = 'in'" class="btn btn-primary">+ Kas Masuk</button>
            <button @click="openModal = true; entryType = 'out'" class="btn btn-danger">+ Kas Keluar</button>
        </div>

        {{-- Pending Approvals --}}
        @if($pendingEntries->isNotEmpty())
            <div class="mb-8 rounded-card border border-warning/30 bg-warning-soft p-5">
                <h2 class="font-bold text-warning-text">Nunggu Persetujuan</h2>
                <div class="mt-4 space-y-3">
                    @foreach($pendingEntries as $pending)
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-card border border-line bg-raised p-4">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-ink">
                                    <span class="{{ $pending->type === 'in' ? 'text-success-text' : 'text-danger-text' }}">
                                        {{ $pending->type === 'in' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                    · <span class="money">Rp {{ number_format($pending->amount, 0, ',', '.') }}</span>
                                </p>
                                <p class="text-xs text-ink-muted">{{ $pending->category }} · Oleh {{ $pending->user->name }}</p>
                                @if($pending->description)
                                    <p class="mt-1 text-xs italic text-ink-subtle">"{{ $pending->description }}"</p>
                                @endif
                            </div>
                            @if($isManager)
                                <div class="flex gap-2">
                                    <form action="{{ route('groups.treasury.approve', [$group, $pending]) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-primary btn-sm">Setujui</button>
                                    </form>
                                    <form action="{{ route('groups.treasury.reject', [$group, $pending]) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-ghost btn-sm text-danger-text hover:bg-danger-soft">Tolak</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Ledger / History --}}
        <section class="card overflow-hidden p-0">
            <div class="p-5 pb-0">
                <h2 class="section-title">Riwayat Mutasi</h2>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Oleh</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td class="whitespace-nowrap text-ink-muted">{{ $entry->created_at->format('d/m/y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $entry->type === 'in' ? 'badge-success' : 'badge-danger' }} uppercase">
                                        {{ $entry->type }}
                                    </span>
                                </td>
                                <td class="font-medium capitalize text-ink">{{ $entry->category }}</td>
                                <td>
                                    <span class="money font-bold {{ $entry->status === 'approved'
                                        ? ($entry->type === 'in' ? 'text-success-text' : 'text-danger-text')
                                        : 'text-ink-subtle' }}">
                                        {{ $entry->type === 'in' ? '+' : '-' }}Rp {{ number_format($entry->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-ink-muted">{{ $entry->user->name }}</td>
                                <td>
                                    <span class="badge {{ match ($entry->status) {
                                        'approved' => 'badge-success',
                                        'pending' => 'badge-warning',
                                        default => 'badge-neutral',
                                    } }}">{{ ucfirst($entry->status) }}</span>
                                </td>
                                <td class="text-right">
                                    @if($entry->receipt_photo)
                                        <a href="{{ url('storage/' . $entry->receipt_photo) }}" target="_blank" rel="noopener"
                                            class="text-sm font-semibold text-brand-text hover:underline">Lampiran</a>
                                    @else
                                        <span class="text-ink-subtle">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center italic text-ink-subtle">Belum ada mutasi kas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($entries->hasPages())
                <div class="p-5">{{ $entries->links() }}</div>
            @endif
        </section>

        {{-- Modal Input --}}
        <div x-show="openModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div @click.away="openModal = false" role="dialog" aria-modal="true" aria-label="Catat mutasi kas"
                    class="relative w-full max-w-md rounded-card border border-line bg-raised p-6 shadow-pop">
                    <h3 class="text-xl font-bold text-ink" x-text="entryType === 'in' ? 'Catat Kas Masuk' : 'Catat Kas Keluar'"></h3>
                    <form :action="'{{ route('groups.treasury.store', $group) }}'" method="POST" enctype="multipart/form-data"
                        class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="type" :value="entryType">

                        <div>
                            <label class="label" for="tr-category">Kategori</label>
                            <select class="select" id="tr-category" name="category" required>
                                <option value="iuran">Iuran Bulanan</option>
                                <option value="konsumsi">Makan & Minum</option>
                                <option value="sewa">Sewa Tempat</option>
                                <option value="logistik">Logistik / Barang</option>
                                <option value="donasi">Donasi / Tambahan</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="label" for="tr-amount">Nominal (Rp)</label>
                            <input class="input" id="tr-amount" type="number" name="amount" required min="1" placeholder="Contoh: 50000">
                        </div>

                        <div>
                            <label class="label" for="tr-desc">Keterangan</label>
                            <textarea class="textarea" id="tr-desc" name="description" rows="2" placeholder="Beli apa atau dari siapa..."></textarea>
                        </div>

                        <div>
                            <label class="label" for="tr-proof">Lampiran Bukti <span class="font-normal text-ink-subtle">(Opsional)</span></label>
                            <input class="file-input" id="tr-proof" type="file" name="proof_photo" accept="image/*">
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button type="button" @click="openModal = false" class="btn btn-secondary flex-1">Batal</button>
                            <button type="submit" class="btn flex-1" :class="entryType === 'in' ? 'btn-primary' : 'btn-danger'">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="fixed inset-0 -z-10 bg-ink/40 backdrop-blur-sm"></div>
        </div>
    </div>
@endsection
