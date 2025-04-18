<?php

namespace Database\Factories;

use App\Enums\Language;
use App\Enums\Lead\LeadType;
use App\Enums\PropertyType;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $townships = \App\Models\Township::whereRelation('state', 'slug', 'yangon')->inRandomOrder()->limit(rand(5, 20))->pluck('id')->toArray();

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'email_verified_at' => $this->faker->dateTime(),
            'phone' => $this->faker->phoneNumber(),
            'phone_verified_at' => $this->faker->dateTime(),
            'password' => bcrypt('password'),
            'image' => $this->faker->imageUrl(),
            'language' => $this->faker->randomElement(Language::cases()),
            'preferred_notification_channels' => $this->faker->optional()->randomElements(['email', 'sms']),
            'preferred_lead_types' => $this->faker->optional()->randomElements(LeadType::cases()),
            'preferred_property_types' => get_weighted_random_elements([
                PropertyType::Independent->value => 15,
                PropertyType::Condo->value => 15,
                PropertyType::MiniCondo->value => 15,
                PropertyType::Apartment->value => 15,
                PropertyType::Commercial->value => 8,
                PropertyType::Land->value => 8,
                PropertyType::Storage->value => 9,
                null => 15,
            ]),
            'preferred_townships' => fake()->randomElement([null, $townships]),
        ];
    }

    public function role(string $roleName): Factory
    {
        return $this->afterCreating(function (Admin $admin) use ($roleName) {
            $admin->assignRole($roleName);
        });
    }
}
