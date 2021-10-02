<?php

namespace Tests\Feature;

use App\Models\Projeto;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjetoStoreTest extends TestCase
{

    // use DatabaseTransactions;
    use RefreshDatabase;

    public function testCriacaoNovoProjeto()
    {
        $arrayInsert = [
            "nome" => "projeto de testes s",
            "descricao" => "Um projeto para testes",
            "id_projeto_pai" => "0",
            "nivel_projeto" => "0",
            "custo_previsto" => "10000"
        ];
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJson($arrayInsert);
        $response->assertStatus(201);
    }

    public function testCriacaoProjetoNomeUnicoEntreProjetosFilhosDoMesmoPai() {
        $arrayInsert = [
            "nome" => "projeto de testes s",
            "descricao" => "Um projeto para testes",
            "id_projeto_pai" => "0",
            "nivel_projeto" => "0",
            "custo_previsto" => "10000"
        ];
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response2 = $this->json('POST', '/api/projeto', $arrayInsert);
        $response2->assertJsonFragment(["nome" => ["já existe um projeto com este nome neste nível"]]);
        $response2->assertStatus(422);
        $this->assertTrue(Projeto::where('id_projeto_pai', $arrayInsert['id_projeto_pai'])
                ->where('nome', $arrayInsert['nome'])
                ->count() == 1
        );
    }

    private function createResponseSemCamposEssenciais($campos, $mensagemEsperada) {
        if(!is_array($campos)) $campos = [$campos];
        $arrayInsert = [
            "nome" => "projeto de testes s",
            "descricao" => "Um projeto para testes",
            "id_projeto_pai" => "0",
            "nivel_projeto" => "0",
            "custo_previsto" => "10000"
        ];
        foreach ($campos as $campo) {
            unset($arrayInsert[$campo]);
        }
        $response = $this->json('POST', '/api/projeto', $arrayInsert);

        $response->assertExactJson([
            "errors" => $mensagemEsperada
        ]);
        $response->assertStatus(422);
    }

    public function testCriacaoNovoProjetoSemNome() {
        $this->createResponseSemCamposEssenciais("nome", [
            "nome" => [
                "Não foi passado o nome para a criação do novo projeto."
            ]
        ]);
    }

    public function testCriacaoProjetoNome255Caracteres() {
        $nome = "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters. 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 1";
        $arrayInsert = [
            "nome" => $nome,
            "descricao" => "Um projeto para testes",
            "id_projeto_pai" => "0",
            "nivel_projeto" => "0",
            "custo_previsto" => "10000"
        ];
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJson($arrayInsert);
        $response->assertStatus(201);
    }

    public function testCriacaoProjetoNome256Caracteres() {
        $nome = "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters. 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16";
        $arrayInsert = [
            "nome" => $nome,
            "descricao" => "Um projeto para testes",
            "id_projeto_pai" => "0",
            "nivel_projeto" => "0",
            "custo_previsto" => "10000"
        ];
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["nome" => ["O nome é maior que 255 caracteres."]]);
        $response->assertStatus(422);
    }

    public function testCriacaoProjetoDescricao255Caracteres() {
        $descricao = "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters. 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 1";
        $arrayInsert = [
            "nome" => "nome qualquer",
            "descricao" => $descricao,
            "id_projeto_pai" => "0",
            "nivel_projeto" => "0",
            "custo_previsto" => "10000"
        ];
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJson($arrayInsert);
        $response->assertStatus(201);
    }

    public function testCriacaoProjetoDescricao256Caracteres() {
        $descricao = "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters. 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16";
        $arrayInsert = [
            "nome" => "nome qualquer",
            "descricao" => $descricao,
            "id_projeto_pai" => "0",
            "nivel_projeto" => "0",
            "custo_previsto" => "10000"
        ];
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["descricao" => ["A descrição é maior que 255 caracteres."]]);
        $response->assertStatus(422);
    }

    public function testCriacaoProjetoComIdProjetoPaiInexistente() {
        $ultimoProjeto = Projeto::query()->latest('id')->first();
        $idProjetoNaoExiste = 1;
        if(!empty($ultimoProjeto)) {
            $idProjetoNaoExiste = Projeto::query()->latest('id')->first()->id + 1;
        }
        $arrayInsert = [
            "nome" => "nome qualquer",
            "descricao" => "",
            "id_projeto_pai" => $idProjetoNaoExiste,
            "nivel_projeto" => "0",
            "custo_previsto" => "10000"
        ];
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["id_projeto_pai" => ["Projeto pai especificado não existe."]]);
        $response->assertStatus(422);
    }

    public function testCriacaoProjetoComCustoNaoNunerico() {
        $arrayInsert = [
            "nome" => "nome qualquer",
            "descricao" => "",
            "id_projeto_pai" => 0,
            "nivel_projeto" => "0",
            "custo_previsto" => "E43"
        ];
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["custo_previsto" => ["Custo previsto do projeto é invalido."]]);
        $response->assertStatus(422);
    }
}
