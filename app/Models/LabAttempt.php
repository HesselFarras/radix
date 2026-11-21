<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabAttempt extends Model
{
    protected $fillable = ['user_id', 'lab_step_id', 'query_text', 'result_json', 'is_correct', 'xp_earned', 'attempted_at'];

    protected function casts(): array
    {
        return ['result_json' => 'array', 'is_correct' => 'boolean', 'attempted_at' => 'datetime'];
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(LabStep::class, 'lab_step_id');
    }
}
