<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('states')->insert(array (
            0 =>
                array (
                    'id' => '1',
                    'name' => 'Acre',
                    'code' => 'AC',
                    'iso' => '12',
                    'slug' => 'acre',
                    'population' => '816687',
                ),
            1 =>
                array (
                    'id' => '2',
                    'name' => 'Alagoas',
                    'code' => 'AL',
                    'iso' => '27',
                    'slug' => 'alagoas',
                    'population' => '3358963',
                ),
            2 =>
                array (
                    'id' => '3',
                    'name' => 'Amazonas',
                    'code' => 'AM',
                    'iso' => '13',
                    'slug' => 'amazonas',
                    'population' => '4001667',
                ),
            3 =>
                array (
                    'id' => '4',
                    'name' => 'Amapá',
                    'code' => 'AP',
                    'iso' => '16',
                    'slug' => 'amapa',
                    'population' => '782295',
                ),
            4 =>
                array (
                    'id' => '5',
                    'name' => 'Bahia',
                    'code' => 'BA',
                    'iso' => '29',
                    'slug' => 'bahia',
                    'population' => '15276566',
                ),
            5 =>
                array (
                    'id' => '6',
                    'name' => 'Ceará',
                    'code' => 'CE',
                    'iso' => '23',
                    'slug' => 'ceara',
                    'population' => '8963663',
                ),
            6 =>
                array (
                    'id' => '7',
                    'name' => 'Distrito Federal',
                    'code' => 'DF',
                    'iso' => '53',
                    'slug' => 'distrito-federal',
                    'population' => '2977216',
                ),
            7 =>
                array (
                    'id' => '8',
                    'name' => 'Espírito Santo',
                    'code' => 'ES',
                    'iso' => '32',
                    'slug' => 'espirito-santo',
                    'population' => '3973697',
                ),
            8 =>
                array (
                    'id' => '9',
                    'name' => 'Goiás',
                    'code' => 'GO',
                    'iso' => '52',
                    'slug' => 'goias',
                    'population' => '6695855',
                ),
            9 =>
                array (
                    'id' => '10',
                    'name' => 'Maranhão',
                    'code' => 'MA',
                    'iso' => '21',
                    'slug' => 'maranhao',
                    'population' => '6954036',
                ),
            10 =>
                array (
                    'id' => '11',
                    'name' => 'Minas Gerais',
                    'code' => 'MG',
                    'iso' => '31',
                    'slug' => 'minas-gerais',
                    'population' => '20997560',
                ),
            11 =>
                array (
                    'id' => '12',
                    'name' => 'Mato Grosso do Sul',
                    'code' => 'MS',
                    'iso' => '50',
                    'slug' => 'mato-grosso-do-sul',
                    'population' => '2682386',
                ),
            12 =>
                array (
                    'id' => '13',
                    'name' => 'Mato Grosso',
                    'code' => 'MT',
                    'iso' => '51',
                    'slug' => 'mato-grosso',
                    'population' => '3305531',
                ),
            13 =>
                array (
                    'id' => '14',
                    'name' => 'Pará',
                    'code' => 'PA',
                    'iso' => '15',
                    'slug' => 'para',
                    'population' => '8272724',
                ),
            14 =>
                array (
                    'id' => '15',
                    'name' => 'Paraiba',
                    'code' => 'PB',
                    'iso' => '25',
                    'slug' => 'paraiba',
                    'population' => '3999415',
                ),
            15 =>
                array (
                    'id' => '16',
                    'name' => 'Pernambuco',
                    'code' => 'PE',
                    'iso' => '26',
                    'slug' => 'pernambuco',
                    'population' => '9410336',
                ),
            16 =>
                array (
                    'id' => '17',
                    'name' => 'Piauí',
                    'code' => 'PI',
                    'iso' => '22',
                    'slug' => 'piaui',
                    'population' => '3212180',
                ),
            17 =>
                array (
                    'id' => '18',
                    'name' => 'Paraná',
                    'code' => 'PR',
                    'iso' => '41',
                    'slug' => 'parana',
                    'population' => '11242720',
                ),
            18 =>
                array (
                    'id' => '19',
                    'name' => 'Rio de Janeiro',
                    'code' => 'RJ',
                    'iso' => '33',
                    'slug' => 'rio-de-janeiro',
                    'population' => '16635996',
                ),
            19 =>
                array (
                    'id' => '20',
                    'name' => 'Rio Grande do Norte',
                    'code' => 'RN',
                    'iso' => '24',
                    'slug' => 'rio-grande-do-norte',
                    'population' => '3474998',
                ),
            20 =>
                array (
                    'id' => '21',
                    'name' => 'Rondônia',
                    'code' => 'RO',
                    'iso' => '11',
                    'slug' => 'rondonia',
                    'population' => '1787279',
                ),
            21 =>
                array (
                    'id' => '22',
                    'name' => 'Roraima',
                    'code' => 'RR',
                    'iso' => '14',
                    'slug' => 'roraima',
                    'population' => '514229',
                ),
            22 =>
                array (
                    'id' => '23',
                    'name' => 'Rio Grande do Sul',
                    'code' => 'RS',
                    'iso' => '43',
                    'slug' => 'rio-grande-do-sul',
                    'population' => '11286500',
                ),
            23 =>
                array (
                    'id' => '24',
                    'name' => 'Santa Catarina',
                    'code' => 'SC',
                    'iso' => '42',
                    'slug' => 'santa-catarina',
                    'population' => '6910553',
                ),
            24 =>
                array (
                    'id' => '25',
                    'name' => 'Sergipe',
                    'code' => 'SE',
                    'iso' => '28',
                    'slug' => 'sergipe',
                    'population' => '2265779',
                ),
            25 =>
                array (
                    'id' => '26',
                    'name' => 'São Paulo',
                    'code' => 'SP',
                    'iso' => '35',
                    'slug' => 'sao-paulo',
                    'population' => '44749699',
                ),
            26 =>
                array (
                    'id' => '27',
                    'name' => 'Tocantins',
                    'code' => 'TO',
                    'iso' => '17',
                    'slug' => 'tocantins',
                    'population' => '1532902',
                ),
        ));
    }
}
