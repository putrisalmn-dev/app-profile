<?php

namespace App\Models;

use App\Enums\AnswerStatus;
use App\Observers\QuestionAnswerObserver;
use Database\Factories\QuestionAnswerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['question_id', 'user_id', 'body', 'status', 'answered_at'])]
#[ObservedBy(QuestionAnswerObserver::class)]
class QuestionAnswer extends Model
{
    /** @use HasFactory<QuestionAnswerFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => 'draft',
    ];

    protected function casts(): array
    {
        return [
            'status' => AnswerStatus::class,
            'answered_at' => 'datetime',
        ];
    }

    public function isPublished(): bool
    {
        return $this->status === AnswerStatus::Published;
    }

    /**
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
