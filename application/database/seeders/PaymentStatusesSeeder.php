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
                "name" => "Processando",
                "slug" => "processing",
            ),
            array(
                "id" => 2,
                "name" => "Autorizado",
                "slug" => "authorized",
            ),
            array(
                "id" => 3,
                "name" => "Pago",
                "slug" => "paid",
            ),
            array(
                "id" => 4,
                "name" => "Estornado",
                "slug" => "refunded",
            ),
            array(
                "id" => 5,
                "name" => "Agardando Pagamento",
                "slug" => "waiting_payment",
            ),
            array(
                "id" => 6,
                "name" => "Estorno solicitado",
                "slug" => "pending_refund",
            ),
            array(
                "id" => 7,
                "name" => "Recusado",
                "slug" => "refused",
            ),
            array(
                "id" => 8,
                "name" => "Chargeback",
                "slug" => "chargedback",
            ),
            array(
                "id" => 9,
                "name" => "Analisando Transação",
                "slug" => "analyzing",
            ),
            array(
                "id" => 10,
                "name" => "Pendente de Revisão",
                "slug" => "pending_review",
            ),
        );
        DB::table('payment_statuses')->insert($plans);
    }
}
