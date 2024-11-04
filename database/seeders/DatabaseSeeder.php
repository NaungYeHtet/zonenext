<?php

namespace Database\Seeders;

use App\Enums\Lead\LeadInterest;
use App\Models\Lead;
use App\Models\Project;
use App\Models\State;
use App\Models\Township;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $states = json_decode(File::get(base_path('database/seeders/data/states.json')), true);

        foreach ($states as $state) {
            State::create($state);
        }

        $townships = json_decode(File::get(base_path('database/seeders/data/townships.json')), true);

        foreach ($townships as $township) {
            Township::create($township);
        }

        $this->call([
            FaqSeeder::class,
            TagSeeder::class,
            BedroomTypeSeeder::class,
            ShieldSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
        ]);

        $this->command->info('Seeding Admin factory....');
        \App\Models\Admin::factory(rand(20, 50))
            ->role('Agent')
            ->create();
        $this->command->info('Seeding Lead Sellers....');
        Lead::factory()
            ->count(rand(100, 300))
            ->state(function (array $attributes) {
                return [
                    'is_owner' => true,
                    'interest' => LeadInterest::Selling,
                ];
            })
            ->create();
        $this->command->info('Seeding Lead Landloards....');
        Lead::factory()
            ->count(rand(300, 500))
            ->state(function (array $attributes) {
                return [
                    'is_owner' => true,
                    'interest' => LeadInterest::Renting,
                ];
            })
            ->create();
        // $this->command->info('Seeding Property factory....');
        // $this->call([
        //     PropertySeeder::class,
        // ]);
        $this->command->info('Seeding Project factory....');
        Project::factory(50)->create();

        $this->call([
            GroupSeeder::class,
        ]);
    }
}
