<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Debts\ReviewDebtPaymentRequest;
use App\Http\Requests\Debts\StoreDebtPaymentRequest;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\NongkrongSession;
use App\Services\DebtPaymentService;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index(Request $request, NongkrongSession $session)
    {
        $this->authorize('view', $session);

        $session->load([
            'creator',
            'members',
            'group',
            'channel',
            'debts.debtor',
            'debts.creditor',
            'debts.settler',
        ]);

        $debts = $session->debts->sortByDesc('created_at');

        $me = $request->user()->id;
        $active = $debts->filter(fn ($debt) => ! $debt->isSettled());
        $settled = $debts->filter(fn ($debt) => $debt->isSettled());

        $summary = [
            'total_owed' => $active->where('from_user_id', $me)->sum(fn ($debt) => $debt->outstanding()),
            'total_receivable' => $active->where('to_user_id', $me)->sum(fn ($debt) => $debt->outstanding()),
            'active_count' => $active->count(),
            'settled_count' => $settled->count(),
        ];

        return view('debts.index', compact('session', 'active', 'settled', 'summary'));
    }

    public function overview(Request $request)
    {
        $user = $request->user();

        $sessions = $user->nongkrongSessions()
            ->with(['debts.debtor', 'debts.creditor', 'debts.settler'])
            ->withCount('debts')
            ->latest('date')
            ->get();

        $allDebts = $sessions->pluck('debts')->flatten()->sortByDesc('created_at');

        $active = $allDebts->filter(fn (Debt $debt) => ! $debt->isSettled());
        $settled = $allDebts->filter(fn (Debt $debt) => $debt->isSettled());

        $summary = [
            'total_owed' => $active->where('from_user_id', $user->id)->sum(fn (Debt $debt) => $debt->outstanding()),
            'total_receivable' => $active->where('to_user_id', $user->id)->sum(fn (Debt $debt) => $debt->outstanding()),
            'active_count' => $active->count(),
            'settled_count' => $settled->count(),
        ];

        return view('debts.overview', compact('sessions', 'active', 'settled', 'summary'));
    }

    public function show(Request $request, NongkrongSession $session, Debt $debt)
    {
        $this->authorize('view', $debt);

        if ($debt->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        $debt->load(['debtor', 'creditor', 'settler', 'payments.reporter', 'payments.reviewer']);

        return view('debts.show', compact('session', 'debt'));
    }

    public function settle(Request $request, NongkrongSession $session, Debt $debt)
    {
        $this->authorize('settle', $debt);

        if ($debt->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        $debt->markSettled($request->user());

        return back()->with('success', 'Utang ditandain lunas. Lega banget.');
    }
}
