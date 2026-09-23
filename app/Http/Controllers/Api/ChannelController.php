<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\StoreChannelRequest;
use App\Http\Requests\Groups\UpdateChannelRequest;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Models\Group;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChannelController extends Controller
{
    public function index(Request $request, Group $group)
    {
        $this->authorize('view', $group);

        $channels = $group->channels()->withCount('sessions')->get();

        return ApiResponse::success(ChannelResource::collection($channels), '');
    }

    public function store(StoreChannelRequest $request, Group $group)
    {
        $channel = $group->channels()->create([
            'name' => $request->validated()['name'],
            'description' => $request->validated()['description'] ?? null,
            'sort_order' => $group->channels()->count() + 1,
            'created_by' => $request->user()->id,
        ]);
        $channel->loadCount('sessions');

        return ApiResponse::created(new ChannelResource($channel), 'Channel baru kebikin.');
    }

    public function show(Request $request, Group $group, Channel $channel)
    {
        $this->authorize('view', $channel);

        if ($channel->group_id !== $group->id) {
            abort(404);
        }

        $channel->load(['sessions.members', 'sessions.expenses', 'sessions.debts']);
        $channel->loadCount('sessions');

        return ApiResponse::success(new ChannelResource($channel), '');
    }

    public function update(UpdateChannelRequest $request, Group $group, Channel $channel)
    {
        if ($channel->group_id !== $group->id) {
            abort(404);
        }

        $channel->update($request->validated());
        $channel->loadCount('sessions');

        return ApiResponse::success(new ChannelResource($channel), 'Channel ke-update.');
    }

    public function destroy(Request $request, Group $group, Channel $channel)
    {
        $this->authorize('delete', $channel);

        if ($channel->group_id !== $group->id) {
            abort(404);
        }

        // Patungan di dalam channel aman: pindah ke level group (histori nggak hilang).
        DB::transaction(function () use ($channel) {
            $channel->sessions()->update(['channel_id' => null]);
            $channel->delete();
        });

        return ApiResponse::noContent('Channel dihapus, patungan di dalamnya pindah ke level group.');
    }
}
