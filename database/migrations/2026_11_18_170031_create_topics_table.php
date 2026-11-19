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
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->json('content_json');
            $table->unsignedSmallInteger('sort_order');
            $table->unsignedSmallInteger('estimated_minutes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->unique(['module_id', 'slug']);
            $table->unique(['module_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
