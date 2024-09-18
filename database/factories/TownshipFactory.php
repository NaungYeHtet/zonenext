<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\State;
use App\Models\Township;

class TownshipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Township::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'state_id' => State::factory(),
            'code' => $this->faker->regexify('[A-Za-z0-9]{30}'),
            'slug' => $this->faker->slug(),
            'name' => '{}',
        ];
    }
}
