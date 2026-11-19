<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErdPuzzle extends Model
{
    protected $fillable = ['module_id', 'title', 'case_text', 'instructions', 'max_score', 'xp_reward', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(ErdComponent::class)->orderBy('sort_order');
    }

    public function relations(): HasMany
    {
        return $this->hasMany(ErdRelation::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ErdAttempt::class);
    }
}
