<?php

namespace Database\Factories;

use App\Models\FileBucket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<FileBucket>
 */
class FileBucketFactory extends Factory
{
    protected $model = FileBucket::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_type' => $this->faker->word(),
            'model_id' => $this->faker->word(),
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'url' => $this->faker->url(),
            'path' => $this->faker->word(),
            'mime_type' => $this->faker->word(),
            'mime' => $this->faker->word(),
            'extension' => $this->faker->word(),
            'size' => $this->faker->randomFloat(),
            'data' => $this->faker->words(),
            'tags' => $this->faker->words(),
            'publicised_at' => Carbon::now(),
            'finished_at' => Carbon::now(),
            'created_by' => $this->faker->randomNumber(),
            'updated_by' => $this->faker->randomNumber(),
            'deleted_by' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
