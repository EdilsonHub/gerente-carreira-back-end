<?php

namespace Tests\Feature\Http\Controller\AgendaController;

use App\Models\Agenda;
use DateInterval;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Factory;

class AgendaStoreTest extends TestCase
{

    use RefreshDatabase;

    public function test_cricacao_nova_agenda()
    {
        $arrayInsert = $this->dataAgendaFaker();
        $response = $this->json('POST', '/api/agenda', $arrayInsert);
        $response->assertJson($arrayInsert);
        $this->assertDatabaseHas('agendas', $arrayInsert);
        $response->assertStatus(201);
    }

    public function test_criacao_agenda_nome_unico_entre_agendas_mesmo_nivel()
    {
        $arrayInsert = $this->dataAgendaFaker();
        $response1 = $this->json('POST', '/api/agenda', $arrayInsert);
        $response2 = $this->json('POST', '/api/agenda', $arrayInsert);
        $response2->assertJsonFragment(["nome" => ["já existe uma agenda com este nome neste nível"]]);
        $response2->assertStatus(422);
        $this->assertTrue(
            Agenda::where('id_agenda_superior', $arrayInsert['id_agenda_superior'])
                ->where('nome', $arrayInsert['nome'])
                ->count() == 1
        );
        $this->assertDatabaseCount('agendas', 1);
    }

    public function test_criacao_nova_agenda_sem_nome()
    {
        $this->createResponseSemCamposEssenciais("nome", [
            "nome" => [
                "Não foi passado o nome para a criação da nova agenda."
            ]
        ]);
        $this->assertDatabaseCount('agendas', 0);
    }

    public function test_criacao_agenda_nome_255_caracteres()
    {
        $nome = "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters. 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 1";
        $arrayInsert = $this->dataAgendaFaker(["nome" => $nome]);
        $response = $this->json('POST', '/api/agenda', $arrayInsert);
        $this->assertDatabaseHas('agendas', $arrayInsert);
        $response->assertJson($arrayInsert);
        $response->assertStatus(201);
        $this->assertDatabaseCount('agendas', 1);
    }

    public function test_criacao_agenda_nome_256_caracteres()
    {
        $nome = "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters. 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16";
        $arrayInsert = $this->dataAgendaFaker(["nome" => $nome]);
        $response = $this->json('POST', '/api/agenda', $arrayInsert);
        $response->assertJsonFragment(["nome" => ["O nome é maior que 255 caracteres."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('agendas', 0);
    }


    public function test_criacao_agenda_id_agenda_superior_inexistente()
    {
        $idAgendaSuperior = 1;
        $arrayInsert = $this->dataAgendaFaker(["id_agenda_superior" => $idAgendaSuperior]);
        $response = $this->json('POST', '/api/agenda', $arrayInsert);
        $response->assertJsonFragment(["id_agenda_superior" => ["Agenda superiora especificada não existe."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('agendas', 0);
    }

    public function test_falha_criacao_agenda_data_inicio_vazia()
    {
        $this->createResponseSemCamposEssenciais("inicio", [
            "inicio" => [
                "Não foi passado a data inicial para a criação da nova agenda."
            ]
        ]);
        $this->assertDatabaseCount('agendas', 0);
    }

    public function test_falha_criacao_agenda_data_fim_vazia()
    {
        $this->createResponseSemCamposEssenciais("fim", [
            "fim" => [
                "Não foi passado a data final para a criação da nova agenda."
            ]
        ], true);
        $this->assertDatabaseCount('agendas', 0);
    }

    public function test_falha_criacao_agenda_data_inicio_invalida()
    {
        $mensagemEsperada = [
            "inicio" => [
                "Não foi passado uma data inicial válida para a criação da nova agenda."
            ]
        ];

        $arrayInsert = $this->dataAgendaFaker(["inicio" => "Skjf32"]);

        $response = $this->json('POST', '/api/agenda', $arrayInsert);

        $response->assertExactJson([
            "errors" => $mensagemEsperada
        ]);
        $response->assertStatus(422);

        $this->assertDatabaseCount('agendas', 0);
    }

    public function test_falha_criacao_agenda_data_fim_invalida()
    {
        $mensagemEsperada = [
            "fim" => [
                "Não foi passado uma data final válida para a criação da nova agenda."
            ]
        ];

        $arrayInsert = $this->dataAgendaFaker(["fim" => "2022-10-32"]);

        $response = $this->json('POST', '/api/agenda', $arrayInsert);

        $response->assertJsonFragment($mensagemEsperada);
        $response->assertStatus(422);

        $this->assertDatabaseCount('agendas', 0);
    }

    public function test_falha_criacao_agenda_data_inicio_maior_que_data_fim()
    {
        $dataInicio = '2022-02-01 01:01:59';
        $dataFinal = '2022-02-01 01:01:58';
        $arrayInsert = $this->dataAgendaFaker(["inicio" => $dataInicio, "fim" => $dataFinal]);
        $response = $this->json('POST', '/api/agenda', $arrayInsert);
        $response->assertJsonFragment(["inicio" => ["Data de início da agenda superior a data final."]]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('agendas', 0);
    }

    private function dataAgendaFaker($params = [])
    {
        $faker = Factory::create();
        $dateTime = $faker->dateTime();
        $dataCreated = [
            "id_agenda_superior" => "0",
            "nome" => str_replace("-", " ", $faker->slug()),
            "inicio" => $dateTime->format('Y-m-d H:i:s'),
            "fim" => $dateTime->add(new DateInterval("P1D"))->format('Y-m-d H:i:s')

        ];
        $return = [];

        $tryParams = function ($key, $data) use ($params) {
            return isset($params[$key]) ? $params[$key] : $data;
        };

        foreach ($dataCreated as $key => $data) {
            $return[$key] = $tryParams($key, $data);
        }

        return $return;
    }

    private function createResponseSemCamposEssenciais($campos, $mensagemEsperada, $fragmentJson = false, $parametrosCriacaoRequest = [])
    {
        if (!is_array($campos)) $campos = [$campos];
        $arrayInsert = $this->dataAgendaFaker($parametrosCriacaoRequest);
        foreach ($campos as $campo) {
            unset($arrayInsert[$campo]);
        }
        $response = $this->json('POST', '/api/agenda', $arrayInsert);

        if ($fragmentJson) {
            $response->assertJsonFragment($mensagemEsperada);
        } else {
            $response->assertExactJson([
                "errors" => $mensagemEsperada
            ]);
        }
        $response->assertStatus(422);
    }
}