<?php

namespace App\Rules;

use App\Models\Agenda;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class CheckAgendaMesmoNomeRule implements Rule
{
    private $idAgendaSuperior;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($idAgendaSuperior)
    {
        $this->idAgendaSuperior = $idAgendaSuperior;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($attribute != "nome") {
            return true;
        }
        return (Agenda::where('id_agenda_superior', $this->idAgendaSuperior)
            ->where('nome', $value)->count() == 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'já existe uma agenda com este :attribute neste nível';
    }
}