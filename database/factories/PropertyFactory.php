<?php

namespace Database\Factories;

use App\Enums\AreaType;
use App\Enums\AreaUnit;
use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Enums\PropertyAcquisitionType;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Lead;
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

        $township = Township::whereRelation('state', 'slug', 'yangon')->get()->random();
        $address = [
            'en' => $this->faker->streetAddress() . ', ' . $township->full_address_detail['en'],
            'my' => $this->faker->streetAddress() . ', ' . $township->full_address_detail['my'],
        ];

        return [
            'township_id' => $township,
            'title' => [
                'en' => $this->faker->sentence,
                'my' => $this->faker->sentence,
            ],
            'description' => [
                'en' => $this->faker->paragraph,
                'my' => $this->faker->paragraph,
            ],
            'address' => $address,
            'type' => get_weighted_random_element([
                PropertyType::Independent->value => 20,
                PropertyType::Condo->value => 20,
                PropertyType::MiniCondo->value => 20,
                PropertyType::Apartment->value => 25,
                PropertyType::Commercial->value => 5,
                PropertyType::Land->value => 5,
                PropertyType::Storage->value => 5,
            ]),
            'status' => get_weighted_random_element([
                PropertyStatus::Draft->value => 30,
                PropertyStatus::Posted->value => 30,
                PropertyStatus::Purchased->value => 20,
                PropertyStatus::Completed->value => 20,
            ]),
            'latitude' => $this->faker->latitude(),
            'cover_image' => $this->faker->imageUrl(),
            'longitude' => $this->faker->longitude(),
            'area_type' => $this->faker->randomElement(AreaType::cases()),
            'square_feet' => $this->faker->randomNumber(3),
            'acquisition_type' => $this->faker->randomElement(PropertyAcquisitionType::cases()),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Property $property) {
            $areaDetails = $this->getAreaDetails($property);
            $dateDetails = $this->setDateDetails($property);

            $imageDetails = $this->createPropertyImages($property);
            $this->attachTags($property);
            $bathroomsCount = $this->createBedroomTypes($property);
            $this->createRateable($property);
            $viewsCount = $this->createViewable($property);
            $this->createCustomerLeads($property);
            $customer = $this->createCustomer($property);

            $priceDetails = $this->fakePriceDetails($property->acquisition_type, status: $property->status);

            $property->update(array_merge($dateDetails, $priceDetails, $areaDetails, $imageDetails, [
                'bathrooms_count' => $bathroomsCount,
                'views_count' => $viewsCount,
                'customer_id' => $customer ? $customer->id : null,
            ]));
        });
    }

    protected function createCustomerLeads(Property $property)
    {
        if ($property->status == PropertyStatus::Draft) {
            return null;
        }

        $leadInterest = $property->acquisition_type == PropertyAcquisitionType::Rent ? LeadInterest::Renting : LeadInterest::Buying;

        $count = rand(0, 10);

        while ($count > 0) {
            Lead::factory(get_weighted_random_element([
                0 => 50,
                rand(1, 3) => 40,
                rand(3, 10) => 15,
                rand(10, 20) => 5,
            ]))->create([
                'status' => get_weighted_random_element([
                    LeadStatus::Assigned->value => 40,
                    LeadStatus::Contacted->value => 40,
                    LeadStatus::FollowedUp->value => 10,
                    LeadStatus::UnderNegotiation->value => 10,
                ]),
                'property_id' => $property->id,
                'property_type' => $property->type,
                'interest' => $leadInterest,
                'is_owner' => false,
            ]);

            $count--;
        }
    }

    protected function createCustomer(Property $property): ?Lead
    {
        if ($property->status == PropertyStatus::Draft || $property->status == PropertyStatus::Posted) {
            return null;
        }

        $lead = $property->leads()->inRandomOrder()->whereIn('status', [LeadStatus::FollowedUp->value, LeadStatus::UnderNegotiation->value])->first();

        if ($lead) {
            $lead->update([
                'status' => LeadStatus::Converted,
            ]);
        } else {
            $lead = Lead::factory()->create([
                'status' => LeadStatus::Converted,
                'property_id' => $property->id,
                'property_type' => $property->type,
                'interest' => $property->acquisition_type == PropertyAcquisitionType::Rent ? LeadInterest::Renting : LeadInterest::Buying,
                'is_owner' => false,
            ]);
        }

        return $lead;
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

    protected function setDateDetails(Property $property)
    {
        $postedAt = null;
        $purchasedAt = null;
        $completedAt = null;

        if ($property->status != PropertyStatus::Draft) {
            $postedAt = fake()->dateTimeBetween(startDate: $property->created_at, endDate: Carbon::parse($property->created_at)->addMonth(1));
            $endDate = Carbon::parse($postedAt)->addMonth(2);

            if ($property->status == PropertyStatus::Purchased) {
                $purchasedAt = fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate);
            } elseif ($property->status == PropertyStatus::Completed) {
                $purchasedAt = fake()->dateTimeBetween(startDate: $postedAt, endDate: $endDate);
                $completedAt = fake()->dateTimeBetween(startDate: $purchasedAt, endDate: Carbon::parse($purchasedAt)->addMonth());
            }
        }

        return [
            'posted_at' => $postedAt,
            'purchased_at' => $purchasedAt,
            'completed_at' => $completedAt,
        ];
    }

    protected function createPropertyImages(Property $property): array
    {
        $count = rand(1, 5);
        $images = [];
        $coverImage = $property->cover_image;

        $coverImage = match ($property->type) {
            PropertyType::Apartment, PropertyType::Condo, PropertyType::MiniCondo => 'images/property/condo' . rand(1, 34) . '.jpg',
                // PropertyType::Independent => 'images/property/independent'.rand(1, 29).'.jpg',
            default => 'images/property/independent' . rand(1, 29) . '.jpg',
        };

        while ($count > 0) {
            $images[] = 'images/property/condo' . rand(1, 34) . '.jpg';
            // $images[] = match ($property->type) {
            //     PropertyType::Apartment, PropertyType::Condo, PropertyType::MiniCondo, PropertyType::Independent => 'images/property/condo'.rand(1, 36).'.jpg',
            //     default => $this->faker->imageUrl()
            // };
            $count--;
        }

        return [
            'images' => $images,
            'cover_image' => $coverImage,
        ];
    }

    protected function attachTags(Property $property)
    {
        $tags = \App\Models\Tag::inRandomOrder()->limit(rand(1, 5))->pluck('id');
        $property->tags()->attach($tags);
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
        if ($property->status == PropertyStatus::Draft) {
            return 0;
        }

        Rateable::factory(rand(0, 10))->create([
            'rateable_id' => $property->id,
            'rateable_type' => $property->getMorphClass(),
        ]);
    }

    protected function createViewable(Property $property): int
    {
        if ($property->status == PropertyStatus::Draft) {
            return 0;
        }

        $count = rand(5, 30);

        Viewable::factory($count)->create([
            'viewable_id' => $property->id,
            'viewable_type' => $property->getMorphClass(),
        ]);

        return $count;
    }

    public static function fakePriceDetails(PropertyAcquisitionType $acquisitionType, ?PropertyPriceType $priceType = null, ?PropertyStatus $status = null)
    {
        $priceFrom = 0;
        $priceTo = 0;
        $ownerCommission = 0;
        $customerCommission = 0;
        $negotiable = false;
        $purchasedPrice = null;
        $purchasedCommission = null;

        $priceType = $priceType ?? fake()->randomElement(PropertyPriceType::cases());
        $ownerCommission = 1;
        $negotiable = fake()->boolean();
        $priceStep = $acquisitionType == PropertyAcquisitionType::Sale ? 5000000 : 50000;

        if ($acquisitionType == PropertyAcquisitionType::Sale) {
            $priceFrom = get_stepped_random_number(60000000, 600000000, $priceStep);
        } else {
            $priceFrom = get_stepped_random_number(200000, 3000000, $priceStep);
            $customerCommission = get_weighted_random_element([
                50 => 15,
                100 => 70,
                200 => 15,
            ]);
        }

        if ($priceType == PropertyPriceType::Range) {
            $priceTo = get_stepped_random_number($priceFrom, $priceFrom * 2, $priceStep);
        }

        if ($status && in_array($status, [PropertyStatus::Purchased, PropertyStatus::Completed])) {
            $purchasedPrice = $priceType === PropertyPriceType::Fix ? $priceFrom : get_stepped_random_number($priceFrom, $priceTo, $priceStep);

            $purchasedCommission = ($purchasedPrice * $customerCommission / 100) + ($purchasedPrice * $ownerCommission / 100);
        }

        return [
            'price_type' => $priceType,
            'price_from' => $priceFrom,
            'price_to' => $priceTo,
            'negotiable' => $negotiable,
            'owner_commission' => $ownerCommission,
            'customer_commission' => $customerCommission,
            'purchased_price' => $purchasedPrice,
            'purchased_commission' => $purchasedCommission,
        ];
    }
}
