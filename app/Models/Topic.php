<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Topic extends Model
{
    protected $fillable = [
        'module_id', 'title', 'slug', 'content_json',
        'sort_order', 'estimated_minutes', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'content_json' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(TopicProgress::class);
    }

    public function annotations(): HasMany
    {
        return $this->hasMany(Annotation::class);
    }

    public function understanding(): HasOne
    {
        return $this->hasOne(Understanding::class);
    }
}
