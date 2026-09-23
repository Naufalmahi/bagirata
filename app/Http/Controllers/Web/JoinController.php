<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\GroupInvite;
use App\Models\GroupMember;
use App\Services\GroupService;
use Illuminate\Http\Request;

class JoinController extends Controller
{
    public function show(Request $request, string $token)
    {
        $invite = GroupInvite::where('token', $token)
            ->with('group.owner', 'group.members')
            ->first();

        $usable = $invite?->usable() ?? false;
        $alreadyMember = false;

        if ($invite && $request->user()) {
            $alreadyMember = GroupMember::where('group_id', $invite->group_id)
                ->where('user_id', $request->user()->id)
                ->exists();
        }

        $reason = null;
        $reasonLabel = null;

        if (! $invite) {
            $reason = 'notfound';
            $reasonLabel = 'Link undangan nggak ketemu. Minta link terbaru ke owner group-nya yaa.';
        } elseif (! $invite->active) {
            $reason = 'dicabut';
            $reasonLabel = 'Link undangan udah dicabut sama admin, jadi nggak bisa dipakai lagi.';
        } elseif ($invite->isExpired()) {
            $reason = 'expired';
            $reasonLabel = 'Link undangan udah kedaluwarsa, minta invite baru ke owner yaa.';
        } elseif ($invite->usageLimitReached()) {
            $reason = 'penuh';
            $reasonLabel = 'Batas pemakaian link undangan udah abis, minta invite baru ke owner yaa.';
        }

        return view('join', compact('invite', 'token', 'usable', 'reason', 'reasonLabel', 'alreadyMember'));
    }

    public function store(Request $request, string $token)
    {
        $invite = GroupInvite::where('token', $token)->with('group')->first();

        if (! $invite) {
            return redirect()->route('home')->with('error', 'Link undangan nggak ketemu, cek lagi yaa.');
        }

        if (! $invite->usable()) {
            return redirect()->route('join.show', $token)
                ->with('error', $invite->invalidReason() ?? 'Link undangan udah nggak bisa dipakai.');
        }

        $alreadyMember = GroupMember::where('group_id', $invite->group_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($alreadyMember) {
            return redirect()->route('groups.show', $invite->group)
                ->with('info', 'Lu udah di group ini, langsung gas aja.');
        }

        try {
            GroupService::join($request->user(), $invite);
        } catch (BusinessException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('groups.show', $invite->group)
            ->with('success', 'Gabung berhasil! Yang penting nggak lupa bayar. Hehe.');
    }
}
