<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicProgress extends Model
{
    protected $table = 'topic_progress';

    protected $fillable = ['user_id', 'topic_id', 'completed_at', 'last_read_at'];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'last_read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
