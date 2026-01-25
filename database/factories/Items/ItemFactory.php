<?php

namespace Database\Factories\Items;

use App\Enums\ItemTypeEnum;
use App\Enums\ItemUnitEnum;
use App\Models\Items\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items\Item>
 */
class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word(),
            'name' => $this->faker->words(3, true),
            'type' => $this->faker->randomElement(ItemTypeEnum::cases()),
            'unit' => $this->faker->randomElement(ItemUnitEnum::cases()),
            'cost' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
