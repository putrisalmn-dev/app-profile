<?php

namespace App\Observers;

use App\Enums\AnswerStatus;
use App\Models\QuestionAnswer;
use App\Notifications\QuestionAnsweredNotification;

class QuestionAnswerObserver
{
    /**
     * Handle the QuestionAnswer "creating" event.
     */
    public function creating(QuestionAnswer $answer): void
    {
        $answer->user_id ??= auth()->id();
    }

    /**
     * Handle the QuestionAnswer "saving" event.
     */
    public function saving(QuestionAnswer $answer): void
    {
        if ($answer->status === AnswerStatus::Published && $answer->answered_at === null) {
            $answer->answered_at = now();
        }
    }

    /**
     * Handle the QuestionAnswer "saved" event.
     */
    public function saved(QuestionAnswer $answer): void
    {
        $question = $answer->question;

        if ($question === null) {
            return;
        }

        $question->syncStatusFromAnswers();

        $wasPublished = $answer->wasRecentlyCreated
            ? $answer->isPublished()
            : ($answer->wasChanged('status') && $answer->isPublished());

        if ($wasPublished) {
            $question->notify(new QuestionAnsweredNotification($answer));
        }
    }

    /**
     * Handle the QuestionAnswer "deleted" event.
     */
    public function deleted(QuestionAnswer $answer): void
    {
        $answer->question?->syncStatusFromAnswers();
    }
}
