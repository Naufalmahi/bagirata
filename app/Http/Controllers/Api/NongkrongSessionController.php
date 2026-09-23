<?php

namespace App\Http\Controllers\Api;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Nongkrong\SplitPreviewRequest;
use App\Http\Requests\Nongkrong\StoreSessionRequest;
use App\Http\Requests\Nongkrong\UpdateSessionRequest;
use App\Http\Resources\SessionResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\NongkrongSession;
use App\Models\User;
use App\Services\CalculationService;
use App\Services\PermissionService;
use App\Services\SessionService;
use App\Services\SplitService;
use App\Services\Support\Money;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class NongkrongSessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = $request->user()->nongkrongSessions()
            ->with(['creator', 'members', 'group', 'channel', 'expenses', 'debts'])
            ->latest('date')
            ->paginate(20);

        return ApiResponse::success(SessionResource::collection($sessions), '');
    }

    public function store(StoreSessionRequest $request)
    {
        $data = $request->validated();

        if (! empty($data['group_id'])) {
            $group = Group::findOrFail($data['group_id']);
            $canCreate = PermissionService::can($request->user(), $group, Permission::CREATE_PATUNGAN)
                || PermissionService::can($request->user(), $group, Permission::MANAGE_PATUNGAN);
            abort_unless($canCreate, 403, 'Role lu nggak boleh bikin patungan di group ini.');
        }

        $session = SessionService::create($request->user(), $data);
        $this->loadDetail($session, $request->user());

        return ApiResponse::created((new SessionResource($session))->viewer($request->user()->id), 'Nongkrong baru kebikin, gas!');
    }

    public function show(Request $request, NongkrongSession $session)
    {
        $this->authorize('view', $session);
        $this->loadDetail($session, $request->user());

        return ApiResponse::success(
            (new SessionResource($session))->withSplit()->viewer($request->user()->id),
            ''
        );
    }

    public function update(UpdateSessionRequest $request, NongkrongSession $session)
    {
        $session->update($request->validated());
        $this->loadDetail($session, $request->user());

        return ApiResponse::success((new SessionResource($session))->viewer($request->user()->id), 'Nongkrong ke-update.');
    }

    public function destroy(Request $request, NongkrongSession $session)
    {
        $this->authorize('delete', $session);
        $session->delete();

        return ApiResponse::noContent('Nongkrong dibatalin, catatannya aman di riwayat.');
    }

    /**
     * Hitung preview split tanpa nyimpen apa pun (frontend nggak pernah ngitung sendiri).
     */
    public function splitPreview(SplitPreviewRequest $request, NongkrongSession $session)
    {
        $input = $request->validated();

        $grandTotal = CalculationService::grandTotal(
            (int) $input['amount'],
            $input['discount_type'],
            (int) $input['discount_value'],
            (int) ($input['service_rate'] ?? 0),
            (int) ($input['tax_rate'] ?? 0)
        );

        $participantIds = array_values(array_unique($input['participant_ids']));
        $amountsOrPercent = $input['split_type'] === 'custom'
            ? ($input['custom_amounts'] ?? [])
            : ($input['percentages'] ?? []);

        $shares = SplitService::split($input['split_type'], $grandTotal, $participantIds, $amountsOrPercent);

        $users = User::whereKey($participantIds)->get()->keyBy('id');
        $preview = collect($participantIds)->map(fn ($id) => [
            'user' => new UserResource($users->get($id)),
            'share' => $shares[$id],
            'share_formatted' => Money::format($shares[$id]),
        ]);

        $paidBy = User::find($input['paid_by_user_id']);

        return ApiResponse::success([
            'grand_total' => $grandTotal,
            'grand_total_formatted' => Money::format($grandTotal),
            'discount_amount' => CalculationService::discountAmount((int) $input['amount'], $input['discount_type'], (int) $input['discount_value']),
            'service_amount' => CalculationService::serviceAmount(
                CalculationService::baseAfterDiscount((int) $input['amount'], $input['discount_type'], (int) $input['discount_value']),
                (int) ($input['service_rate'] ?? 0)
            ),
            'tax_amount' => CalculationService::taxAmount(
                CalculationService::baseAfterDiscount((int) $input['amount'], $input['discount_type'], (int) $input['discount_value']),
                CalculationService::serviceAmount(
                    CalculationService::baseAfterDiscount((int) $input['amount'], $input['discount_type'], (int) $input['discount_value']),
                    (int) ($input['service_rate'] ?? 0)
                ),
                (int) ($input['tax_rate'] ?? 0)
            ),
            'paid_by' => $paidBy ? new UserResource($paidBy) : null,
            'split_type' => $input['split_type'],
            'splits' => $preview,
        ], '');
    }

    private function loadDetail(NongkrongSession $session, User $actor): void
    {
        $session->load([
            'creator',
            'members',
            'group',
            'channel',
            'expenses.payer',
            'expenses.creator',
            'expenses.splits.user',
            'debts.debtor',
            'debts.creditor',
            'debts.settler',
        ]);
    }
}
