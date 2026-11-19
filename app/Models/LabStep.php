<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabStep extends Model
{
    protected $fillable = ['lab_id', 'title', 'instruction', 'expected_result_json', 'reference_query', 'xp_reward', 'sort_order'];

    protected function casts(): array
    {
        return ['expected_result_json' => 'array'];
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class);
    }
}
