<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Services\BudgetService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $budgets = Budget::where('user_id', $user->id)->get();

        $summaries = $budgets->map(function ($budget) {
            return BudgetService::summary($budget);
        });

        // Let's grab monthly comparison for the first budget or a default one
        $defaultCategory = $budgets->first()?->category ?? 'nongkrong';
        $comparison = BudgetService::monthlyComparison($user, $defaultCategory);

        return view('budgets.index', compact('summaries', 'comparison', 'defaultCategory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'amount' => 'required|integer|min:1',
            'period_type' => 'required|in:monthly,weekly',
        ]);

        $now = Carbon::now();
        if ($validated['period_type'] === 'weekly') {
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
        } else {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
        }

        Budget::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'category' => $validated['category'],
            ],
            [
                'amount' => $validated['amount'],
                'period_start' => $start,
                'period_end' => $end,
            ]
        );

        return back()->with('success', 'Budget berhasil disimpan!');
    }

    public function destroy(Request $request, Budget $budget)
    {
        if ($budget->user_id !== $request->user()->id) {
            abort(403);
        }

        $budget->delete();

        return back()->with('success', 'Budget dihapus.');
    }
}
