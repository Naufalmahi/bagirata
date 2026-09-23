<?php

namespace App\Http\Controllers\Web;

use App\Enums\Permission;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Nongkrong\StoreSessionRequest;
use App\Models\Group;
use App\Models\NongkrongSession;
use App\Services\PermissionService;
use App\Services\SessionService;
use Illuminate\Http\Request;

class NongkrongController extends Controller
{
    public function index(Request $request)
    {
        $sessions = $request->user()->nongkrongSessions()
            ->with(['creator', 'members', 'group', 'channel', 'expenses', 'debts'])
            ->latest('date')
            ->get();

        $sessions->loadCount(['expenses', 'members', 'debts']);

        return view('nongkrong.index', compact('sessions'));
    }

    public function create(Request $request)
    {
        $group = null;
        $channels = collect();

        if ($request->filled('group')) {
            /** @var Group $group */
            $group = Group::findOrFail($request->integer('group'));

            $canCreate = PermissionService::can($request->user(), $group, Permission::CREATE_PATUNGAN)
                || PermissionService::can($request->user(), $group, Permission::MANAGE_PATUNGAN);

            abort_unless($canCreate, 403, 'Role lu nggak boleh bikin patungan di group ini.');

            $group->load(['channels', 'members.user']);
            $channels = $group->channels;
        }

        return view('nongkrong.create', compact('group', 'channels'));
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

        try {
            $session = SessionService::create($request->user(), $data);
        } catch (BusinessException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('nongkrong.show', $session)
            ->with('success', 'Nongkrong baru kebikin, gas!');
    }

    public function show(Request $request, NongkrongSession $session)
    {
        $this->authorize('view', $session);

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

        $summary = SessionService::summaryFor($session, $request->user()->id);
        $balances = \App\Services\DebtService::netBalances($session);
        $canAddExpense = $request->user()->can('addExpense', $session);

        return view('nongkrong.show', compact('session', 'summary', 'balances', 'canAddExpense'));
    }
}
