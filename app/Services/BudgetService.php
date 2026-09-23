<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\User;
use Carbon\Carbon;

class BudgetService
{
    /**
     * Get spending summary for a budget.
     */
    public static function summary(Budget $budget): array
    {
        // Get actual expenses for this user in this category and period
        // Expenses are linked via NongkrongSession or ExpenseParticipants where user is involved, 
        // or where user paid, or splits. Let's check how expenses are split for a user.
        // Usually, an expense has splits. Let's query ExpenseSplit where user_id = budget->user_id
        
        $spent = \App\Models\ExpenseSplit::where('user_id', $budget->user_id)
            ->whereHas('expense', function ($query) use ($budget) {
                $query->whereBetween('created_at', [$budget->period_start->startOfDay(), $budget->period_end->endOfDay()])
                      ->where('category', $budget->category);
            })
            ->sum('share_amount');

        $utilization = $budget->amount > 0 ? min(100, round(($spent / $budget->amount) * 100)) : 0;

        $alert = self::generateAlert($budget->category, $utilization);

        return [
            'budget' => $budget,
            'spent' => $spent,
            'utilization' => $utilization,
            'alert' => $alert,
        ];
    }

    public static function generateAlert(string $category, int $utilization): ?string
    {
        if ($utilization >= 100) {
            return "Budget {$category} bulan ini udah rata.";
        }
        if ($utilization >= 90) {
            return "Pelan-pelan boss, tinggal 10% lagi.";
        }
        if ($utilization >= 70) {
            return "Budget {$category} lu udah kepakai {$utilization}%.";
        }
        return null;
    }

    public static function monthlyComparison(User $user, string $category): array
    {
        $now = Carbon::now();
        
        // This month spent
        $thisMonthSpent = self::_spentBetween($user, $category, $now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        
        // Last month spent
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $lastMonthSpent = self::_spentBetween($user, $category, $lastMonthStart, $lastMonthEnd);

        // 3-month average
        $threeMonthStart = $now->copy()->subMonths(3)->startOfMonth();
        $threeMonthSpent = self::_spentBetween($user, $category, $threeMonthStart, $now->copy()->subMonth()->endOfMonth());
        $threeMonthAvg = round($threeMonthSpent / 3);

        $suggestion = self::generateSuggestion($category, $thisMonthSpent, $lastMonthSpent, $threeMonthAvg);

        return [
            'this_month' => $thisMonthSpent,
            'last_month' => $lastMonthSpent,
            'three_month_avg' => $threeMonthAvg,
            'suggestion' => $suggestion,
        ];
    }

    private static function _spentBetween(User $user, string $category, Carbon $start, Carbon $end): int
    {
        return \App\Models\ExpenseSplit::where('user_id', $user->id)
            ->whereHas('expense', function ($query) use ($category, $start, $end) {
                $query->whereBetween('created_at', [$start, $end])
                      ->where('category', $category);
            })
            ->sum('share_amount');
    }

    public static function generateSuggestion(string $category, int $thisMonth, int $lastMonth, int $avg): string
    {
        if ($avg === 0) {
            return "Belum cukup data buat bandingin pengeluaran {$category} bulan ini.";
        }

        $diff = round((($thisMonth - $avg) / max(1, $avg)) * 100);

        if ($diff > 0) {
            return "Pengeluaran {$category} bulan ini naik {$diff}% dibanding rata-rata.";
        } elseif ($diff < 0) {
            return "Pengeluaran {$category} bulan ini turun " . abs($diff) . "% dibanding rata-rata.";
        }

        return "Pengeluaran {$category} bulan ini stabil sesuai rata-rata.";
    }
}
