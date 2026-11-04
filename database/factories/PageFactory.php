<?php

namespace Database\Factories;

use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'user_id' => User::factory(),
            'title' => Str::title($title),
            'slug' => Str::slug($title),
            'content' => '<p>'.implode('</p><p>', fake()->paragraphs(3)).'</p>',
            'image' => null,
            'status' => PageStatus::Published,
            'sort_order' => fake()->numberBetween(1, 10),
            'show_in_menu' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PageStatus::Draft,
        ]);
    }
}
