<?php

namespace Database\Factories;

use App\Models\Agent;
use App\Models\AgentProperty;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentPropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AgentProperty::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'agent_id' => Agent::all()->random(),
            'property_id' => Property::factory(),
        ];
    }
}
