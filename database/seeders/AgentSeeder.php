<?php

namespace Database\Seeders;

use App\Enums\Language;
use App\Models\Agent;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Agent::create([
            'name' => 'Agent',
            'email' => 'agent@zonenext.com',
            'phone' => '+959775330805',
            'password' => bcrypt('agent@123'),
            'phone_verified_at' => now(),
            'language' => Language::Myanmar,
        ]);
    }
}
