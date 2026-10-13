<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $friends = $user->friends()->get();

        $pendingReceived = Friendship::where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->with('sender')
            ->latest()
            ->get();

        $pendingSent = Friendship::where('sender_id', $user->id)
            ->where('status', 'pending')
            ->with('receiver')
            ->latest()
            ->get();

        return view('friends.index', compact('friends', 'pendingReceived', 'pendingSent'));
    }

    public function search(Request $request)
    {
        $q = $request->input('q', '');
        $user = $request->user();
        $results = collect();

        if (strlen(trim($q)) >= 2) {
            $results = User::where('id', '!=', $user->id)
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('username', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                })
                ->limit(20)
                ->get()
                ->map(function ($u) use ($user) {
                    $u->friendship_status = null;
                    $friendship = $user->friendshipWith($u);
                    if ($friendship) {
                        $u->friendship_status = $friendship->status;
                        $u->friendship_id = $friendship->id;
                        $u->is_sender = $friendship->sender_id === $user->id;
                    }
                    return $u;
                });
        }

        return view('friends.search', compact('results', 'q'));
    }

    public function sendRequest(Request $request, User $user)
    {
        $me = $request->user();

        if ($me->id === $user->id) {
            return back()->with('error', 'Tidak bisa menambahkan diri sendiri.');
        }

        $existing = $me->friendshipWith($user);
        if ($existing) {
            return back()->with('error', 'Permintaan pertemanan sudah ada.');
        }

        Friendship::create([
            'sender_id' => $me->id,
            'receiver_id' => $user->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permintaan pertemanan terkirim!');
    }

    public function accept(Request $request, Friendship $friendship)
    {
        if ($friendship->receiver_id !== $request->user()->id) {
            abort(403);
        }

        $friendship->update(['status' => 'accepted']);

        return back()->with('success', 'Pertemanan diterima!');
    }

    public function reject(Request $request, Friendship $friendship)
    {
        if ($friendship->receiver_id !== $request->user()->id) {
            abort(403);
        }

        $friendship->delete();

        return back()->with('success', 'Permintaan pertemanan ditolak.');
    }

    public function remove(Request $request, Friendship $friendship)
    {
        $me = $request->user();
        if ($friendship->sender_id !== $me->id && $friendship->receiver_id !== $me->id) {
            abort(403);
        }

        $friendship->delete();

        return back()->with('success', 'Pertemanan dihapus.');
    }
}
