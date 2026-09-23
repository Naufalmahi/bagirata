<?php

namespace Tests\Unit\Services;

use App\Services\DebtService;
use PHPUnit\Framework\TestCase;

class DebtServiceTest extends TestCase
{
    public function test_greedy_matching_mengurangi_transaksi(): void
    {
        // A -50k, B -30k, C +40k, D +40k → 4 orang, minimal tetep 3 transaksi
        $balances = [1 => -50_000, 2 => -30_000, 3 => 40_000, 4 => 40_000];

        $debts = DebtService::debtsFromBalances($balances);

        $this->assertSame(3, count($debts));
        $this->assertLessThanOrEqual(3, count($debts));
        // Total kewajiban sama dengan total tagihan
        $this->assertSame(80_000, array_sum(array_column($debts, 'amount')));
    }

    public function test_chain_sederhana(): void
    {
        // Kasus dari spec: A→B 50k, B→C 50k ⇒ disederhanakan jadi A→C 50k
        $balances = [1 => -50_000, 2 => 0, 3 => 50_000];

        $debts = DebtService::debtsFromBalances($balances);

        $this->assertCount(1, $debts);
        $this->assertSame(['from' => 1, 'to' => 3, 'amount' => 50_000], $debts[0]);
    }

    public function test_payer_penuh_creditor(): void
    {
        // Si A nombok full 100k, nggak ikut split; B & C kebagian 50k each
        $balances = [1 => 100_000, 2 => -50_000, 3 => -50_000];

        $debts = DebtService::debtsFromBalances($balances);

        $this->assertSame([
            ['from' => 2, 'to' => 1, 'amount' => 50_000],
            ['from' => 3, 'to' => 1, 'amount' => 50_000],
        ], $debts);
    }

    public function test_total_balance_selalu_nol(): void
    {
        foreach ([
            [1 => -40_000, 2 => 40_000],
            [1 => -100_000, 2 => 60_000, 3 => 40_000],
            [1 => -20_000, 2 => -30_000, 3 => 15_000, 4 => 35_000],
        ] as $balances) {
            $debts = DebtService::debtsFromBalances($balances);

            $creditSum = array_sum(array_filter($balances, fn ($balance) => $balance > 0));
            $this->assertSame($creditSum, array_sum(array_column($debts, 'amount')), 'total utang harus = total tagihan');
        }
    }

    public function test_tidak_ada_self_debt(): void
    {
        $balances = [1 => -25_000, 2 => 25_000];

        $debts = DebtService::debtsFromBalances($balances);

        foreach ($debts as $debt) {
            $this->assertNotSame($debt['from'], $debt['to']);
        }
    }

    public function test_transaksi_maksimal_n_minus_1(): void
    {
        $balances = [];
        for ($i = 1; $i <= 8; $i++) {
            $balances[$i] = $i === 1 ? 100_000 : -10_000;
        }

        $debts = DebtService::debtsFromBalances($balances);

        $this->assertLessThanOrEqual(7, count($debts));
    }
}
