<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lab extends Model
{
    protected $fillable = ['module_id', 'title', 'description', 'mode', 'schema_json', 'sort_order', 'is_published'];

    protected function casts(): array
    {
        return ['schema_json' => 'array', 'is_published' => 'boolean'];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(LabStep::class)->orderBy('sort_order');
    }

    public function attempts(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(LabAttempt::class, LabStep::class, 'lab_id', 'lab_step_id');
    }
}
