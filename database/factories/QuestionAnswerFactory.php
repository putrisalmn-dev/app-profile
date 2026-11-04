<?php

namespace Database\Factories;

use App\Enums\AnswerStatus;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionAnswer>
 */
class QuestionAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'user_id' => User::factory(),
            'body' => fake()->paragraph(),
            'status' => AnswerStatus::Published,
            'answered_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AnswerStatus::Draft,
            'answered_at' => null,
        ]);
    }
}
