<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::create([
            'name' => 'Naung Ye Htet',
            'email' => 'naungyehtet@zonenext.com',
            'password' => bcrypt('admin@123'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('Super admin');

        $admin = Admin::factory()->role('Agent')->create([
            'name' => 'Naung Ye Htet',
            'email' => 'naungyehtet.zonenextagent@gmail.com',
            'phone' => '09775330805',
            'password' => bcrypt('admin@123'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
    }
}
