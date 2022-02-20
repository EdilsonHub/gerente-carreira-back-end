<?php

namespace Tests\Feature;

use App\Models\Projeto;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjetoIndexTest extends TestCase
{

    // use DatabaseTransactions;
    use RefreshDatabase;

    public function testListaProjetoVazia()
    {
        $response = $this->json('GET', '/api/projeto');
        $response->assertStatus(204);
    }
    public function testListaProjetoComUmElementoSemFilho()
    {
        $projetoPai = Projeto::factory()->create();
        $projetoBuscado = Projeto::find($projetoPai->id);

        $response = $this->json('GET', '/api/projeto');
        $response->assertStatus(200);
        $response->assertJson(
            [
                "data" => [
                    [
                        "id" => $projetoBuscado->id,
                        "nome" => $projetoBuscado->nome,
                        "descricao" => $projetoBuscado->descricao,
                        "nivel_projeto" => $projetoBuscado->nivel_projeto,
                        "data_criacao" =>  $projetoBuscado->data_criacao,
                        "data_limite" => $projetoBuscado->data_limite,
                        "custo_previsto" => $projetoBuscado->custo_previsto,
                        "local_de_realizacao_previsto" => $projetoBuscado->local_de_realizacao_previsto,
                        "filhos" => []
                    ]
                ],
                "links" => [
                    "first" => "http://gerente-carreira.test/api/projeto?page=1",
                    "last" => "http://gerente-carreira.test/api/projeto?page=1",
                    "prev" => null,
                    "next" => null
                ],
                "meta" => [
                    "current_page" => 1,
                    "from" => 1,
                    "last_page" => 1,
                    "links" => [
                        [
                            "url" => null,
                            "label" => "&laquo; Previous",
                            "active" => false
                        ],
                        [
                            "url" => "http://gerente-carreira.test/api/projeto?page=1",
                            "label" => "1",
                            "active" => true
                        ],
                        [
                            "url" => null,
                            "label" => "Next &raquo;",
                            "active" => false
                        ]
                    ],
                    "path" => "http://gerente-carreira.test/api/projeto",
                    "per_page" => 10,
                    "to"  => 1,
                    "total" => 1
                ]
            ]
        );
    }

    public function testListaProjetosComTresElementosSemFilhos()
    {
        $projetosPai = Projeto::factory()->count(3)->create();
        $projetoBuscadoArray = [];
        foreach ($projetosPai as $projetoPai) {
            $projetoBuscadoArray[] = Projeto::find($projetoPai->id);
        }
        $response = $this->json('GET', '/api/projeto');
        $response->assertStatus(200);

        $response->assertJson(
            [
                "data" => [
                    [
                        "id" => $projetoBuscadoArray[0]->id,
                        "nome" => $projetoBuscadoArray[0]->nome,
                        "descricao" => $projetoBuscadoArray[0]->descricao,
                        "nivel_projeto" => $projetoBuscadoArray[0]->nivel_projeto,
                        "data_criacao" =>  $projetoBuscadoArray[0]->data_criacao,
                        "data_limite" => $projetoBuscadoArray[0]->data_limite,
                        "custo_previsto" => $projetoBuscadoArray[0]->custo_previsto,
                        "local_de_realizacao_previsto" => $projetoBuscadoArray[0]->local_de_realizacao_previsto,
                        "filhos" => []
                    ],
                    [
                        "id" => $projetoBuscadoArray[1]->id,
                        "nome" => $projetoBuscadoArray[1]->nome,
                        "descricao" => $projetoBuscadoArray[1]->descricao,
                        "nivel_projeto" => $projetoBuscadoArray[1]->nivel_projeto,
                        "data_criacao" =>  $projetoBuscadoArray[1]->data_criacao,
                        "data_limite" => $projetoBuscadoArray[1]->data_limite,
                        "custo_previsto" => $projetoBuscadoArray[1]->custo_previsto,
                        "local_de_realizacao_previsto" => $projetoBuscadoArray[1]->local_de_realizacao_previsto,
                        "filhos" => []
                    ],
                    [
                        "id" => $projetoBuscadoArray[2]->id,
                        "nome" => $projetoBuscadoArray[2]->nome,
                        "descricao" => $projetoBuscadoArray[2]->descricao,
                        "nivel_projeto" => $projetoBuscadoArray[2]->nivel_projeto,
                        "data_criacao" =>  $projetoBuscadoArray[2]->data_criacao,
                        "data_limite" => $projetoBuscadoArray[2]->data_limite,
                        "custo_previsto" => $projetoBuscadoArray[2]->custo_previsto,
                        "local_de_realizacao_previsto" => $projetoBuscadoArray[2]->local_de_realizacao_previsto,
                        "filhos" => []
                    ]
                ],
                "links" => [
                    "first" => "http://gerente-carreira.test/api/projeto?page=1",
                    "last" => "http://gerente-carreira.test/api/projeto?page=1",
                    "prev" => null,
                    "next" => null
                ],
                "meta" => [
                    "current_page" => 1,
                    "from" => 1,
                    "last_page" => 1,
                    "links" => [
                        [
                            "url" => null,
                            "label" => "&laquo; Previous",
                            "active" => false
                        ],
                        [
                            "url" => "http://gerente-carreira.test/api/projeto?page=1",
                            "label" => "1",
                            "active" => true
                        ],
                        [
                            "url" => null,
                            "label" => "Next &raquo;",
                            "active" => false
                        ]
                    ],
                    "path" => "http://gerente-carreira.test/api/projeto",
                    "per_page" => 10,
                    "to"  => 3,
                    "total" => 3
                ]
            ]
        );
    }

    public function testListaUmProjetoComUmProjetoFilho()
    {
        $projetoPai = Projeto::factory()->create();
        $projetoFilho = Projeto::factory(['id_projeto_pai' => $projetoPai->id])->create();

        $projetoBuscado = Projeto::find($projetoPai->id);
        $projetoFilhoBuscado = Projeto::find($projetoFilho->id);


        $response = $this->json('GET', '/api/projeto');
        $response->assertStatus(200);
        $response->assertJson(
            [
                "data" => [
                    [
                        "id" => $projetoBuscado->id,
                        "nome" => $projetoBuscado->nome,
                        "descricao" => $projetoBuscado->descricao,
                        "nivel_projeto" => $projetoBuscado->nivel_projeto,
                        "data_criacao" =>  $projetoBuscado->data_criacao,
                        "data_limite" => $projetoBuscado->data_limite,
                        "custo_previsto" => $projetoBuscado->custo_previsto,
                        "local_de_realizacao_previsto" => $projetoBuscado->local_de_realizacao_previsto,
                        "filhos" => [
                            [
                                "id" => $projetoFilhoBuscado->id,
                                "nome" => $projetoFilhoBuscado->nome,
                                "descricao" => $projetoFilhoBuscado->descricao,
                                "nivel_projeto" => $projetoFilhoBuscado->nivel_projeto,
                                "data_criacao" =>  $projetoFilhoBuscado->data_criacao,
                                "data_limite" => $projetoFilhoBuscado->data_limite,
                                "custo_previsto" => $projetoFilhoBuscado->custo_previsto,
                                "local_de_realizacao_previsto" => $projetoFilhoBuscado->local_de_realizacao_previsto,
                                "filhos" => []
                            ]
                        ]
                    ]
                ],
                "links" => [
                    "first" => "http://gerente-carreira.test/api/projeto?page=1",
                    "last" => "http://gerente-carreira.test/api/projeto?page=1",
                    "prev" => null,
                    "next" => null
                ],
                "meta" => [
                    "current_page" => 1,
                    "from" => 1,
                    "last_page" => 1,
                    "links" => [
                        [
                            "url" => null,
                            "label" => "&laquo; Previous",
                            "active" => false
                        ],
                        [
                            "url" => "http://gerente-carreira.test/api/projeto?page=1",
                            "label" => "1",
                            "active" => true
                        ],
                        [
                            "url" => null,
                            "label" => "Next &raquo;",
                            "active" => false
                        ]
                    ],
                    "path" => "http://gerente-carreira.test/api/projeto",
                    "per_page" => 10,
                    "to"  => 1,
                    "total" => 1
                ]
            ]
        );
    }
}