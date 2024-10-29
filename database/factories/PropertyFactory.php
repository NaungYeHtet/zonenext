<?php

namespace Database\Factories;

use App\Enums\AreaType;
use App\Enums\AreaUnit;
use App\Enums\Lead\LeadInterest;
use App\Enums\LeadStatus;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Lead;
use App\Models\Property;
use App\Models\PropertyBedroomType;
use App\Models\Rateable;
use App\Models\Township;
use App\Models\User;
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

        $township = Township::whereRelation('state', 'slug', 'yangon')->get()->random();
        $address = [
            'en' => $this->faker->streetAddress().', '.$township->full_address_detail['en'],
            'my' => $this->faker->streetAddress().', '.$township->full_address_detail['my'],
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

            $imageDetails = $this->createPropertyImages($property);
            $this->attachTags($property);
            $this->attachAgents($property, $status);
            $bathroomsCount = $this->createBedroomTypes($property);
            $this->createRateable($property, $status);
            $viewsCount = $this->createViewable($property, $status);
            $ownerDetails = $this->createOwner($property, $status);
            $customerDetails = $this->createCustomer($property, $status);

            $priceDetails = $this->setPriceDetails($property, $status);

            $property->update(array_merge($dateDetails, $priceDetails, $areaDetails, $imageDetails, $ownerDetails, $customerDetails, [
                'bathrooms_count' => $bathroomsCount,
                'views_count' => $viewsCount,
                'status' => $status,
            ]));
        });
    }

    protected function createOwner(Property $property, PropertyStatus $status)
    {
        $type = $this->faker->randomElement([null, 'user', 'lead']);
        $owner = match ($type) {
            'user' => User::all()->random(),
            'lead' => Lead::where('property_type', $property->type->value)->where('is_owner', true)->get()->random(),
            default => null,
        };

        return [
            'owner_type' => $type,
            'owner_id' => $owner ? $owner->id : null,
        ];
    }

    protected function createCustomer(Property $property, PropertyStatus $status)
    {
        if ($status == PropertyStatus::Draft || $status == PropertyStatus::Posted) {
            return [];
        }

        if ($property->is_saleable && ($status === PropertyStatus::SoldOut || $status === PropertyStatus::Completed)) {
            $type = $this->faker->randomElement([null, 'user', 'lead']);

            $customer = match ($type) {
                'user' => User::all()->random(),
                'lead' => Lead::where('property_type', $property->type->value)->where('interest', LeadInterest::Buying->value)->whereIn('status', [LeadStatus::Converted->value, LeadStatus::Closed->value])->where('is_owner', false)->get()->random(),
                default => null,
            };

            return [
                'customer_sale_type' => $type,
                'customer_sale_id' => $customer ? $customer->id : null,
            ];
        }

        if ($property->is_rentable && ($status === PropertyStatus::Rented || $status === PropertyStatus::Completed)) {
            $type = $this->faker->randomElement([null, 'user', 'lead']);

            $customer = match ($type) {
                'user' => User::all()->random(),
                'lead' => Lead::where('property_type', $property->type->value)->where('interest', LeadInterest::Renting->value)->where('is_owner', false)->get()->random(),
                default => null,
            };

            return [
                'customer_rent_type' => $type,
                'customer_rent_id' => $customer ? $customer->id : null,
            ];
        }
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
            'rented_at' => $rentAt,
            'completed_at' => $completedAt,
        ];
    }

    protected function createPropertyImages(Property $property): array
    {
        $count = rand(1, 5);
        $images = [];
        $coverImage = $property->cover_image;

        $coverImage = match ($property->type) {
            PropertyType::Apartment, PropertyType::Condo, PropertyType::MiniCondo => 'images/property/condo'.rand(1, 36).'.jpg',
            PropertyType::Independent => 'images/property/independent'.rand(1, 29).'.jpg',
            default => $coverImage
        };

        while ($count > 0) {
            $images[] = match ($property->type) {
                PropertyType::Apartment, PropertyType::Condo, PropertyType::MiniCondo, PropertyType::Independent => 'images/property/condo'.rand(1, 36).'.jpg',
                default => $this->faker->imageUrl()
            };
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

    protected function attachAgents(Property $property, PropertyStatus $status)
    {
        if ($status == PropertyStatus::Draft) {
            return;
        }

        $agents = \App\Models\Agent::inRandomOrder()->limit(rand(1, 3))->pluck('id');
        $property->agents()->attach($agents);
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

    protected function createRateable(Property $property, PropertyStatus $status)
    {
        if ($status == PropertyStatus::Draft) {
            return 0;
        }

        Rateable::factory(rand(0, 10))->create([
            'rateable_id' => $property->id,
            'rateable_type' => $property->getMorphClass(),
        ]);
    }

    protected function createViewable(Property $property, PropertyStatus $status): int
    {
        if ($status == PropertyStatus::Draft) {
            return 0;
        }

        $count = rand(5, 30);

        Viewable::factory($count)->create([
            'viewable_id' => $property->id,
            'viewable_type' => $property->getMorphClass(),
        ]);

        return $count;
    }

    protected function setPriceDetails(Property $property, PropertyStatus $status)
    {
        $salePriceDetails = $this->fakeSalePriceDetails($status, $property->is_saleable);
        $rentPriceDetails = $this->fakeRentPriceDetails($status, $property->is_rentable);

        return array_merge($salePriceDetails, $rentPriceDetails);
    }

    public static function fakeSalePriceDetails(?PropertyStatus $status = null, bool $generate = true, ?PropertyPriceType $salePriceType = null)
    {
        $salePriceFrom = 0;
        $salePriceTo = 0;
        $sellerCommission = 0;
        $saleNegotiable = false;
        $soldPrice = null;
        $soldCommission = null;

        if ($generate) {
            $salePriceType = $salePriceType ?? fake()->randomElement(PropertyPriceType::cases());
            $sellerCommission = fake()->randomNumber(1);
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

                $soldCommission = $soldPrice * $sellerCommission / 100;
            }
        }

        return [
            'sale_price_type' => $salePriceType,
            'sale_price_from' => $salePriceFrom,
            'sale_price_to' => $salePriceTo,
            'sale_negotiable' => $saleNegotiable,
            'seller_commission' => $sellerCommission,
            'sold_price' => $soldPrice,
            'sold_commission' => $soldCommission,
        ];
    }

    public static function fakeRentPriceDetails(?PropertyStatus $status = null, bool $generate = true, ?PropertyPriceType $rentPriceType = null)
    {
        $rentPriceFrom = 0;
        $rentPriceTo = 0;
        $landlordCommission = 0;
        $renterCommission = 0;
        $rentNegotiable = false;
        $rentPrice = null;
        $rentCommission = null;

        if ($generate) {
            $rentPriceType = $rentPriceType ?? fake()->randomElement(PropertyPriceType::cases());
            $landlordCommission = fake()->randomElement([30, 50, 70, 100, 200]);
            $renterCommission = fake()->randomElement([30, 50, 70, 100, 200]);
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

                $rentCommission = ($rentPrice * $renterCommission / 100) + ($rentPrice * $landlordCommission / 100);
            }
        }

        return [
            'rent_price_type' => $rentPriceType,
            'rent_price_from' => $rentPriceFrom,
            'rent_price_to' => $rentPriceTo,
            'rent_negotiable' => $rentNegotiable,
            'landlord_commission' => $landlordCommission,
            'renter_commission' => $renterCommission,
            'rented_price' => $rentPrice,
            'rented_commission' => $rentCommission,
        ];
    }
}
