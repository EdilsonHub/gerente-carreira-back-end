<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjetoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        $filhos = [];
        foreach ($this->filhos as $f) {
            $filhos[] = [
                'id' => $f->id,
                'nome' => $f->nome,
                'descricao' => $f->descricao,
                'id_projeto_pai' => $f->id_projeto_pai,
                'nivel_projeto' => $f->nivel_projeto,
                'data_criacao' => $f->data_criacao,
                'custo_previsto' => $f->custo_previsto,
                'local_de_realizacao_previsto' => $f->local_de_realizacao_previsto,
                'filhos' => []
            ];
        }

        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'id_projeto_pai' => $this->id_projeto_pai,
            'nivel_projeto' => $this->nivel_projeto,
            'data_criacao' => $this->data_criacao,
            'custo_previsto' => $this->custo_previsto,
            'local_de_realizacao_previsto' => $this->local_de_realizacao_previsto,
            'filhos' => $filhos
            // 'filhos' => new ProjetoCollection($this->filhos)
        ];
    }
}
