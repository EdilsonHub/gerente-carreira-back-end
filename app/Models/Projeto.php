<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projeto extends Model
{
    use HasFactory;
    protected $fillable = [
        "nome",
        "descricao",
        "id_projeto_pai",
        "nivel_projeto",
        "data_criacao",
        "data_limite",
        "data_inicio_execucao",
        "data_conclusao",
        "custo_previsto",
        'meses_previstos',
        'dias_previstos',
        'horas_previstas',
        'minutos_previstos',
        "local_de_realizacao_previsto"
    ];

    public function filhos()
    {
        return $this->hasMany(Projeto::class, 'id_projeto_pai', 'id');
    }

    public function pai()
    {
        return $this->belongsTo(Projeto::class, 'id');
    }
}