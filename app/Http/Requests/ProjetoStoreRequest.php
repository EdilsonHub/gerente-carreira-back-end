<?php

namespace App\Http\Requests;

use App\Rules\CheckProjetoMesmoNomeRule;
use App\Http\Requests\ProjetoRequest;

class ProjetoStoreRequest extends ProjetoRequest
{

    public function rules()
    {
        return array_merge(
            ['nome' => ['required', 'max:255', new CheckProjetoMesmoNomeRule($this->id_projeto_pai)]],
            parent::rules()
        );
    }

    public function messages()
    {
        return array_merge(
            ['nome.required' => 'Não foi passado o nome para a criação do novo projeto.'],
            parent::messages()
        );
    }
}
