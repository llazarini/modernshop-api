<?php

namespace App\Store\Shipping;

use App\Models\Option;
use App\Models\Product;
use GuzzleHttp\Client;

class MelhorEnvio implements Shipping
{
    public static function calculate($postalCode, $products)
    {
        $data = [
            'from' => [
                'postal_code' => env('STORE_POSTAL_CODE'),
                'address' => "Rua General Canavarro",
                'number' => 388,
            ],
            'to' => [
                'postal_code' => $postalCode,
            ],
            'products' => [],
            'services' => '1,2,3,4'
        ];
        foreach($products as $item) {
            $product = Product::find($item['id']);
            $option = Option::find($item['option_id']);
            $data['products'][] =     [
                "id" => $product->id,
                "weight" => $option->weight / 1000,
                "width" => $option->width,
                "height" => $option->height,
                "length" => $option->length,
                "quantity" => $item['quantity'],
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
        $shippings = collect();
        if (($status = $response->getStatusCode()) && $status >= 200 && $status < 300) {
            $options = json_decode($response->getBody());
            foreach($options as $option) {
                if (isset($option->error) && $option->error) {
                    continue;
                }
                $shippings->push([
                    'id' => $option->id,
                    'name' => $option->name,
                    'company' => $option->company->name,
                    'image' => $option->company->picture,
                    'price' => isset($option->price) ? $option->price : 0,
                    'delivery_time' => $option->delivery_time
                ]);
            }
            return $shippings;
        }
        return null;
    }
    public static function shipping($postalCode, $products, $shippingOptionId)
    {
        $shippings = self::calculate($postalCode, $products);
        if (!$shippings) {
            throw new \Exception(__('Nenhum frete encontrado.'));
        }
        $shippingOption = $shippings->where('id', '=', $shippingOptionId)
            ->first();
        if (!$shippingOption) {
            throw new \Exception(__('Nenhum frete encontrado (2).'));
        }
        return (object) $shippingOption;
    }
}
