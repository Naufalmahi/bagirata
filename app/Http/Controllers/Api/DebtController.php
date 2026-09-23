<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DebtResource;
use App\Models\Debt;
use App\Models\NongkrongSession;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class DebtController extends Controller
{
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
}
