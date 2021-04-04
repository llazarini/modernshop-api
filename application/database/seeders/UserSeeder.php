<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'company_id' => 1,
            'user_type_id' => 1,
            'name' => 'Administrador',
            'email' => 'admin@flecha.com',
            'password' => Hash::make('1q2w3e4r')
        ]);
    }
}
