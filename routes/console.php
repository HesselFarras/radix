<?php

use App\Models\Lab;
use App\Models\ErdPuzzle;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('radix:cleanup-legacy-content', function () {
    $labs = \App\Models\Lab::query()->whereIn('title', ['Teknik Query SQL', 'Perpustakaan Kampus'])->get();
    $puzzles = \App\Models\ErdPuzzle::query()->where('title', 'Perpustakaan Kampus')->get();
    $this->info("Found {$labs->count()} legacy labs and {$puzzles->count()} legacy puzzles.");
    if ($this->confirm('Delete only these legacy content records?')) {
        $labs->each->delete();
        $puzzles->each->delete();
        $this->info('Legacy content deleted. Learner attempts for valid content remain untouched.');
    }
})->purpose('Review and optionally remove legacy learning content');

Artisan::command('radix:report-learning-content', function () {
    $labs = Lab::query()->withCount('steps')->get();
    $puzzles = ErdPuzzle::query()->withCount(['components', 'relations'])->get();
    $this->info('SQL Labs: '.$labs->count());
    $labs->each(fn (Lab $lab) => $this->line("- {$lab->title}: {$lab->steps_count} steps"));
    $this->info('ERD Puzzles: '.$puzzles->count());
    $puzzles->each(fn (ErdPuzzle $puzzle) => $this->line("- {$puzzle->title}: {$puzzle->components_count} components, {$puzzle->relations_count} relations"));
})->purpose('Report learning content health');
