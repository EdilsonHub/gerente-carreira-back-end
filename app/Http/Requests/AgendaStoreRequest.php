<?php

namespace App\Http\Requests;

use App\Rules\CheckAgendaMesmoNomeRule;
use App\Rules\CheckAgendaExisteRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AgendaStoreRequest extends FormRequest
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
            'id_agenda_superior' => [new CheckAgendaExisteRule($this->id_agenda_superior)],
            'nome' => ['required', 'max:255', new CheckAgendaMesmoNomeRule($this->id_agenda_superior)],
            'inicio' => ['required', 'date', 'before:fim'],
            'fim' => ['required', 'date']
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
            'nome.required' => 'Não foi passado o nome para a criação da nova agenda.',
            'nome.max' => 'O nome é maior que 255 caracteres.',
            'inicio.required' => 'Não foi passado a data inicial para a criação da nova agenda.',
            'fim.required' => 'Não foi passado a data final para a criação da nova agenda.',
            'inicio.before' => 'Data de início da agenda superior a data final.',
            'inicio.date' => 'Não foi passado uma data inicial válida para a criação da nova agenda.',
            'fim.date' => 'Não foi passado uma data final válida para a criação da nova agenda.'
        ];
    }
}