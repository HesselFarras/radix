<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnderstandingOption extends Model
{
    protected $fillable = ['understanding_question_id', 'option_text', 'is_correct', 'sort_order'];

    protected function casts(): array
    {
        return ['is_correct' => 'boolean'];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(UnderstandingQuestion::class, 'understanding_question_id');
    }
}
