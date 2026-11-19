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
        Schema::create('understanding_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('understanding_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('multiple_choice');
            $table->text('question');
            $table->text('explanation')->nullable();
            $table->unsignedTinyInteger('points')->default(1);
            $table->unsignedSmallInteger('sort_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('understanding_questions');
    }
};
