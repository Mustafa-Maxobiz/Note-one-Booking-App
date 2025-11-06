<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Package 1',
                'description' => '1 Month Access to Online Lessons',
                'duration_months' => 1,
                'price' => 100.00,
                'is_active' => true,
            ],
            [
                'name' => 'Package 2',
                'description' => '2 Months Access to Online Lessons',
                'duration_months' => 2,
                'price' => 200.00,
                'is_active' => true,
            ],
            [
                'name' => 'Package 3',
                'description' => '3 Months Access to Online Lessons',
                'duration_months' => 3,
                'price' => 100.00,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
