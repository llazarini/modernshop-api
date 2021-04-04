<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_types')->insert([
            [
                'name' => 'Super Administrador',
                'slug' => 'super_admin'
            ],
            [
                'name' => 'Administrador',
                'slug' => 'admin'
            ],
            [
                'name' => 'Client',
                'slug' => 'client'
            ]
        ]);
    }
}
