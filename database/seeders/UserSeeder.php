<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->unverified()->create([
            'name' => 'Naung Ye Htet',
            'email' => 'naungyehtet.zonenextuser@gmail.com',
            'password' => bcrypt('user@123'),
        ]);

        User::factory()
            ->count(100)->create();
    }
}
