<?php

namespace Database\Factories;

use App\Enums\AreaType;
use App\Enums\AreaUnit;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Property;
use App\Models\PropertyBedroomType;
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
        $createdAt = $this->faker->dateTimeBetween(now()->subMonths(12), now()->subMonths(3));
        $isSaleable = $this->faker->boolean();

        return [
            'township_id' => Township::whereRelation('state', 'slug', 'yangon')->get()->random(),
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
            'cover_image' => $this->faker->imageUrl(),
            'longitude' => $this->faker->longitude(),
            'area_type' => $this->faker->randomElement(AreaType::cases()),
            'square_feet' => $this->faker->randomNumber(3),
            'is_saleable' => $isSaleable,
            'is_rentable' => $isSaleable ? $this->faker->boolean() : true,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Property $property) {
            $areaDetails = $this->getAreaDetails($property);
            $statuses = $this->determineStatuses($property);
            $status = fake()->randomElement($statuses);

            $dateDetails = $this->setDateDetails($property, $status);

            $images = $this->createPropertyImages($property);
            $this->attachTags($property);
            $bathroomsCount = $this->createBedroomTypes($property);
            $this->createRateable($property);
            $this->createViewable($property);

            $priceDetails = $this->setPriceDetails($property, $status);

            $property->update(array_merge($dateDetails, $priceDetails, $areaDetails, [
                'bathrooms_count' => $bathroomsCount,
                'status' => $status,
                'images' => $images,
            ]));
        });
    }

    protected function getAreaDetails(Property $property)
    {
        $width = null;
        $length = null;
        $squareFeet = null;
        $area = null;
        $areaUnit = null;

        if ($property->area_type == AreaType::LengthWidth) {
            $width = fake()->randomNumber(2);
            $length = fake()->randomNumber(2);
            $squareFeet = $width * $length;
        } else {
            $areaUnit = fake()->randomElement(AreaUnit::cases());
            $area = $areaUnit == AreaUnit::Acre ? rand(1, 5) : fake()->randomNumber(3);
            $squareFeet = $areaUnit == AreaUnit::Acre ? $area * 43560 : $area;
        }

        return [
            'width' => $width,
            'length' => $length,
            'area_unit' => $areaUnit,
            'area' => $area,
            'square_feet' => $squareFeet,
        ];
    }

    protected function determineStatuses(Property $property)
    {
        $statuses = [
            PropertyStatus::Draft,
            PropertyStatus::Posted,
            PropertyStatus::Completed,
        ];

        if ($property->is_saleable && $property->is_rentable) {
            $statuses[] = PropertyStatus::SoldOut;
            $statuses[] = PropertyStatus::Rented;
        }

        return $statuses;
    }

    protected function setDateDetails(Property $property, $status)
    {
        $postedAt = null;
        $soldAt = null;
        $rentAt = null;
        $completedAt = null;

        if ($status != PropertyStatus::Draft) {
            $postedAt = fake()->dateTimeBetween(startDate: $property->created_at, endDate: Carbon::parse($property->created_at)->addMonth(1));
            $endDate = Carbon::parse($postedAt)->addMonth(2);

            if ($status == PropertyStatus::SoldOut) {
                $soldAt = fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate);
            } elseif ($status == PropertyStatus::Rented) {
                $rentAt = fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate);
            } elseif ($status == PropertyStatus::Completed) {
                $soldAt = $property->is_saleable ? fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate) : null;
                $rentAt = $property->is_rentable ? fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate) : null;
                $greaterDate = $soldAt > $rentAt ? $soldAt : $rentAt;
                $completedAt = fake()->dateTimeBetween(startDate: $greaterDate, endDate: Carbon::parse($greaterDate)->addMonth());
            }
        }

        return [
            'posted_at' => $postedAt,
            'sold_at' => $soldAt,
            'rent_at' => $rentAt,
            'completed_at' => $completedAt,
        ];
    }

    protected function createPropertyImages(Property $property): array
    {
        $count = fake()->randomNumber(1);
        $images = [];

        while ($count > 0) {
            $images[] = $this->faker->imageUrl();
            $count--;
        }

        return $images;
    }

    protected function attachTags(Property $property)
    {
        $tags = \App\Models\Tag::inRandomOrder()->limit(fake()->randomNumber(1))->get();
        foreach ($tags as $tag) {
            $property->tags()->attach($tag->id);
        }
    }

    protected function createBedroomTypes(Property $property)
    {
        $bathroomsCount = 0;
        if (in_array($property->type, [PropertyType::Apartment, PropertyType::Condo, PropertyType::MiniCondo, PropertyType::Independent])) {
            $bedroomTypes = \App\Models\BedroomType::inRandomOrder()->limit(fake()->randomNumber(rand(0, 3)))->get();
            $bathroomsCount = fake()->randomNumber(1);

            foreach ($bedroomTypes as $bedroomType) {
                PropertyBedroomType::create([
                    'bedroom_type_id' => $bedroomType->id,
                    'property_id' => $property->id,
                    'quantity' => rand(1, 2),
                ]);
            }
        }

        return $bathroomsCount;
    }

    protected function createRateable(Property $property)
    {
        Rateable::factory(rand(0, 10))->create([
            'rateable_id' => $property->id,
            'rateable_type' => $property->getMorphClass(),
        ]);
    }

    protected function createViewable(Property $property)
    {
        Viewable::factory(rand(5, 30))->create([
            'viewable_id' => $property->id,
            'viewable_type' => $property->getMorphClass(),
        ]);
    }

    protected function setPriceDetails(Property $property, PropertyStatus $status)
    {
        $salePriceDetails = $this->getSalePriceDetails($property, $status);
        $rentPriceDetails = $this->getRentPriceDetails($property, $status);

        return array_merge($salePriceDetails, $rentPriceDetails);
    }

    protected function getSalePriceDetails(Property $property, PropertyStatus $status)
    {
        $salePriceType = null;
        $salePriceFrom = 0;
        $salePriceTo = 0;
        $saleOwnerCommission = 0;
        $saleNegotiable = false;
        $soldPrice = null;
        $soldCommission = null;

        if ($property->is_saleable) {
            $salePriceType = fake()->randomElement(PropertyPriceType::cases());
            $saleOwnerCommission = fake()->randomNumber(1);
            $saleNegotiable = fake()->boolean();
            $salePriceFrom = get_stepped_random_number(60000000, 600000000 / 2, 5000000);

            if ($salePriceType == PropertyPriceType::Range) {
                $salePriceTo = get_stepped_random_number($salePriceFrom, $salePriceFrom * 2, 5000000);
            }

            if (in_array($status, [PropertyStatus::SoldOut, PropertyStatus::Completed])) {
                if ($salePriceType === PropertyPriceType::Fix) {
                    $soldPrice = $salePriceFrom;
                } else {
                    $soldPrice = get_stepped_random_number($salePriceFrom, $salePriceTo, 5000000);
                }

                $soldCommission = $soldPrice * $saleOwnerCommission / 100;
            }
        }

        return [
            'sale_price_type' => $salePriceType,
            'sale_price_from' => $salePriceFrom,
            'sale_price_to' => $salePriceTo,
            'sale_negotiable' => $saleNegotiable,
            'sale_owner_commission' => $saleOwnerCommission,
            'sold_price' => $soldPrice,
            'sold_commission' => $soldCommission,
        ];
    }

    protected function getRentPriceDetails(Property $property, PropertyStatus $status)
    {
        $rentPriceType = null;
        $rentPriceFrom = 0;
        $rentPriceTo = 0;
        $rentOwnerCommission = 0;
        $rentCustomerCommission = 0;
        $rentNegotiable = false;
        $rentPrice = null;
        $rentCommission = null;

        if ($property->is_rentable) {
            $rentPriceType = fake()->randomElement(PropertyPriceType::cases());
            $rentOwnerCommission = fake()->randomElement([30, 50, 70, 100, 200]);
            $rentCustomerCommission = fake()->randomElement([30, 50, 70, 100, 200]);
            $rentNegotiable = fake()->boolean();
            $rentPriceFrom = get_stepped_random_number(300000, 6000000 / 2, 50000);

            if ($rentPriceType == PropertyPriceType::Range) {
                $rentPriceTo = get_stepped_random_number($rentPriceFrom, $rentPriceFrom * 2, 50000);
            }

            if (in_array($status, [PropertyStatus::Rented, PropertyStatus::Completed])) {
                if ($rentPriceType === PropertyPriceType::Fix) {
                    $rentPrice = $rentPriceFrom;
                } else {
                    $rentPrice = get_stepped_random_number($rentPriceFrom, $rentPriceTo, 50000);
                }

                $rentCommission = ($rentPrice * $rentCustomerCommission / 100) + ($rentPrice * $rentOwnerCommission / 100);
            }
        }

        return [
            'rent_price_type' => $rentPriceType,
            'rent_price_from' => $rentPriceFrom,
            'rent_price_to' => $rentPriceTo,
            'rent_negotiable' => $rentNegotiable,
            'rent_owner_commission' => $rentOwnerCommission,
            'rent_customer_commission' => $rentCustomerCommission,
            'rented_price' => $rentPrice,
            'rented_commission' => $rentCommission,
        ];
    }
}
