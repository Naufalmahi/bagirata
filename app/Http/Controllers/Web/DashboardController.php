<?php

namespace App\Http\Controllers\Web;

use App\Enums\DebtStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $groups = $request->user()->groups()
            ->withCount(['members', 'sessions'])
            ->with(['owner', 'activeInvite', 'channels', 'members'])
            ->latest()
            ->get();

        $sessions = $request->user()->nongkrongSessions()
            ->with(['creator', 'members', 'group', 'expenses', 'debts'])
            ->latest('date')
            ->get()
            ->loadCount(['expenses', 'members', 'debts']);

        $pendingTotal = $sessions
            ->sum(fn ($session) => $session->debts->where('status', DebtStatus::PENDING->value)->sum('amount'));

        $pendingCount = $sessions
            ->sum(fn ($session) => $session->debts->where('status', DebtStatus::PENDING->value)->count());

        return view('dashboard', compact('groups', 'sessions', 'pendingTotal', 'pendingCount'));
    }
}
