<?php

namespace App\Rules;

use App\Models\Projeto;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class CheckProjetoMesmoNomeRule implements Rule
{
    private $idProjetoPai;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($idProjetoPai)
    {
        $this->idProjetoPai = $idProjetoPai;
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
        return (Projeto::where('id_projeto_pai', $this->idProjetoPai)
            ->where('nome', $value)->count() == 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'já existe um projeto com este :attribute neste nível';
    }
}