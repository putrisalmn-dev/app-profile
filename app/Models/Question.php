<?php

namespace App\Models;

use App\Enums\AnswerStatus;
use App\Enums\QuestionStatus;
use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['topic_id', 'name', 'email', 'phone', 'subject', 'body', 'status', 'ip_address'])]
#[Hidden(['email', 'phone', 'ip_address'])]
class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory, Notifiable;

    protected $attributes = [
        'status' => 'pending',
    ];

    protected function casts(): array
    {
        return [
            'status' => QuestionStatus::class,
        ];
    }

    /**
     * Route mail notifications to the person who asked the question.
     *
     * @return array<string, string>
     */
    public function routeNotificationForMail(): array
    {
        return [$this->email => $this->name];
    }

    /**
     * @return BelongsTo<QuestionTopic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(QuestionTopic::class, 'topic_id');
    }

    /**
     * @return HasMany<QuestionAnswer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    /**
     * @return HasMany<QuestionAnswer, $this>
     */
    public function publishedAnswers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class)
            ->where('status', AnswerStatus::Published)
            ->orderBy('answered_at');
    }

    /**
     * Answered questions that have at least one published answer, i.e. safe to show publicly.
     */
    #[Scope]
    protected function publiclyVisible(Builder $query): void
    {
        $query->where('status', QuestionStatus::Answered)->whereHas('publishedAnswers');
    }

    /**
     * Keep the question status in sync with its published answers. Rejected questions are left untouched.
     */
    public function syncStatusFromAnswers(): void
    {
        if ($this->status === QuestionStatus::Rejected) {
            return;
        }

        $this->update([
            'status' => $this->publishedAnswers()->exists() ? QuestionStatus::Answered : QuestionStatus::Pending,
        ]);
    }
}
