<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileName = fake()->unique()->word().'.jpg';

        return [
            'user_id' => User::factory(),
            'file_name' => $fileName,
            'file_path' => 'media/'.$fileName,
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(1024, 500000),
            'alt_text' => fake()->sentence(3),
            'disk' => 'public',
        ];
    }
}
