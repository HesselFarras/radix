<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __invoke(): View
    {
        $scope = User::query()
            ->where('is_active', true)
            ->where('is_leaderboard_visible', true);
        $topUsers = (clone $scope)
            ->orderByDesc('total_xp')
            ->orderBy('name')
            ->limit(10)
            ->get();

        $currentUser = auth()->user();
        $isVisible = $currentUser->is_active && $currentUser->is_leaderboard_visible;
        $position = $isVisible
            ? (clone $scope)->where(function ($query) use ($currentUser): void {
                $query->where('total_xp', '>', $currentUser->total_xp)
                    ->orWhere(function ($query) use ($currentUser): void {
                        $query->where('total_xp', $currentUser->total_xp)->where('name', '<', $currentUser->name);
                    });
            })->count() + 1
            : null;

        return view('leaderboard.index', [
            'topUsers' => $topUsers,
            'currentUser' => $currentUser,
            'position' => $position === false ? null : $position + 1,
        ]);
    }
}
