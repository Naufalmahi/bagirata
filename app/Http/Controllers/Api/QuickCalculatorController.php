<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CalculationService;
use App\Services\SplitService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class QuickCalculatorController extends Controller
{
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'subtotal' => 'required|integer|min:0',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|integer|min:0',
            'service_rate' => 'required|integer|min:0|max:100',
            'tax_rate' => 'required|integer|min:0|max:100',
            'people_count' => 'required_without:splits|integer|min:1',
            'split_type' => 'nullable|in:equal,custom,percentage',
            'splits' => 'nullable|array',
        ]);

        $subtotal = $validated['subtotal'];
        $discountType = $validated['discount_type'];
        $discountValue = $validated['discount_value'];
        $serviceRate = $validated['service_rate'];
        $taxRate = $validated['tax_rate'];

        $discountAmount = CalculationService::discountAmount($subtotal, $discountType, $discountValue);
        $netSubtotal = CalculationService::baseAfterDiscount($subtotal, $discountType, $discountValue);
        $serviceCharge = CalculationService::serviceAmount($netSubtotal, $serviceRate);
        $taxableBase = $netSubtotal + $serviceCharge;
        $tax = CalculationService::taxAmount($netSubtotal, $serviceCharge, $taxRate);
        $grandTotal = CalculationService::grandTotal($subtotal, $discountType, $discountValue, $serviceRate, $taxRate);

        $splitType = $validated['split_type'] ?? 'equal';
        $shares = [];

        if ($splitType === 'equal') {
            $count = $validated['people_count'] ?? count($validated['splits'] ?? []);
            $participantIds = range(1, max(1, $count));
            $shares = SplitService::equal($grandTotal, $participantIds);
        } elseif ($splitType === 'percentage' && !empty($validated['splits'])) {
            $shares = SplitService::percentage($grandTotal, $validated['splits']);
        } elseif ($splitType === 'custom' && !empty($validated['splits'])) {
            $shares = SplitService::custom($grandTotal, $validated['splits']);
        }

        return ApiResponse::success([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'net_subtotal' => $netSubtotal,
            'service_charge' => $serviceCharge,
            'taxable_base' => $taxableBase,
            'tax' => $tax,
            'grand_total' => $grandTotal,
            'split_type' => $splitType,
            'shares' => $shares,
        ], 'Perhitungan berhasil');
    }
}
