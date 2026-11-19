<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __invoke(): View
    {
        $visibleUsers = User::query()
            ->where('is_active', true)
            ->where('is_leaderboard_visible', true)
            ->orderByDesc('total_xp')
            ->orderBy('name')
            ->get();

        $currentUser = auth()->user();
        $position = $visibleUsers->search(fn (User $user) => $user->is($currentUser));
        $topUsers = $visibleUsers->take(10);

        return view('leaderboard.index', [
            'topUsers' => $topUsers,
            'currentUser' => $currentUser,
            'position' => $position === false ? null : $position + 1,
        ]);
    }
}
