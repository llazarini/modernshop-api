<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = array(
            array(
                "id" => 1,
                "name" => "Cartão de Crédito",
                "slug" => "credit_card",
            ),
            array(
                "id" => 2,
                "name" => "Pix",
                "slug" => "pix",
            ),
            array(
                "id" => 3,
                "name" => "Boleto Bancário",
                "slug" => "boleto",
            ),
        );
        DB::table('payment_types')->insert($items);
    }
}
