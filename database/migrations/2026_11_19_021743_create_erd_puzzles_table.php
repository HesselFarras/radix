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
        Schema::create('erd_puzzles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->unique(['module_id', 'title']);
            $table->text('case_text');
            $table->text('instructions')->nullable();
            $table->unsignedSmallInteger('max_score')->default(100);
            $table->unsignedSmallInteger('xp_reward')->default(25);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erd_puzzles');
    }
};
