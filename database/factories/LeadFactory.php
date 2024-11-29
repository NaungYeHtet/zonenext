<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyType;
use App\Models\Admin;
use App\Models\Appointment;
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
        $phone = $this->faker->optional()->phoneNumber();

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
                LeadStatus::Assigned->value => 10,
                LeadStatus::Contacted->value => 10,
                LeadStatus::FollowedUp->value => 5,
                LeadStatus::UnderNegotiation->value => 5,
                LeadStatus::Converted->value => 30,
                LeadStatus::Closed->value => 40,
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
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Lead $lead) {
            $lead->update([
                'max_price' => PropertyFactory::fakePriceDetails($lead->interest->getPropertyAcquisitionType(), PropertyPriceType::Fix)['price_from'],
            ]);

            // handle lead without assignments
            if (fake()->boolean(5)) {
                $lead->update([
                    'status' => LeadStatus::New,
                ]);

                return;
            }

            $admin = Admin::getLeadAssigmentAgent($lead);

            $lead->update([
                'admin_id' => $admin->id,
            ]);
            $admin->notify(new \App\Notifications\LeadAssignedNotification($lead));

            if ($lead->interest != LeadInterest::Buying && $lead->is_owner && $lead->status == LeadStatus::Converted) {
                $property = Property::factory()
                    ->create([
                        'owner_id' => $lead->id,
                        'type' => $lead->property_type,
                        'acquisition_type' => $lead->interest == LeadInterest::Selling ? PropertyAcquisitionType::Sale : PropertyAcquisitionType::Rent,
                    ]);

                $lead->update([
                    'created_at' => $property->created_at->subDays(rand(1, 30)),
                ]);
            } else {
                if ($lead->property_id && $lead->property->posted_at) {
                    $lead->update([
                        'created_at' => $lead->property->posted_at->addDays(rand(1, 30)),
                    ]);
                }
            }

            if (in_array($lead->status, [LeadStatus::Contacted, LeadStatus::UnderNegotiation, LeadStatus::Converted])) {
                $status = $lead->status == LeadStatus::Converted ? AppointmentStatus::Completed : AppointmentStatus::Pending;

                Appointment::factory()->create([
                    'lead_id' => $lead->id,
                    'date' => $lead->created_at->addDays(rand(1, 10)),
                    'status' => $status
                ]);

                if ($status == AppointmentStatus::Completed) {
                    Appointment::factory(rand(0, 3))->create([
                        'lead_id' => $lead->id,
                        'date' => $lead->created_at->addDays(rand(1, 10)),
                        'status' => AppointmentStatus::Cancelled
                    ]);
                }
            }
        });
    }
}
