<?php

namespace Database\Factories;

use App\Models\Viewable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ViewableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Viewable::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'viewer_id' => $this->faker->uuid(),
            'user_agent' => $this->faker->userAgent(),
            'ip_address' => $this->faker->ipv4(),
        ];
    }
}
