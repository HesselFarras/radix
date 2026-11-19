<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErdComponent extends Model
{
    protected $fillable = ['erd_puzzle_id', 'type', 'name', 'definition_json', 'sort_order'];

    protected function casts(): array
    {
        return ['definition_json' => 'array'];
    }

    public function puzzle(): BelongsTo
    {
        return $this->belongsTo(ErdPuzzle::class, 'erd_puzzle_id');
    }
}
