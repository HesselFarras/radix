<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $users = User::query()
            ->where('role', 'student')
            ->when($search, fn ($query) => $query->where(fn ($q) => $q->where('name', 'like', "%$search%")->orWhere('email', 'like', "%$search%")->orWhere('nim', 'like', "%$search%")))
            ->withCount(['topicProgress as completed_topics_count' => fn ($query) => $query->whereNotNull('completed_at')])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function toggleActive(User $user): RedirectResponse
    {
        abort_unless($user->role === 'student', 404);
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('status', 'Status akun diperbarui.');
    }

    public function toggleLeaderboard(User $user): RedirectResponse
    {
        abort_unless($user->role === 'student', 404);
        $user->update(['is_leaderboard_visible' => ! $user->is_leaderboard_visible]);

        return back()->with('status', 'Visibilitas leaderboard diperbarui.');
    }
}
