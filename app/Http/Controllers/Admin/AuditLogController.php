<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __invoke(): View
    {
        $logs = AdminActionLog::with(['admin', 'targetUser'])->latest()->paginate(20);

        return view('admin.audit-logs.index', compact('logs'));
    }
}
