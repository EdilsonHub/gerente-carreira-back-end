<?php

namespace Tests\Feature\Http\Controller\ProjetoController;

use App\Models\Projeto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjetoShowTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @test
     */
    public function visualizar_um_projeto_de_id_inexistente()
    {
        $response = $this->json('GET', '/api/projeto/' . 1);
        $response->assertStatus(404);
        $response->assertJson([]);
    }

    /**
     * @test
     */
    public function visualizar_um_projeto_sem_projeto_filho()
    {
        $projeto = Projeto::factory()->create();
        $projetoBuscado = Projeto::find($projeto->id);

        $response = $this->json('GET', '/api/projeto/' . $projeto->id);
        $response->assertStatus(200);
        $response->assertJson([
            "id" => $projetoBuscado->id,
            "nome" => $projetoBuscado->nome,
            "descricao" => $projetoBuscado->descricao,
            "nivel_projeto" => $projetoBuscado->nivel_projeto,
            "data_criacao" =>  $projetoBuscado->data_criacao,
            "data_limite" => $projetoBuscado->data_limite,
            "custo_previsto" => $projetoBuscado->custo_previsto,
            'meses_previstos' => $projetoBuscado->meses_previstos,
            'dias_previstos' => $projetoBuscado->dias_previstos,
            'horas_previstas' => $projetoBuscado->horas_previstas,
            'minutos_previstos' => $projetoBuscado->minutos_previstos,
            "local_de_realizacao_previsto" => $projetoBuscado->local_de_realizacao_previsto,
            "filhos" => []
        ]);
    }

    /**
     * @test
     */
    public function visualizar_um_projeto_com_tres_projetos_filhos()
    {
        $projetoPai = Projeto::factory()->create();
        $projetoFilho = Projeto::factory(['id_projeto_pai' => $projetoPai->id])->count(3)->create();

        $projetoBuscado = Projeto::find($projetoPai->id);
        $filhos = $projetoBuscado->filhos;

        $response = $this->json('GET', '/api/projeto/' . $projetoPai->id);
        $response->assertStatus(200);
        $response->assertJson([
            "id" => $projetoBuscado->id,
            "nome" => $projetoBuscado->nome,
            "descricao" => $projetoBuscado->descricao,
            "nivel_projeto" => $projetoBuscado->nivel_projeto,
            "data_criacao" =>  $projetoBuscado->data_criacao,
            "data_limite" => $projetoBuscado->data_limite,
            "custo_previsto" => $projetoBuscado->custo_previsto,
            'meses_previstos' => $projetoBuscado->meses_previstos,
            'dias_previstos' => $projetoBuscado->dias_previstos,
            'horas_previstas' => $projetoBuscado->horas_previstas,
            'minutos_previstos' => $projetoBuscado->minutos_previstos,
            "local_de_realizacao_previsto" => $projetoBuscado->local_de_realizacao_previsto,
            "filhos" => [
                [
                    "id" => $filhos[0]->id,
                    "nome" => $filhos[0]->nome,
                    "descricao" => $filhos[0]->descricao,
                    "nivel_projeto" => $filhos[0]->nivel_projeto,
                    "data_criacao" =>  $filhos[0]->data_criacao,
                    "data_limite" =>  $filhos[0]->data_limite,
                    "custo_previsto" => $filhos[0]->custo_previsto,
                    'meses_previstos' => $filhos[0]->meses_previstos,
                    'dias_previstos' => $filhos[0]->dias_previstos,
                    'horas_previstas' => $filhos[0]->horas_previstas,
                    'minutos_previstos' => $filhos[0]->minutos_previstos,
                    "local_de_realizacao_previsto" => $filhos[0]->local_de_realizacao_previsto,
                ],
                [
                    "id" => $filhos[1]->id,
                    "nome" => $filhos[1]->nome,
                    "descricao" => $filhos[1]->descricao,
                    "nivel_projeto" => $filhos[1]->nivel_projeto,
                    "data_criacao" =>  $filhos[1]->data_criacao,
                    "data_limite" =>  $filhos[1]->data_limite,
                    "custo_previsto" => $filhos[1]->custo_previsto,
                    'meses_previstos' => $filhos[1]->meses_previstos,
                    'dias_previstos' => $filhos[1]->dias_previstos,
                    'horas_previstas' => $filhos[1]->horas_previstas,
                    'minutos_previstos' => $filhos[1]->minutos_previstos,
                    "local_de_realizacao_previsto" => $filhos[1]->local_de_realizacao_previsto,
                ],
                [
                    "id" => $filhos[2]->id,
                    "nome" => $filhos[2]->nome,
                    "descricao" => $filhos[2]->descricao,
                    "nivel_projeto" => $filhos[2]->nivel_projeto,
                    "data_criacao" =>  $filhos[2]->data_criacao,
                    "data_limite" =>  $filhos[2]->data_limite,
                    "custo_previsto" => $filhos[2]->custo_previsto,
                    'meses_previstos' => $filhos[2]->meses_previstos,
                    'dias_previstos' => $filhos[2]->dias_previstos,
                    'horas_previstas' => $filhos[2]->horas_previstas,
                    'minutos_previstos' => $filhos[2]->minutos_previstos,
                    "local_de_realizacao_previsto" => $filhos[2]->local_de_realizacao_previsto,
                ]
            ]
        ]);
    }
}