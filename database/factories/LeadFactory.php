<?php

namespace Database\Factories;

use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyType;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\Property;
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
        return [
            'township_id' => Township::all()->random(),
            'property_type' => get_weighted_random_element([
                PropertyType::Independent->value => 20,
                PropertyType::Condo->value => 15,
                PropertyType::MiniCondo->value => 15,
                PropertyType::Apartment->value => 20,
                PropertyType::Commercial->value => 10,
                PropertyType::Land->value => 10,
                PropertyType::Storage->value => 10,
            ]),
            // 'interest' => $interest,
            // 'is_owner' => $isOwner,
            'address' => $this->faker->optional()->streetAddress(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'status' => get_weighted_random_element([
                LeadStatus::Assigned->value => 20,
                LeadStatus::Contacted->value => 10,
                LeadStatus::Scheduled->value => 5,
                LeadStatus::UnderNegotiation->value => 5,
                LeadStatus::Converted->value => 30,
                LeadStatus::Closed->value => 30,
            ]),
            'phone' => $this->faker->optional()->e164PhoneNumber(),
            'email' => ! $phone ? $this->faker->email() : $this->faker->optional()->email(),
            'send_updates' => $this->faker->boolean(),
            'max_price' => function (array $attributes) {
                $hasMaxPrice = fake()->boolean();
                $maxPrice = null;

                if ($hasMaxPrice) {
                    $acquisitionType = $attributes['interest'] == LeadInterest::Renting->value ? PropertyAcquisitionType::Rent : PropertyAcquisitionType::Sale;

                    $maxPrice = PropertyFactory::fakePriceDetails($acquisitionType, PropertyPriceType::Fix)['price_from'];
                }

                return $maxPrice;
            },
            'square_feet' => $this->faker->optional()->randomNumber(3),
            'bedrooms' => $this->faker->optional()->randomNumber(1),
            'bathrooms' => $this->faker->optional()->randomNumber(1),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Lead $lead) {
            $admin = Admin::leadAssignment($lead->interest, $lead->property_type, $lead->township_id)->first();

            $lead->update([
                'admin_id' => $admin->id,
            ]);
            $admin->notify(new \App\Notifications\LeadAssignedNotification($lead));

            if ($lead->interest != LeadInterest::Buying && $lead->is_owner && in_array($lead->status, [LeadStatus::Converted, LeadStatus::Closed])) {
                Property::factory()
                    ->create([
                        'owner_id' => $lead->id,
                        'type' => $lead->property_type,
                        'acquisition_type' => $lead->interest == LeadInterest::Selling ? PropertyAcquisitionType::Sale : PropertyAcquisitionType::Rent,
                    ]);
            }
        });
    }
}
