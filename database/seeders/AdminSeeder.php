<?php

namespace Database\Seeders;

use App\Enums\Lead\LeadType;
use App\Enums\PropertyType;
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
            'email' => 'naungyehtet.zonenextadmin@gmail.com',
            'password' => bcrypt('admin@123'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('Super admin');

        $admin = Admin::factory()->role('Agent')->create([
            'name' => 'Naung Ye Htet (Fallback AG)',
            'email' => 'naungyehtet.fallbackagent@gmail.com',
            'phone' => '09775330805',
            'password' => bcrypt('admin@123'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'preferred_lead_types' => null,
            'preferred_property_types' => null,
            'preferred_townships' => null
        ]);

        $admin = Admin::factory()->role('Agent')->create([
            'name' => 'Naung Ye Htet (Seller AG)',
            'email' => 'naungyehtet.selleragent@gmail.com',
            'phone' => '09775330805',
            'password' => bcrypt('admin@123'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'preferred_lead_types' => [LeadType::Sellers],
            'preferred_property_types' => [PropertyType::Apartment, PropertyType::Condo],
            'preferred_townships' => null
        ]);

        $admin = Admin::factory()->role('Agent')->create([
            'name' => 'Naung Ye Htet (Renter AG)',
            'email' => 'naungyehtet.renteragent@gmail.com',
            'phone' => '09775330805',
            'password' => bcrypt('admin@123'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'preferred_lead_types' => [LeadType::Landloards],
            'preferred_property_types' => [PropertyType::Independent, PropertyType::Commercial],
            'preferred_townships' => null
        ]);

        $admin = Admin::factory()->role('Agent')->create([
            'name' => 'Naung Ye Htet (Buyer AG)',
            'email' => 'naungyehtet.buyeragent@gmail.com',
            'phone' => '09775330805',
            'password' => bcrypt('admin@123'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'preferred_lead_types' => [LeadType::Buyers],
            'preferred_property_types' => [PropertyType::MiniCondo, PropertyType::Commercial],
            'preferred_townships' => null
        ]);
    }
}
