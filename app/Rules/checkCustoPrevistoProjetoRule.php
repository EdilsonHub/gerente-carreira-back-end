<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class checkCustoPrevistoProjetoRule implements Rule //provavelmente o laravel já tem uma solução para esta validação. Favor olhar e fazer melhor
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($attribute != "custo_previsto") {
            return true;
        }
        return is_numeric($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Custo previsto do projeto é invalido.';
    }
}
