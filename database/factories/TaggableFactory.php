<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\Taggable;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaggableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Taggable::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tag_id' => Tag::factory(),
            'taggable_id' => $this->faker->randomNumber(),
            'taggable_type' => $this->faker->regexify('[A-Za-z0-9]{30}'),
        ];
    }
}
