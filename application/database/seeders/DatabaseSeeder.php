<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PaymentTypesSeeder::class,
            UserTypesSeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
            PaymentStatusesSeeder::class,
            StatesSeeder::class,
            CitySeeder::class,
        ]);
    }
}
