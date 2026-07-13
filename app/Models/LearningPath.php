<?php

namespace App\Models;

use Database\Factories\LearningPathFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningPath extends Model
{
    /** @use HasFactory<LearningPathFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'focus_area',
        'outcome',
        'status',
        'weekly_minutes',
        'confidence',
        'target_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'target_date' => 'date',
            'weekly_minutes' => 'integer',
            'confidence' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<LearningLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(LearningLog::class);
    }

    /**
     * @return HasMany<LearningCheckpoint, $this>
     */
    public function checkpoints(): HasMany
    {
        return $this->hasMany(LearningCheckpoint::class);
    }
}
