<?php

namespace Database\Factories;

use App\Models\Faq;
use App\Models\QuestionTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'topic_id' => QuestionTopic::factory(),
            'question' => rtrim(fake()->sentence(6), '.').'?',
            'answer' => '<p>'.fake()->paragraph().'</p>',
            'sort_order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}
