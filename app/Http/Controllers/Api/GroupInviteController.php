<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\GroupInvite;
use App\Services\GroupService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class GroupInviteController extends Controller
{
    public function join(Request $request, string $token)
    {
        $invite = GroupInvite::where('token', $token)->first();

        if (! $invite) {
            $request->expectsJson()
                ? abort(404, 'Link undangan nggak ketemu. Cek lagi yaa.')
                : redirect(route('groups.index'));

            return null;
        }

        GroupService::join($request->user(), $invite);

        $group = $invite->group;
        $group->load(['owner', 'roles', 'members.user', 'members.role', 'channels', 'activeInvite']);

        return ApiResponse::success(new GroupResource($group), 'Gabung berhasil! Yang penting nggak lupa bayar. Hehe.');
    }
}
