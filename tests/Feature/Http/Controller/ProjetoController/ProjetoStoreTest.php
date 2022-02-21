<?php

namespace Tests\Feature;

use App\Models\Projeto;
use DeepCopy\Filter\ReplaceFilter;
use Faker\Factory;
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
        $arrayInsert = $this->dataProjetoFaker();
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJson($arrayInsert);
        $this->assertDatabaseHas('projetos', $arrayInsert);
        $response->assertStatus(201);
    }

    public function testCriacaoProjetoNomeUnicoEntreProjetosFilhosDoMesmoPai()
    {
        $arrayInsert = $this->dataProjetoFaker();
        $response1 = $this->json('POST', '/api/projeto', $arrayInsert);
        $response2 = $this->json('POST', '/api/projeto', $arrayInsert);
        $response2->assertJsonFragment(["nome" => ["já existe um projeto com este nome neste nível"]]);
        $response2->assertStatus(422);
        $this->assertTrue(
            Projeto::where('id_projeto_pai', $arrayInsert['id_projeto_pai'])
                ->where('nome', $arrayInsert['nome'])
                ->count() == 1
        );
        $this->assertDatabaseCount('projetos', 1);
    }

    public function testCriacaoNovoProjetoSemNome()
    {
        $this->createResponseSemCamposEssenciais("nome", [
            "nome" => [
                "Não foi passado o nome para a criação do novo projeto."
            ]
        ]);
        $this->assertDatabaseCount('projetos', 0);
    }

    public function testCriacaoProjetoNome_255Caracteres()
    {
        $nome = "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters. 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 1";
        $arrayInsert = $this->dataProjetoFaker(["nome" => $nome]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $this->assertDatabaseHas('projetos', $arrayInsert);
        $response->assertJson($arrayInsert);
        $response->assertStatus(201);
        $this->assertDatabaseCount('projetos', 1);
    }

    public function testCriacaoProjetoNome_256Caracteres()
    {
        $nome = "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters. 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16";
        $arrayInsert = $this->dataProjetoFaker(["nome" => $nome]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["nome" => ["O nome é maior que 255 caracteres."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }

    public function testCriacaoProjetoDescricao_255Caracteres()
    {
        $descricao = "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters. 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 1";
        $arrayInsert = $this->dataProjetoFaker(["descricao" => $descricao]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJson($arrayInsert);
        $response->assertStatus(201);
        $this->assertDatabaseHas('projetos', $arrayInsert);
        $this->assertDatabaseCount('projetos', 1);
    }

    public function testCriacaoProjetoDescricao_256Caracteres()
    {
        $descricao = "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters. 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16";
        $arrayInsert = $this->dataProjetoFaker(["descricao" => $descricao]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["descricao" => ["A descrição é maior que 255 caracteres."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }

    public function testCriacaoProjetoComIdProjetoPaiInexistente()
    {
        // $ultimoProjeto = Projeto::query()->latest('id')->first();
        $idProjetoNaoExiste = 1;
        // if(!empty($ultimoProjeto)) {
        //     $idProjetoNaoExiste = Projeto::query()->latest('id')->first()->id + 1;
        // }
        $arrayInsert = $this->dataProjetoFaker(["id_projeto_pai" => $idProjetoNaoExiste]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["id_projeto_pai" => ["Projeto pai especificado não existe."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }

    public function testCriacaoProjetoComCustoNaoNunerico()
    {
        $arrayInsert = $this->dataProjetoFaker(["custo_previsto" => "E43"]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["custo_previsto" => ["Custo previsto do projeto é invalido."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }


    /**
     * @test
     */
    public function falhar_tentar_criar_projeto_meses_previsto_negativo()
    {
        $arrayInsert = $this->dataProjetoFaker(["meses_previstos" => -1]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["meses_previstos" => ["Valor do campo meses deve ser maior ou igual a zero."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }

    /**
     * @test
     */
    public function falhar_tentar_criar_projeto_dias_previsto_negativo()
    {
        $arrayInsert = $this->dataProjetoFaker(["dias_previstos" => -1]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["dias_previstos" => ["Valor do campo dias deve ser maior ou ou igual a zero."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }

    /**
     * @test
     */
    public function falhar_tentar_criar_projeto_horas_previstas_negativa()
    {
        $arrayInsert = $this->dataProjetoFaker(["horas_previstas" => -1]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["horas_previstas" => ["Valor do campo horas deve ser maior ou igual zero."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }

    /**
     * @test
     */
    public function falhar_tentar_criar_projeto_minutos_previsto_negativo()
    {
        $arrayInsert = $this->dataProjetoFaker(["minutos_previstos" => -1]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["minutos_previstos" => ["Valor do campo minutos deve ser maior ou igual a zero."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }

    /**
     * @test
     */
    public function falhar_tentar_criar_projeto_meses_previsto_maior_1200()
    {
        $arrayInsert = $this->dataProjetoFaker(["meses_previstos" => 1201]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["meses_previstos" => ["Valor do campo meses deve ser menor ou igual a 1200."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }

    /**
     * @test
     */
    public function falhar_tentar_criar_projeto_dias_previsto_maior_31()
    {
        $arrayInsert = $this->dataProjetoFaker(["dias_previstos" => 32]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["dias_previstos" => ["Valor do campo dias deve ser menor ou igual a 31."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }

    /**
     * @test
     */
    public function falhar_tentar_criar_projeto_horas_previstas_maior_23()
    {
        $arrayInsert = $this->dataProjetoFaker(["horas_previstas" => 24]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["horas_previstas" => ["Valor do campo horas deve ser menor ou igual a 23."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }

    /**
     * @test
     */
    public function falhar_tentar_criar_projeto_minutos_previsto_maior_59()
    {
        $arrayInsert = $this->dataProjetoFaker(["minutos_previstos" => 60]);
        $response = $this->json('POST', '/api/projeto', $arrayInsert);
        $response->assertJsonFragment(["minutos_previstos" => ["Valor do campo minutos deve ser menor ou igual a 59."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('projetos', 0);
    }
    private function createResponseSemCamposEssenciais($campos, $mensagemEsperada)
    {
        if (!is_array($campos)) $campos = [$campos];
        $arrayInsert = $this->dataProjetoFaker();
        foreach ($campos as $campo) {
            unset($arrayInsert[$campo]);
        }
        $response = $this->json('POST', '/api/projeto', $arrayInsert);

        $response->assertExactJson([
            "errors" => $mensagemEsperada
        ]);
        $response->assertStatus(422);
    }


    private function dataProjetoFaker($params = [])
    {
        $faker = Factory::create();
        $daraCreated = [
            "nome" => str_replace("-", " ", $faker->slug()),
            "descricao" => $faker->text(),
            "id_projeto_pai" => "0",
            "nivel_projeto" => "0",
            "data_limite" =>  $faker->dateTime()->format('Y-m-d H:i:s'),
            "custo_previsto" => $faker->numberBetween(),
            "meses_previstos" => $faker->numberBetween(1, 48),
            "dias_previstos" => $faker->numberBetween(1, 30),
            "horas_previstas" => $faker->numberBetween(1, 23),
            "minutos_previstos" => $faker->numberBetween(1, 59)

        ];
        $return = [];

        $tryParams = function ($key, $data) use ($params) {
            return isset($params[$key]) ? $params[$key] : $data;
        };

        foreach ($daraCreated as $key => $data) {
            $return[$key] = $tryParams($key, $data);
        }

        return $return;
    }
}
