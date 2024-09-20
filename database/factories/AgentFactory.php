<?php

namespace Database\Factories;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Agent::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'email_verified_at' => $this->faker->dateTime(),
            'phone' => $this->faker->phoneNumber(),
            'phone_verified_at' => $this->faker->dateTime(),
            'password' => $this->faker->password(),
            'image' => $this->faker->word(),
        ];
    }
}
