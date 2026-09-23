<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\NongkrongSession;
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

        return back()->with('success', 'Utang ditandain lunas. Lega banget.');
    }
}
