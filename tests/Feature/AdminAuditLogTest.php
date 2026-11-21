<?php

namespace Tests\Feature;

use App\Models\AdminActionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'student']);
        AdminActionLog::create(['admin_id' => $admin->id, 'target_user_id' => $target->id, 'action' => 'deactivate_user', 'reason' => 'Pelanggaran aturan']);

        $this->actingAs($admin)->get(route('admin.audit-logs.index'))->assertOk()->assertSee('deactivate_user')->assertSee('Pelanggaran aturan')->assertSee($target->name);
    }

    public function test_student_cannot_view_audit_logs(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))->get(route('admin.audit-logs.index'))->assertForbidden();
    }
}
