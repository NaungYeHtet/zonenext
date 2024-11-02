<?php

namespace Database\Factories;

use App\Models\Rateable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RateableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rateable::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->boolean() ? User::all()->random() : null,
            'rating' => $this->faker->numberBetween(1, 5),
            'review' => $this->faker->optional()->text(),
        ];
    }
}
