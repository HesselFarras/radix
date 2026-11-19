<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErdAttempt extends Model
{
    protected $fillable = ['user_id', 'erd_puzzle_id', 'answer_json', 'score', 'passed', 'xp_earned', 'attempt_number', 'completed_at'];

    protected function casts(): array
    {
        return ['answer_json' => 'array', 'passed' => 'boolean', 'completed_at' => 'datetime'];
    }

    public function puzzle(): BelongsTo
    {
        return $this->belongsTo(ErdPuzzle::class, 'erd_puzzle_id');
    }
}
