<?php

namespace App\Store\Shipping;

interface Shipping
{
    public static function calculate(string $postalCode, $products);
    public static function shipping(string $postalCode, $products, int $shippingOptionId);
}
