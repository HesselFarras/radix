<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErdRelation extends Model
{
    protected $fillable = ['erd_puzzle_id', 'name', 'definition_json'];

    protected function casts(): array
    {
        return ['definition_json' => 'array'];
    }

    public function puzzle(): BelongsTo
    {
        return $this->belongsTo(ErdPuzzle::class, 'erd_puzzle_id');
    }
}
