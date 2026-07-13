<?php

namespace App\Models;

use Database\Factories\LearningLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningLog extends Model
{
    /** @use HasFactory<LearningLogFactory> */
    use HasFactory;

    protected $fillable = [
        'learned_on',
        'minutes',
        'topic',
        'reflection',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'learned_on' => 'date',
            'minutes' => 'integer',
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
