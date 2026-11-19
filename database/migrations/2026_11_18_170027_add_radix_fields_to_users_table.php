<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim')->nullable()->unique()->after('name');
            $table->string('role')->default('student')->index()->after('password');
            $table->unsignedInteger('total_xp')->default(0)->after('role');
            $table->unsignedSmallInteger('streak_days')->default(0)->after('total_xp');
            $table->boolean('is_active')->default(true)->after('streak_days');
            $table->boolean('is_leaderboard_visible')->default(true)->after('is_active');
            $table->timestamp('onboarding_completed_at')->nullable()->after('is_leaderboard_visible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nim']);
            $table->dropIndex(['role']);
            $table->dropColumn([
                'nim',
                'role',
                'total_xp',
                'streak_days',
                'is_active',
                'is_leaderboard_visible',
                'onboarding_completed_at',
            ]);
        });
    }
};
