<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'user_id' => User::factory(),
            'date' => $this->faker->dateTime(),
            'status' => $this->faker->regexify('[A-Za-z0-9]{30}'),
        ];
    }
}
