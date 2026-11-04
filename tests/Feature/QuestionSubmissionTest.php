<?php

namespace Tests\Feature;

use App\Enums\AnswerStatus;
use App\Enums\QuestionStatus;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionTopic;
use App\Models\User;
use App\Notifications\QuestionAnsweredNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class QuestionSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_submit_a_question(): void
    {
        $topic = QuestionTopic::factory()->create();

        $response = $this->postJson('/api/v1/questions', [
            'topic_id' => $topic->id,
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'phone' => '08123456789',
            'subject' => 'Jadwal Layanan Terpadu',
            'body' => 'Apakah layanan buka pada hari Sabtu?',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.asker_name', 'Siti Aminah')
            ->assertJsonPath('data.subject', 'Jadwal Layanan Terpadu')
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonMissingPath('data.email')
            ->assertJsonMissingPath('data.phone');

        $this->assertDatabaseHas('questions', [
            'topic_id' => $topic->id,
            'email' => 'siti@example.com',
            'status' => QuestionStatus::Pending->value,
        ]);
    }

    public function test_submitting_question_validates_active_topic_and_required_fields(): void
    {
        $inactiveTopic = QuestionTopic::factory()->create(['is_active' => false]);

        $this->postJson('/api/v1/questions', [
            'topic_id' => $inactiveTopic->id,
            'name' => '',
            'email' => 'not-an-email',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['topic_id', 'name', 'email', 'subject', 'body']);
    }

    public function test_public_questions_list_returns_only_answered_questions_with_published_answers(): void
    {
        Notification::fake();

        $question = Question::factory()->create(['subject' => 'Pertanyaan Terjawab']);
        Question::factory()->create(['subject' => 'Pertanyaan Menunggu']);

        $admin = User::factory()->admin()->create();

        QuestionAnswer::factory()->for($question)->for($admin, 'author')->create([
            'body' => '<p>Hari Sabtu layanan tutup.</p>',
            'status' => AnswerStatus::Published,
        ]);

        QuestionAnswer::factory()->for($question)->draft()->create([
            'body' => '<p>Catatan internal draft.</p>',
        ]);

        $this->assertSame(QuestionStatus::Answered, $question->fresh()->status);

        Notification::assertSentTo($question, QuestionAnsweredNotification::class);

        $this->getJson('/api/v1/questions')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.subject', 'Pertanyaan Terjawab')
            ->assertJsonCount(1, 'data.0.answers')
            ->assertJsonPath('data.0.answers.0.body', '<p>Hari Sabtu layanan tutup.</p>');
    }
}
