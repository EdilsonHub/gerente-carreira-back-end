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
        $response = $this->json('GET', '/api/projeto');
        $response->assertStatus(200);
        $response->assertJsonFragment($projetoPai->toArray());
    }

    public function testListaProjetosComTresElementosSemFilhos()
    {
        $projetosPai = Projeto::factory()->count(3)->create();
        $response = $this->json('GET', '/api/projeto');
        $response->assertStatus(200);
        foreach($projetosPai as $projetoPai) {
            $response->assertJsonFragment($projetoPai->toArray());
        }
    }

    public function testListaUmProjetoComUmProjetoFilho() {
        $projetoPai = Projeto::factory()->create();
        $projetoFilho = Projeto::factory(['id_projeto_pai' => $projetoPai->id])->create();
        // var_dump($projetoPai->id, $projetoFilho->toArray());
    }


    // public function testGetProjetoExistentePeloId() {
    //     $projetoPai = Projeto::factory()->create();
    //     $response = $this->json('GET', '/api/projeto/'.$projetoPai->id);
    //     $response->assertStatus(200);
    //     $response->assertJsonFragment(['id' => $projetoPai->id]);
    // }

    // public function testGetProjetoInexistentePeloId() {
    //     $response = $this->json('GET', '/api/projeto/'.'1');
    //     $response->assertStatus(404);
    //     $response->assertJson([]);
    //     $response->assertJsonCount(0);
    // }
}
