<?php

namespace App\Http\Requests;

use App\Models\Projeto;
use App\Rules\checkCustoPrevistoProjetoRule;
use App\Rules\checkProjetoExisteRule;
use App\Rules\CheckProjetoMesmoNomeRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ProjetoStoreRequest extends FormRequest
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
            'nome' => ['required', 'max:255', new CheckProjetoMesmoNomeRule($this->id_projeto_pai)],
            'descricao' => 'max:255',
            'id_projeto_pai' => new checkProjetoExisteRule(),
            'custo_previsto' => new checkCustoPrevistoProjetoRule()
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
            'nome.required' => 'Não foi passado o nome para a criação do novo projeto.',
            'nome.max' => 'O nome é maior que 255 caracteres.',
            'descricao.max' => 'A descrição é maior que 255 caracteres.'
        ];
    }


}
