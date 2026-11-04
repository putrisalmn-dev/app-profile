<?php

namespace Database\Factories;

use App\Enums\MenuItemType;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'parent_id' => null,
            'label' => fake()->words(2, true),
            'url' => '/'.fake()->slug(2),
            'type' => MenuItemType::Custom,
            'linkable_id' => null,
            'linkable_type' => null,
            'sort_order' => 1,
            'is_active' => true,
        ];
    }
}
