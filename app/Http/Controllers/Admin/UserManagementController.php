<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\Module;
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

        $modules = Module::where('is_published', true)->orderBy('sort_order')->get();

        return view('admin.users.index', compact('users', 'search', 'modules'));
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'student', 404);
        $data = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:1000']]);
        $user->update(['is_active' => ! $user->is_active]);
        AdminActionLog::create(['admin_id' => auth()->id(), 'target_user_id' => $user->id, 'action' => $user->is_active ? 'activate_user' : 'deactivate_user', 'reason' => $data['reason']]);

        return back()->with('status', 'Status akun diperbarui.');
    }

    public function toggleLeaderboard(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'student', 404);
        $data = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:1000']]);
        $user->update(['is_leaderboard_visible' => ! $user->is_leaderboard_visible]);
        AdminActionLog::create(['admin_id' => auth()->id(), 'target_user_id' => $user->id, 'action' => 'toggle_leaderboard', 'reason' => $data['reason']]);

        return back()->with('status', 'Visibilitas leaderboard diperbarui.');
    }

    public function resetModuleProgress(Request $request, User $user, Module $module): RedirectResponse
    {
        abort_unless($user->role === 'student', 404);
        $data = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:1000']]);
        $topicIds = $module->topics()->pluck('id');
        $quizIds = $module->quizzes()->pluck('id');
        $user->topicProgress()->whereIn('topic_id', $topicIds)->delete();
        $user->quizAttempts()->whereIn('quiz_id', $quizIds)->delete();
        AdminActionLog::create([
            'admin_id' => auth()->id(), 'target_user_id' => $user->id, 'action' => 'reset_module_progress',
            'reason' => $data['reason'], 'metadata_json' => ['module_id' => $module->id],
        ]);

        return back()->with('status', 'Progress modul direset. XP histori tidak diubah.');
    }
}
