<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCep implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match('/^\d{2}\.?\d{6}$/', $value) > 0;
    }

    public function message()
    {
        return 'O campo :attribute não possui um formato válido de CEP.';
    }
}
