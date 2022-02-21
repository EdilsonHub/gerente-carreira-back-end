<?php

namespace App\Http\Requests;

use App\Rules\checkCustoPrevistoProjetoRule;
use App\Rules\checkProjetoExisteRule;
use App\Rules\CheckProjetoMesmoNomeRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ProjetoRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'descricao' => 'max:255',
            'id_projeto_pai' => new checkProjetoExisteRule(),
            'custo_previsto' => [new checkCustoPrevistoProjetoRule()],
            'meses_previstos' => ['gte:0', 'lte:1200'],
            'dias_previstos' => ['gte:0', 'lte:31'],
            'horas_previstas' => ['gte:0', 'lte:23'],
            'minutos_previstos' => ['gte:0', 'lte:59']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    public function messages()
    {
        return [
            'nome.max' => 'O nome é maior que 255 caracteres.',
            'descricao.max' => 'A descrição é maior que 255 caracteres.',
            'meses_previstos.gte' => 'Valor do campo meses deve ser maior ou igual a zero.',
            'dias_previstos.gte' => 'Valor do campo dias deve ser maior ou ou igual a zero.',
            'horas_previstas.gte' => 'Valor do campo horas deve ser maior ou igual zero.',
            'minutos_previstos.gte' => 'Valor do campo minutos deve ser maior ou igual a zero.',
            'meses_previstos.lte' => 'Valor do campo meses deve ser menor ou igual a 1200.',
            'dias_previstos.lte' => 'Valor do campo dias deve ser menor ou igual a 31.',
            'horas_previstas.lte' => 'Valor do campo horas deve ser menor ou igual a 23.',
            'minutos_previstos.lte' => 'Valor do campo minutos deve ser menor ou igual a 59.'
        ];
    }
}