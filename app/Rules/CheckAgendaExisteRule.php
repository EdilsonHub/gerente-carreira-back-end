<?php

namespace App\Rules;

use App\Models\Agenda;
use Illuminate\Contracts\Validation\Rule;

class CheckAgendaExisteRule implements Rule // verificar pq provavelmente o laravel já implementa esta solução
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
        if (!in_array($attribute, ["id_agenda_superior"])) {
            return true;
        }
        return !!Agenda::find($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Agenda superiora especificada não existe.';
    }
}