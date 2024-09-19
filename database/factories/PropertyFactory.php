<?php

namespace Database\Factories;

use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Property;
use App\Models\PropertyAcquisition;
use App\Models\PropertyDocument;
use App\Models\Rateable;
use App\Models\Township;
use App\Models\Viewable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Property::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'township_id' => Township::factory(),
            'title' => [
                'en' => $this->faker->sentence,
                'my' => $this->faker->sentence,
            ],
            'description' => [
                'en' => $this->faker->paragraph,
                'my' => $this->faker->paragraph,
            ],
            'address' => [
                'en' => $this->faker->address,
                'my' => $this->faker->address,
            ],
            'type' => $this->faker->randomElement(PropertyType::cases()),
            'status' => $this->faker->randomElement(PropertyStatus::cases()),
            'latitude' => $this->faker->latitude(),
            'cover_image' => $this->faker->word,
            'longitude' => $this->faker->longitude(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Property $property) {
            $hasSell = fake()->boolean;
            $statuses = [
                PropertyStatus::Draft,
                PropertyStatus::Posted,
                PropertyStatus::Completed,
            ];

            if ($hasSell) {
                array_push($statuses, PropertyStatus::SoldOut);
                PropertyAcquisition::factory()->create([
                    'type' => PropertyAcquisitionType::Sell,
                    'property_id' => $property->id,
                ]);
            }

            $hasRent = ! $hasSell ? true : fake()->boolean;
            if ($hasRent) {
                array_push($statuses, PropertyStatus::Rent);
                PropertyAcquisition::factory()->create([
                    'type' => PropertyAcquisitionType::Rent,
                    'price_type' => PropertyPriceType::Fix,
                    'property_id' => $property->id,
                ]);
            }

            if ($hasSell && $hasRent) {
                array_push($statuses, PropertyStatus::RentNSoldOut);
            }

            $status = fake()->randomElement($statuses);

            $postedAt = null;
            $soldAt = null;
            $rentAt = null;
            $completedAt = null;

            if ($status != PropertyStatus::Draft) {
                $postedAt = fake()->dateTimeBetween(startDate: $property->created_at, endDate: Carbon::parse($property->created_at)->addYear());

                $endDate = Carbon::parse($postedAt)->addYear();

                if ($status == PropertyStatus::SoldOut) {
                    $soldAt = fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate);
                } elseif ($status == PropertyStatus::Rent) {
                    $rentAt = fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate);
                } elseif ($status == PropertyStatus::RentNSoldOut) {
                    $rentAt = fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate);
                    $soldAt = fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate);
                } elseif ($status == PropertyStatus::Completed) {
                    $soldAt = $hasSell ? fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate) : null;
                    $rentAt = $hasRent ? fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate) : null;
                    $greaterDate = $soldAt > $rentAt ? $soldAt : $rentAt;
                    $completedAt = fake()->dateTimeBetween(startDate: $greaterDate, endDate: Carbon::parse($greaterDate)->addMonth());
                }
            }

            $property->update([
                'posted_at' => $postedAt,
                'sold_at' => $soldAt,
                'rent_at' => $rentAt,
                'completed_at' => $completedAt,
                'status' => $status,
            ]);

            PropertyDocument::factory(rand(1, 5))->create([
                'property_id' => $property->id,
            ]);

            $tags = \App\Models\Tag::inRandomOrder()->limit(fake()->randomNumber(1))->get();

            foreach ($tags as $tag) {
                $property->tags()->attach($tag->id);
            }

            Rateable::factory(rand(0, 10))->create([
                'rateable_id' => $property->id,
                'rateable_type' => $property->getMorphClass(),
            ]);

            Viewable::factory(rand(5, 30))->create([
                'viewable_id' => $property->id,
                'viewable_type' => $property->getMorphClass(),
            ]);
        });
    }
}
