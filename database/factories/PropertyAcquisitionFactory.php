<?php

namespace Database\Factories;

use App\Enums\PropertyPriceType;
use App\Enums\PropertyType;
use App\Models\Property;
use App\Models\PropertyAcquisition;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyAcquisitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyAcquisition::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'type' => $this->faker->randomElement(PropertyType::cases()),
            'price_type' => $this->faker->randomElement(PropertyPriceType::cases()),
            'price_from' => $this->faker->numberBetween(-10000, 10000),
            'price_to' => $this->faker->numberBetween(-10000, 10000),
            'negotiable' => $this->faker->boolean(),
            'owner_commission' => $this->faker->randomFloat(2, 0, 9.99),
            'customer_commission' => $this->faker->randomFloat(2, 0, 9.99),
        ];
    }
}
