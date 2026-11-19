<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnderstandingQuestion extends Model
{
    protected $fillable = ['understanding_id', 'type', 'question', 'explanation', 'points', 'sort_order'];

    public function understanding(): BelongsTo
    {
        return $this->belongsTo(Understanding::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(UnderstandingOption::class)->orderBy('sort_order');
    }
}
