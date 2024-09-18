<?php

namespace Database\Factories;

use App\Models\BedroomType;
use App\Models\Property;
use App\Models\PropertyBedroomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyBedroomTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyBedroomType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'bedroom_type_id' => BedroomType::factory(),
            'quantity' => $this->faker->numberBetween(1, 20),
        ];
    }
}
