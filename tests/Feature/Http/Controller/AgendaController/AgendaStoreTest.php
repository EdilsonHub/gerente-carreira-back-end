<?php

namespace Tests\Feature\Http\Controller\AgendaController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    private function dataAgendaFaker($params = [])
    {
        $faker = Factory::create();
        $dataCreated = [
            "id_agenda_superior" => "0",
            "nome" => str_replace("-", " ", $faker->slug()),
            "inicio" => $faker->dateTime()->format('Y-m-d H:i:s'),
            "fim" => $faker->dateTime()->format('Y-m-d H:i:s')

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
}