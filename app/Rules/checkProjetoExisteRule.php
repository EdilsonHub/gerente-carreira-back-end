<?php

namespace App\Rules;

use App\Models\Projeto;
use Illuminate\Contracts\Validation\Rule;

class checkProjetoExisteRule implements Rule // verificar pq provavelmente o laravel já implementa esta solução
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
        if (empty($value)) {
            return true;
        }
        if (!in_array($attribute, ["id_projeto_pai"])) {
            return true;
        }
        return !!Projeto::find($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Projeto pai especificado não existe.';
    }
}