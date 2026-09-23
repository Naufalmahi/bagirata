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
<<<<<<< HEAD
            ->sum(fn ($session) => $session->debts
                ->where('status', '!=', DebtStatus::SETTLED->value)
                ->sum(fn ($debt) => $debt->outstanding()));

        $pendingCount = $sessions
            ->sum(fn ($session) => $session->debts
                ->where('status', '!=', DebtStatus::SETTLED->value)
                ->count());
=======
            ->sum(fn ($session) => $session->debts->where('status', DebtStatus::PENDING->value)->sum('amount'));

        $pendingCount = $sessions
            ->sum(fn ($session) => $session->debts->where('status', DebtStatus::PENDING->value)->count());
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a

        return view('dashboard', compact('groups', 'sessions', 'pendingTotal', 'pendingCount'));
    }
}
