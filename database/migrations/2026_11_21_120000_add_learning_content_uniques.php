<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labs', function (Blueprint $table): void {
            $table->unique(['module_id', 'title'], 'labs_module_title_unique');
        });

        Schema::table('erd_puzzles', function (Blueprint $table): void {
            $table->unique(['module_id', 'title'], 'erd_puzzles_module_title_unique');
        });

        Schema::table('erd_components', function (Blueprint $table): void {
            $table->unique(['erd_puzzle_id', 'name'], 'erd_components_puzzle_name_unique');
        });

        Schema::table('erd_relations', function (Blueprint $table): void {
            $table->unique(['erd_puzzle_id', 'name'], 'erd_relations_puzzle_name_unique');
        });

        Schema::table('erd_components', function (Blueprint $table): void {
            $table->index('erd_puzzle_id', 'erd_components_puzzle_index');
        });

        Schema::table('erd_relations', function (Blueprint $table): void {
            $table->index('erd_puzzle_id', 'erd_relations_puzzle_index');
        });
    }

    public function down(): void
    {
        Schema::table('labs', fn (Blueprint $table) => $table->dropUnique('labs_module_title_unique'));
        Schema::table('erd_puzzles', fn (Blueprint $table) => $table->dropUnique('erd_puzzles_module_title_unique'));
        Schema::table('erd_components', fn (Blueprint $table) => $table->dropUnique('erd_components_puzzle_name_unique'));
        Schema::table('erd_relations', function (Blueprint $table): void {
            $table->dropIndex('erd_relations_puzzle_index');
            $table->dropUnique('erd_relations_puzzle_name_unique');
        });
        Schema::table('erd_components', function (Blueprint $table): void {
            $table->dropIndex('erd_components_puzzle_index');
            $table->dropUnique('erd_components_puzzle_name_unique');
        });
    }
};
