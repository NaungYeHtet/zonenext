<?php

namespace Database\Factories;

use App\Enums\PropertyAcquisitionType;
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

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (PropertyAcquisition $record) {
            if ($record->type == PropertyAcquisitionType::Sell) {
                $minPrice = 60000000;
                $maxPrice = 600000000;
                $step = 5000000;
                $ownerCommission = fake()->randomNumber(1);
                $customerCommission = 0;
            } else {
                $minPrice = 300000;
                $maxPrice = 6000000;
                $step = 50000;
                $ownerCommission = fake()->randomElement([30, 50, 70, 100, 200]);
                $customerCommission = fake()->randomElement([30, 50, 70, 100, 200]);
            }

            $priceFrom = get_stepped_random_number($minPrice, $maxPrice / 2, $step);
            $priceTo = 0;

            if ($record->price_type == PropertyPriceType::Range) {
                $priceTo = get_stepped_random_number($priceFrom, $priceFrom * 2, $step);
            }

            $record->update([
                'price_from' => $priceFrom,
                'price_to' => $priceTo,
                'owner_commission' => $ownerCommission,
                'customer_commission' => $customerCommission,
            ]);
        });
    }
}
