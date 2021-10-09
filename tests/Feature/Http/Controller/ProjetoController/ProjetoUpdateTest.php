<?php

namespace Tests\Feature\Http\Controller\ProjetoController;

use App\Models\Projeto;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjetoUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function atualizar_todos_campos_permitidos()
    {
        $projetoPai1 = Projeto::factory()->create();
        $projetoPai2 = Projeto::factory()->create();

        $projetoFilho = Projeto::factory(['id_projeto_pai' => $projetoPai1->id])->create();

        $projetoFilhoBuscado = Projeto::find($projetoFilho->id);

        $response = $this->json('PUT', '/api/projeto/' . $projetoFilho->id, [
            "nome" => "nome atualizado",
            "descricao" => "campo descricao atualizada",
            "id_projeto_pai" => $projetoPai2->id,
            "nivel_projeto" => 1,
            "custo_previsto" => $projetoFilhoBuscado->custo_previsto + 1
            // "local_de_realizacao_previsto" => local_de_realizacao_previsto
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            "id" => $projetoFilho->id,
            "nome" => "nome atualizado",
            "descricao" => "campo descricao atualizada",
            "id_projeto_pai" => $projetoPai2->id,
            "nivel_projeto" => 1,
            "custo_previsto" => $projetoFilhoBuscado->custo_previsto + 1,
            // "local_de_realizacao_previsto": null,
            "filhos" => []
        ]);

        $this->assertDatabaseHas('projetos', [
            "id" => $projetoFilho->id,
            "nome" => "nome atualizado",
            "descricao" => "campo descricao atualizada",
            "id_projeto_pai" => $projetoPai2->id,
            "nivel_projeto" => 1,
            "custo_previsto" => $projetoFilhoBuscado->custo_previsto + 1
            // "local_de_realizacao_previsto": null,
        ]);
    }

    /**
     * @test
     */
    public function atualiza_apenas_campo_nome()
    {
        $this->assertAtualizaCampoUnico(['nome' => 'nome atualizado']);
    }

    /**
     * @test
     */
    public function atualiza_apenas_campo_descricao()
    {
        $this->assertAtualizaCampoUnico(['descricao' => 'descricao atualizada']);
    }

    /**
     * @test
     */
    public function atualiza_apenas_campo_id_projeto_pai()
    {
        $projetoPai = Projeto::factory()->create();
        $this->assertAtualizaCampoUnico(['id_projeto_pai' => $projetoPai->id]);
    }

    /**
     * @test
     */
    public function atualiza_apenas_campo_nivel_projeto()
    {
        $projetoPai = Projeto::factory()->create();
        $arrayValoresPadroesCriacaoProjeto = $this->arrayValoresPadroesCriacaoProjeto([
            'id_projeto_pai' => $projetoPai->id,
            'nivel_projeto' => 1
        ]);

        $projetoFilho = Projeto::factory($arrayValoresPadroesCriacaoProjeto)->create()->fresh();
        $this->assertAtualizaCampoUnico(['nivel_projeto' => 0], $projetoFilho);
    }

    /**
     * @test
     */
    public function atualiza_apenas_campo_custo_previsto()
    {
        $this->assertAtualizaCampoUnico(['custo_previsto' => 999]);
    }

    // /**
    //  * @test
    //  */
    // public function atualiza_local_de_realizacao_previsto()
    // {
    //     //implementar os endereços ainda
    // }


    /**
     * @test
     */
    public function falha_tentar_atualizar_campo_data_criacao()
    {
        $projeto = Projeto::factory($this->arrayValoresPadroesCriacaoProjeto())->create()->fresh();

        $response = $this->json('PUT', '/api/projeto/' . $projeto->id, ['data_criacao' => '2019-10-09 06:35:41']);

        $response->assertStatus(400);

        $assertJson = [
            // "id" => $projeto->id,
            // "nome" => $projeto->nome,
            // "descricao" => $projeto->descricao,
            // "id_projeto_pai" => $projeto->id_projeto_pai,
            // "nivel_projeto" => $projeto->nivel_projeto,
            // "custo_previsto" => $projeto->custo_previsto,
            // // "local_de_realizacao_previsto": null,
            // "filhos" => []
        ];

        $assertDatabaseHas = [
            "id" => $projeto->id,
            "nome" => $projeto->nome,
            "descricao" => $projeto->descricao,
            "id_projeto_pai" => $projeto->id_projeto_pai,
            "nivel_projeto" => $projeto->nivel_projeto,
            "custo_previsto" => $projeto->custo_previsto
            // "local_de_realizacao_previsto": null,
        ];

        $response->assertJson($assertJson);

        $this->assertDatabaseHas('projetos', $assertDatabaseHas);
    }

    /**
     * @test
     */
    public function falha_tentar_atualizar_campo_id()
    {
        $projeto = Projeto::factory($this->arrayValoresPadroesCriacaoProjeto())->create()->fresh();

        $response = $this->json('PUT', '/api/projeto/' . $projeto->id, ['id' => $projeto->id + 10]);

        $response->assertStatus(400);

        $assertJson = [
            // "id" => $projeto->id,
            // "nome" => $projeto->nome,
            // "descricao" => $projeto->descricao,
            // "id_projeto_pai" => $projeto->id_projeto_pai,
            // "nivel_projeto" => $projeto->nivel_projeto,
            // "custo_previsto" => $projeto->custo_previsto,
            // // "local_de_realizacao_previsto": null,
            // "filhos" => []
        ];

        $assertDatabaseHas = [
            "id" => $projeto->id,
            "nome" => $projeto->nome,
            "descricao" => $projeto->descricao,
            "id_projeto_pai" => $projeto->id_projeto_pai,
            "nivel_projeto" => $projeto->nivel_projeto,
            "custo_previsto" => $projeto->custo_previsto
            // "local_de_realizacao_previsto": null,
        ];

        $response->assertJson($assertJson);

        $this->assertDatabaseHas('projetos', $assertDatabaseHas);
    }

    /**
     * @test
     */
    public function falhar_tentar_atualizar_id_projeto_pai_inexistente()
    {
        $projeto = Projeto::factory($this->arrayValoresPadroesCriacaoProjeto())->create()->fresh();

        $response = $this->json('PUT', '/api/projeto/' . $projeto->id, ['id_projeto_pai' => $projeto->id + 10]);

        $response->assertStatus(400);

        $assertJson = [
            // "id" => $projeto->id,
            // "nome" => $projeto->nome,
            // "descricao" => $projeto->descricao,
            // "id_projeto_pai" => $projeto->id_projeto_pai,
            // "nivel_projeto" => $projeto->nivel_projeto,
            // "custo_previsto" => $projeto->custo_previsto,
            // // "local_de_realizacao_previsto": null,
            // "filhos" => []
        ];

        $assertDatabaseHas = [
            "id" => $projeto->id,
            "nome" => $projeto->nome,
            "descricao" => $projeto->descricao,
            "id_projeto_pai" => $projeto->id_projeto_pai,
            "nivel_projeto" => $projeto->nivel_projeto,
            "custo_previsto" => $projeto->custo_previsto
            // "local_de_realizacao_previsto": null,
        ];

        $response->assertJsonFragment(["id_projeto_pai" => ["Não foi encontrado o projeto de id " . ($projeto->id + 10) . "."]]);

        $this->assertDatabaseHas('projetos', $assertDatabaseHas);
    }

    /**
     * @test
     */
    public function falhar_tentar_atualizar_custo_previsto_para_valor_negativo()
    {

        $projeto = Projeto::factory($this->arrayValoresPadroesCriacaoProjeto())->create()->fresh();

        $response = $this->json('PUT', '/api/projeto/' . $projeto->id, ['custo_previsto' => -200]);

        $assertJson = [
            'custo_previsto' => ['Não pode ser atuazlizado com valor menor que zero.']
        ];

        $response->assertStatus(422);

        $assertDatabaseHas = [
            "id" => $projeto->id,
            "nome" => $projeto->nome,
            "descricao" => $projeto->descricao,
            "id_projeto_pai" => $projeto->id_projeto_pai,
            "nivel_projeto" => $projeto->nivel_projeto,
            "custo_previsto" => $projeto->custo_previsto
            // "local_de_realizacao_previsto": null,
        ];

        $response->assertJsonFragment($assertJson);

        $this->assertDatabaseHas('projetos', $assertDatabaseHas);
    }

    /**
     * @test
     */
    public function falhar_tentar_atualizar_custo_previsto_para_valor_nao_numerico()
    {
        $projeto = Projeto::factory($this->arrayValoresPadroesCriacaoProjeto())->create()->fresh();

        $response = $this->json('PUT', '/api/projeto/' . $projeto->id, ['custo_previsto' => 'Dw23']);

        $assertJson = [
            'custo_previsto' => ['valor precisa ser um número.']
        ];

        $response->assertStatus(422);

        $assertDatabaseHas = [
            "id" => $projeto->id,
            "nome" => $projeto->nome,
            "descricao" => $projeto->descricao,
            "id_projeto_pai" => $projeto->id_projeto_pai,
            "nivel_projeto" => $projeto->nivel_projeto,
            "custo_previsto" => $projeto->custo_previsto
            // "local_de_realizacao_previsto": null,
        ];

        $response->assertJsonFragment($assertJson);

        $this->assertDatabaseHas('projetos', $assertDatabaseHas);
    }

    /**
     * @test
     */
    public function falhar_tentar_atualizar_um_projeto_inexistente()
    {
        $response = $this->json('PUT', '/api/projeto/' . 100, ['custo_previsto' => 2500]);
        $response->assertStatus(404);
        $response->assertJson([]);
        $this->assertDatabaseCount('projetos', 0);
    }

    public function arrayValoresPadroesCriacaoProjeto(array $alteracoes = [])
    {
        $padrao = [
            "nome" => "nome antigo",
            "descricao" => "descricao antiga",
            "id_projeto_pai" => 0,
            "nivel_projeto" => 0,
            "custo_previsto" => 100,
        ];

        if (COUNT($alteracoes) === 0) {
            return $padrao;
        }
        foreach ($alteracoes as $k => $s) {
            if (!isset($padrao[$k])) throw new Exception("arrayValoresPadroesCriacaoProjeto");
            $padrao[$k] = $s;
        }
        return $padrao;
    }

    public function assertAtualizaCampoUnico(array $campo, $projeto = null)
    {

        if (COUNT($campo) > 1) throw new Exception("assertAtualizaCampoUnico");

        $chaveArray = key($campo);
        $valorArray = $campo[$chaveArray];

        if (!$projeto) {
            $projeto = Projeto::factory($this->arrayValoresPadroesCriacaoProjeto())->create()->fresh();
        }

        $response = $this->json('PUT', '/api/projeto/' . $projeto->id, $campo);

        $response->assertStatus(200);

        $assertJson = [
            "id" => $projeto->id,
            "nome" => $projeto->nome,
            "descricao" => $projeto->descricao,
            "id_projeto_pai" => $projeto->id_projeto_pai,
            "nivel_projeto" => $projeto->nivel_projeto,
            "custo_previsto" => $projeto->custo_previsto,
            // "local_de_realizacao_previsto": null,
            "filhos" => []
        ];

        $assertDatabaseHas = [
            "id" => $projeto->id,
            "nome" => $projeto->nome,
            "descricao" => $projeto->descricao,
            "id_projeto_pai" => $projeto->id_projeto_pai,
            "nivel_projeto" => $projeto->nivel_projeto,
            "custo_previsto" => $projeto->custo_previsto
            // "local_de_realizacao_previsto": null,
        ];

        $assertJson[$chaveArray] = $valorArray;
        $assertDatabaseHas[$chaveArray] = $valorArray;

        $response->assertJson($assertJson);

        $this->assertDatabaseHas('projetos', $assertDatabaseHas);
    }
}
