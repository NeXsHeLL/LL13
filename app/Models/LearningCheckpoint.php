<?php

namespace App\Models;

use Database\Factories\LearningCheckpointFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningCheckpoint extends Model
{
    /** @use HasFactory<LearningCheckpointFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'notes',
        'activity_type',
        'difficulty',
        'prompt',
        'options',
        'correct_option',
        'user_answer',
        'explanation',
        'is_complete',
        'position',
    ];

    protected $attributes = [
        'activity_type' => 'task',
        'difficulty' => 'basics',
        'is_complete' => false,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_complete' => 'boolean',
            'options' => 'array',
            'position' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<LearningPath, $this>
     */
    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }
}
