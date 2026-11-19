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
        Schema::create('erd_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('erd_puzzle_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('definition_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erd_relations');
    }
};
