<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\StoreTreasuryEntryRequest;
use App\Models\Group;
use App\Models\WalletEntry;
use App\Services\TreasuryService;
use Illuminate\Http\Request;

class TreasuryController extends Controller
{
    public function show(Request $request, Group $group)
    {
        if (! $group->isMember($request->user()) && $group->user_id !== $request->user()->id) {
            abort(403);
        }

        $wallet = TreasuryService::getOrCreateWallet($group);

        $entries = $wallet->entries()
            ->with(['user', 'approver'])
            ->latest()
            ->paginate(15);

        $pendingEntries = $wallet->entries()
            ->where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();

        $inTotal = $wallet->entries()->where('status', 'approved')->where('type', 'in')->sum('amount');
        $outTotal = $wallet->entries()->where('status', 'approved')->where('type', 'out')->sum('amount');

        $isManager = $group->user_id === $request->user()->id;

        return view('groups.treasury', compact('group', 'wallet', 'entries', 'pendingEntries', 'inTotal', 'outTotal', 'isManager'));
    }

    public function store(StoreTreasuryEntryRequest $request, Group $group)
    {
        if (! $group->isMember($request->user()) && $group->user_id !== $request->user()->id) {
            abort(403);
        }

        $wallet = TreasuryService::getOrCreateWallet($group);

        $proofPath = null;
        if ($request->hasFile('proof_photo')) {
            $proofPath = $request->file('proof_photo')->store('treasury-receipts', 'public');
        }

        TreasuryService::recordEntry($wallet, $request->user(), $request->validated(), $proofPath);

        return back()->with('success', 'Transaksi kas berhasil dicatat.');
    }

    public function approve(Request $request, Group $group, WalletEntry $entry)
    {
        $this->assertEntryBelongsToGroup($group, $entry);

        if ($group->user_id !== $request->user()->id) {
            abort(403, 'Hanya admin yang boleh menyetujui transaksi.');
        }

        $this->authorize('approveEntry', $entry);

        TreasuryService::approveEntry($entry, $request->user());

        return back()->with('success', 'Transaksi kas disetujui.');
    }

    public function reject(Request $request, Group $group, WalletEntry $entry)
    {
        $this->assertEntryBelongsToGroup($group, $entry);

        if ($group->user_id !== $request->user()->id) {
            abort(403, 'Hanya admin yang boleh menolak transaksi.');
        }

        $this->authorize('rejectEntry', $entry);

        TreasuryService::rejectEntry($entry, $request->user(), $request->input('note'));

        return back()->with('success', 'Transaksi kas ditolak.');
    }

    /**
     * {entry} di-resolve global, jadi harus dipastikan wallet-nya milik group
     * yang ada di URL. 404 (bukan 403) supaya keberadaan entry group lain
     * nggak bocor.
     */
    private function assertEntryBelongsToGroup(Group $group, WalletEntry $entry): void
    {
        if ($entry->wallet?->group_id !== $group->id) {
            abort(404);
        }
    }
}
