<?php

namespace Tests\Feature\Http\Controller\ProjetoController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Projeto;

class ProjetoDestroyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function deletar_um_projeto_sem_filho()
    {
        $projeto = Projeto::factory()->create()->fresh();

        $response = $this->json('DELETE', '/api/projeto/' . $projeto->id);

        $response->assertStatus(200);
        $response->assertJson([]);
        $this->assertDatabaseCount('projetos', 0);

    }

    /**
     * @test
     */
    public function deletar_um_projeto_inexistente()
    {
        $projeto = Projeto::factory()->create()->fresh();
        $response = $this->json('DELETE', '/api/projeto/' . $projeto->id + 4);

        $response->assertStatus(404);
        $response->assertJson([]);
        $this->assertDatabaseCount('projetos', 1);
    }

    /**
     * @test
     */
    public function falhar_deletar_deletar_um_projeto_com_filhos()
    {
        $projetoPai = Projeto::factory()->create()->fresh();
        $projetoFilhos = Projeto::factory(['id_projeto_pai' => $projetoPai->id])->count(8)->create()->fresh();

        $response = $this->json('DELETE', '/api/projeto/' . $projetoPai->id);

        $response->assertStatus(400);
        $response->assertJsonFragment(['error' => ['Este projeto não pode ser apagado, este possui subprojetos']]);
        $this->assertDatabaseCount('projetos', 9);
    }
}
