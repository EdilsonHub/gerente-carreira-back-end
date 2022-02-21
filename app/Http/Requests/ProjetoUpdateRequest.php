<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Rules\CheckProjetoMesmoNomeRule;

class ProjetoUpdateRequest extends ProjetoRequest
{
    public function rules()
    {
        $rules = parent::rules();
        return array_merge(
            [
                'nome' => ['max:255'/*, new CheckProjetoMesmoNomeRule($this->id_projeto_pai, true)*/],
                'id' => Rule::in([null]),
                'data_criacao' => Rule::in([null])
            ],
            $rules
        );
    }
}