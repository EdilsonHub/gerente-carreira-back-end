<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AgendaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $filhas = [];
        foreach ($this->filhas as $f) {
            $filhas[] = [
                "id_agenda_superior" => $f->id_agenda_superior,
                "nome" => $f->nome,
                "inicio" => $f->inicio,
                "fim" => $f->fim,
                'filhas' => []
            ];
        }
        return [
            "id_agenda_superior" => $this->id_agenda_superior,
            "nome" => $this->nome,
            "inicio" => $this->inicio,
            "fim" => $this->fim,
            "filhas" => $filhas
        ];
    }
}
