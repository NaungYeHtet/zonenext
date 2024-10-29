<?php

namespace Database\Factories;

use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyType;
use App\Models\Lead;
use App\Models\Township;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phone = $this->faker->optional()->phoneNumber();
        $interest = $this->faker->randomElement(LeadInterest::cases());
        $hasMaxPrice = fake()->boolean();
        $maxPrice = null;

        if ($hasMaxPrice) {
            if ($interest == LeadInterest::Renting) {
                $maxPrice = PropertyFactory::fakeRentPriceDetails(rentPriceType: PropertyPriceType::Fix)['rent_price_from'];
            } else {
                $maxPrice = PropertyFactory::fakeSalePriceDetails(salePriceType: PropertyPriceType::Fix)['sale_price_from'];
            }
        }

        return [
            'township_id' => Township::all()->random(),
            'property_type' => get_weighted_random_element([
                PropertyType::Independent->value => 20,
                PropertyType::Condo->value => 20,
                PropertyType::MiniCondo->value => 20,
                PropertyType::Apartment->value => 25,
                PropertyType::Commercial->value => 5,
                PropertyType::Land->value => 5,
                PropertyType::Storage->value => 5,
            ]),
            'interest' => $interest,
            'is_owner' => $this->faker->boolean(),
            'address' => $this->faker->optional()->streetAddress(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'status' => get_weighted_random_element([
                LeadStatus::New->value => 10,
                LeadStatus::Assigned->value => 10,
                LeadStatus::Contacted->value => 10,
                LeadStatus::Scheduled->value => 5,
                LeadStatus::UnderNegotiation->value => 5,
                LeadStatus::Converted->value => 30,
                LeadStatus::Closed->value => 30,
            ]),
            'phone' => $phone,
            'email' => ! $phone ? $this->faker->email() : $this->faker->optional()->email(),
            'send_updates' => $this->faker->boolean(),
            'max_price' => $maxPrice,
            'square_feet' => $this->faker->optional()->randomNumber(3),
            'bedrooms' => $this->faker->optional()->randomNumber(1),
            'bathrooms' => $this->faker->optional()->randomNumber(1),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Lead $lead) {
            event(new \App\Events\LeadSubmitted($lead));
        });
    }
}
