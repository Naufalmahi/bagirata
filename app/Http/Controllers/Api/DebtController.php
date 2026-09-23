<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Debts\ReviewDebtPaymentRequest;
use App\Http\Requests\Debts\StoreDebtPaymentRequest;
use App\Http\Resources\DebtPaymentResource;
use App\Http\Resources\DebtResource;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\NongkrongSession;
use App\Services\DebtPaymentService;
use App\Services\Support\Money;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index(Request $request, NongkrongSession $session)
    {
        $this->authorize('view', $session);

        [$totalOwed, $totalReceivable, $activeCount, $settledCount] = $this->dashboardStats($session, $request->user()->id);

        $debts = $session->debts()
            ->with(['debtor', 'creditor', 'settler', 'payments'])
            ->get()
            ->sortByDesc('created_at')
            ->values();

        return ApiResponse::success([
            'total_owed' => $totalOwed,
            'total_owed_formatted' => Money::format($totalOwed),
            'total_receivable' => $totalReceivable,
            'total_receivable_formatted' => Money::format($totalReceivable),
            'active_count' => $activeCount,
            'settled_count' => $settledCount,
            'debts' => DebtResource::collection($debts),
        ], '');
    }

    public function show(Request $request, NongkrongSession $session, Debt $debt)
    {
        $this->authorize('view', $debt);

        if ($debt->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        $debt->load(['debtor', 'creditor', 'settler', 'session', 'payments.reporter', 'payments.reviewer']);

        return ApiResponse::success(new DebtResource($debt), '');
    }

    public function reportPayment(StoreDebtPaymentRequest $request, NongkrongSession $session, Debt $debt)
    {
        if ($debt->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        $proofPath = $this->storeProof($request);

        $payment = DebtPaymentService::report($request->user(), $debt, $request->validated(), $proofPath);
        $payment->load(['reporter', 'reviewer', 'debt.debtor', 'debt.creditor']);

        return ApiResponse::created(
            new DebtPaymentResource($payment),
            'Lapor bayar kesimpen. Tinggal nunggu kreditur konfirmasi.'
        );
    }

    public function confirmPayment(ReviewDebtPaymentRequest $request, NongkrongSession $session, Debt $debt, DebtPayment $payment)
    {
        if ($debt->nongkrong_session_id !== $session->id || $payment->debt_id !== $debt->id) {
            abort(404);
        }

        $debt = DebtPaymentService::confirm(
            $request->user(),
            $debt,
            $payment,
            $request->input('review_note')
        );
        $debt->load(['debtor', 'creditor', 'settler', 'session', 'payments.reporter', 'payments.reviewer']);

        $message = $debt->isSettled()
            ? 'Pembayaran dikonfirmasi. Semua aman, utang udah rata!'
            : 'Pembayaran dikonfirmasi, sisa utang masih lanjut dikit lagi.';

        return ApiResponse::success(new DebtResource($debt), $message);
    }

    public function rejectPayment(ReviewDebtPaymentRequest $request, NongkrongSession $session, Debt $debt, DebtPayment $payment)
    {
        if ($debt->nongkrong_session_id !== $session->id || $payment->debt_id !== $debt->id) {
            abort(404);
        }

        $debt = DebtPaymentService::reject(
            $request->user(),
            $debt,
            $payment,
            $request->input('review_note', '')
        );
        $debt->load(['debtor', 'creditor', 'settler', 'session', 'payments.reporter', 'payments.reviewer']);

        return ApiResponse::success(
            new DebtResource($debt),
            'Pembayaran ditolak. Yuk dipastiin lagi, nggak papa kok.'
        );
    }

    public function settle(Request $request, NongkrongSession $session, Debt $debt)
    {
        $this->authorize('settle', $debt);

        if ($debt->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        $debt->markSettled($request->user());
        $debt->load(['debtor', 'creditor', 'settler']);

        return ApiResponse::success(new DebtResource($debt), 'Udah ditandain bayar. Enak banget, nggak ada utang lagi. Hehe.');
    }

    private function dashboardStats(NongkrongSession $session, int $viewerId): array
    {
        $debts = $session->debts()->with(['debtor', 'creditor', 'settler', 'payments'])->get();

        $totalOwed = 0;
        $totalReceivable = 0;
        $activeCount = 0;
        $settledCount = 0;

        foreach ($debts as $debt) {
            if ($debt->isSettled()) {
                $settledCount++;

                continue;
            }

            $activeCount++;
            $remaining = $debt->outstanding();
            if ($debt->from_user_id === $viewerId) {
                $totalOwed += $remaining;
            } elseif ($debt->to_user_id === $viewerId) {
                $totalReceivable += $remaining;
            }
        }

        return [$totalOwed, $totalReceivable, $activeCount, $settledCount];
    }

    private function storeProof(Request $request): ?string
    {
        if (! $request->hasFile('proof_photo')) {
            return null;
        }

        return $request->file('proof_photo')->store('payment-proofs', 'public');
    }
}
