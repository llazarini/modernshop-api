<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserType;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function basic(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);
        $user = User::whereEmail($request->get('email'))
            ->first();
        return response()->json(!!$user);
    }

    public function create(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['required', 'max:200', 'min:10'],
            'password' => ['required', 'min:6', 'max:200', 'same:password'],
            'password_confirm' => ['required', 'min:6', 'max:200'],
        ]);
        $user = new User();
        $user->company_id = 1;
        $user->user_type_id = UserType::getId('client');
        $user->fill($request->all());
        if (!$user->save()) {
            return response()->json([
                'message' => __('Erro ao criar usuário')
            ]);
        }
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $tokenResult,
        ]);
    }

    public function address(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'zip_code' => ['required', 'min:8', 'max:8'],
            'street_name' => ['required', 'min:3'],
            'street_number' => ['required', 'numeric'],
            'complement' => ['nullable'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
        ]);
        UserAddress::whereUserId($user->id)
            ->update(['main' => false]);
        $address = new UserAddress();
        $address->fill($request->all());
        $address->user_id = $user->id;
        if (!$address->save()) {
            return response()->json([
                'message' => __('O endereço não foi cadastrado.')
            ], 400);
        }
        return response()->json([
            'message' => __('Sucesso ao criar endereço.')
        ]);
    }

    public function shipment(Request $request) {
        $request->validate([
            'postal_code' => ['required'],
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric'],
        ]);
        $data = [
            'from' => [
                'postal_code' => env('STORE_POSTAL_CODE'),
                'address' => "Rua General Canavarro",
                'number' => 388,
            ],
            'to' => [
                'postal_code' => $request->get('postal_code'),
            ],
            'products' => [],
            'services' => '1,2,3,4'
        ];
        foreach($request->get('products') as $item) {
            $product = Product::find($item['id']);
            $data['products'][] =     [
                "id" => $product->id,
                "weight" => $product->weight, // peso
                "width" => $product->width, // largura
                "height" => $product->height, // altura
                "length" => $product->length, // comprimento
                "quantity" => $item['quantity'], // opcional, padrão 1
                "insurance_value" => 100
            ];
        }
        $client = new Client([
            'base_uri' => env('MELHOR_ENVIO_URL'),
            'headers' => [
                'Authorization' => 'Bearer ' . env('MELHOR_ENVIO_KEY'),
                'Accept' => 'application/json'
            ],
            'json' => $data
        ]);
        $response = $client->post('/api/v2/me/shipment/calculate');
        if (($status = $response->getStatusCode()) && $status >= 200 && $status < 300) {
            return response()->json(json_decode($response->getBody()));
        }
        return response()->json([
            'message' => __('Ocorreu um erro ao tentar calcular o frete. Mas sem problemas, você ainda pode concluir sua compra!')
        ]);
    }

    public function payment(Request $request) {
        $pagarme = new PagarMe\Client(env('PAGARME_KEY'));

        $transaction = $pagarme->transactions()->create([
            'amount' => 1000,
            'payment_method' => 'credit_card',
            'card_holder_name' => 'Anakin Skywalker',
            'card_cvv' => '123',
            'card_number' => '4242424242424242',
            'card_expiration_date' => '1220',
            'customer' => [
                'external_id' => '1',
                'name' => 'Nome do cliente',
                'type' => 'individual',
                'country' => 'br',
                'documents' => [
                    [
                        'type' => 'cpf',
                        'number' => '00000000000'
                    ]
                ],
                'phone_numbers' => [ '+551199999999' ],
                'email' => 'cliente@email.com'
            ],
            'billing' => [
                'name' => 'Nome do pagador',
                'address' => [
                    'country' => 'br',
                    'street' => 'Avenida Brigadeiro Faria Lima',
                    'street_number' => '1811',
                    'state' => 'sp',
                    'city' => 'Sao Paulo',
                    'neighborhood' => 'Jardim Paulistano',
                    'zipcode' => '01451001'
                ]
            ],
            'shipping' => [
                'name' => 'Nome de quem receberá o produto',
                'fee' => 1020,
                'delivery_date' => '2018-09-22',
                'expedited' => false,
                'address' => [
                    'country' => 'br',
                    'street' => 'Avenida Brigadeiro Faria Lima',
                    'street_number' => '1811',
                    'state' => 'sp',
                    'city' => 'Sao Paulo',
                    'neighborhood' => 'Jardim Paulistano',
                    'zipcode' => '01451001'
                ]
            ],
            'items' => [
                [
                    'id' => '1',
                    'title' => 'R2D2',
                    'unit_price' => 300,
                    'quantity' => 1,
                    'tangible' => true
                ],
                [
                    'id' => '2',
                    'title' => 'C-3PO',
                    'unit_price' => 700,
                    'quantity' => 1,
                    'tangible' => true
                ]
            ]
        ]);
    }
}
