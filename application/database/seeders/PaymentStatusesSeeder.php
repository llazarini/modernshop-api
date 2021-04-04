<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = array(
            array(
                "id" => 1,
                "name" => "Aguardando",
                "slug" => "waiting",
            ),
            array(
                "id" => 2,
                "name" => "Pago",
                "slug" => "paid",
            ),
            array(
                "id" => 3,
                "name" => "Erro",
                "slug" => "error",
            ),
        );

        DB::table('payment_statuses')->insert($plans);
    }
}
