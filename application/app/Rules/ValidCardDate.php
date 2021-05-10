<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCardDate implements Rule
{
    public function passes($attribute, $value)
    {
        if (strlen($value) != 4) {
            return false;
        }
        $month = substr($value, 0, 2);
        if ($month > 12) {
            return false;
        }
        $year = substr($value, 2);
        if ((int) $year < (int) date('y')) {
            return false;
        }
        return true;
    }

    public function message()
    {
        return __("A data informada no cartão é inválida.");
    }
}
