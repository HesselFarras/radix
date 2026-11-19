<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnderstandingAttempt extends Model
{
    protected $fillable = ['user_id', 'understanding_id', 'score', 'passed', 'answers_json', 'completed_at'];

    protected function casts(): array
    {
        return [
            'passed' => 'boolean',
            'answers_json' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function understanding(): BelongsTo
    {
        return $this->belongsTo(Understanding::class);
    }
}
