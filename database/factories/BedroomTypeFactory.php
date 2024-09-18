<?php

namespace Database\Factories;

use App\Models\BedroomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BedroomTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BedroomType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => [
                'en' => $this->faker->name,
                'my' => $this->faker->name,
            ],
            'slug' => $this->faker->slug(),
        ];
    }
}
