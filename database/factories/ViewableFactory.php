<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Viewable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ViewableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Viewable::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random(),
            'viewable_id' => $this->faker->randomNumber(),
            'viewable_type' => $this->faker->regexify('[A-Za-z0-9]{30}'),
        ];
    }
}
