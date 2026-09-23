<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Http\Requests\Debts\ReviewDebtPaymentRequest;
use App\Http\Requests\Debts\StoreDebtPaymentRequest;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\NongkrongSession;
use App\Services\DebtPaymentService;
=======
use App\Models\Debt;
use App\Models\NongkrongSession;
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
use Illuminate\Http\Request;

class DebtController extends Controller
{
<<<<<<< HEAD
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
=======
    public function settle(Request $request, NongkrongSession $session, Debt $debt)
    {
        $this->authorize('settle', $debt);
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a

        if ($debt->nongkrong_session_id !== $session->id) {
            abort(404);
        }

<<<<<<< HEAD
        $debt->load(['debtor', 'creditor', 'settler', 'payments.reporter', 'payments.reviewer']);

        return view('debts.show', compact('session', 'debt'));
    }

    public function reportPayment(StoreDebtPaymentRequest $request, NongkrongSession $session, Debt $debt)
    {
        if ($debt->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        $proofPath = $this->storeProof($request);
        DebtPaymentService::report($request->user(), $debt, $request->validated(), $proofPath);

        return redirect()->route('debts.show', [$session, $debt])
            ->with('success', 'Lapor bayar kesimpen. Tinggal nunggu kreditur konfirmasi.');
    }

    public function confirmPayment(ReviewDebtPaymentRequest $request, NongkrongSession $session, Debt $debt, DebtPayment $payment)
    {
        if ($debt->nongkrong_session_id !== $session->id || $payment->debt_id !== $debt->id) {
            abort(404);
        }

        $debt = DebtPaymentService::confirm($request->user(), $debt, $payment, $request->input('review_note'));

        $message = $debt->isSettled()
            ? 'Pembayaran dikonfirmasi. Semua aman, utang udah rata!'
            : 'Pembayaran dikonfirmasi, sisa cicilan lanjut lagi.';

        return redirect()->route('debts.show', [$session, $debt])->with('success', $message);
    }

    public function rejectPayment(ReviewDebtPaymentRequest $request, NongkrongSession $session, Debt $debt, DebtPayment $payment)
    {
        if ($debt->nongkrong_session_id !== $session->id || $payment->debt_id !== $debt->id) {
            abort(404);
        }

        DebtPaymentService::reject($request->user(), $debt, $payment, $request->input('review_note', ''));

        return redirect()->route('debts.show', [$session, $debt])
            ->with('success', 'Pembayaran ditolak.');
    }

    private function storeProof(Request $request): ?string
    {
        if (! $request->hasFile('proof_photo')) {
            return null;
        }

        return $request->file('proof_photo')->store('payment-proofs', 'public');
=======
        $debt->markSettled($request->user());

        return back()->with('success', 'Utang ditandain lunas. Lega banget.');
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
    }
}
