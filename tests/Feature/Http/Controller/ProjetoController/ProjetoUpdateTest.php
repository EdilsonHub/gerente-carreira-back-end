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

        $dataLimite = (new \DateTime())->add(new \DateInterval("P30D"))->format('Y-m-d H:i:s');

        $response = $this->json('PUT', '/api/projeto/' . $projetoFilho->id, [
            "nome" => "nome atualizado",
            "descricao" => "campo descricao atualizada",
            "id_projeto_pai" => $projetoPai2->id,
            "nivel_projeto" => 1,
            "custo_previsto" => $projetoFilhoBuscado->custo_previsto + 1,
            "data_limite" => $dataLimite,
            'meses_previstos' => 1,
            'dias_previstos' => 1,
            'horas_previstas' => 1,
            'minutos_previstos' => 1
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
            "data_limite" => $dataLimite,
            'meses_previstos' => 1,
            'dias_previstos' => 1,
            'horas_previstas' => 1,
            'minutos_previstos' => 1,
            // "local_de_realizacao_previsto": null,
            "filhos" => []
        ]);

        $this->assertDatabaseHas('projetos', [
            "id" => $projetoFilho->id,
            "nome" => "nome atualizado",
            "descricao" => "campo descricao atualizada",
            "id_projeto_pai" => $projetoPai2->id,
            "nivel_projeto" => 1,
            "custo_previsto" => $projetoFilhoBuscado->custo_previsto + 1,
            "data_limite" => $dataLimite,
            'meses_previstos' => 1,
            'dias_previstos' => 1,
            'horas_previstas' => 1,
            'minutos_previstos' => 1
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
        $this->assertAtualizaCampoUnico(['nivel_projeto' => 0], 200, null, $projetoFilho);
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
    public function falhar_tentar_atualizar_campo_data_criacao()
    {
        $projeto = Projeto::factory($this->arrayValoresPadroesCriacaoProjeto())->create()->fresh();

        $response = $this->json('PUT', '/api/projeto/' . $projeto->id, ['data_criacao' => '2019-10-09 06:35:41']);

        $response->assertStatus(422);

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
    public function falhar_tentar_atualizar_campo_id()
    {
        $projeto = Projeto::factory($this->arrayValoresPadroesCriacaoProjeto())->create()->fresh();

        $response = $this->json('PUT', '/api/projeto/' . $projeto->id, ['id' => $projeto->id + 10]);

        $response->assertStatus(422);

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

        $response->assertStatus(422);

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

        $response->assertJsonFragment(["id_projeto_pai" => ["Projeto pai especificado não existe."]]);

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
            'custo_previsto' => ['Custo previsto do projeto é invalido.']
        ];

        $response->assertStatus(422);

        $assertDatabaseHas = [
            "id" => $projeto->id,
            "nome" => $projeto->nome,
            "descricao" => $projeto->descricao,
            "id_projeto_pai" => $projeto->id_projeto_pai,
            "nivel_projeto" => $projeto->nivel_projeto,
            "custo_previsto" => $projeto->custo_previsto,
            "data_limite" => $projeto->data_limite
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
            'custo_previsto' => ['Custo previsto do projeto é invalido.']
        ];

        $response->assertStatus(422);

        $assertDatabaseHas = [
            "id" => $projeto->id,
            "nome" => $projeto->nome,
            "descricao" => $projeto->descricao,
            "id_projeto_pai" => $projeto->id_projeto_pai,
            "nivel_projeto" => $projeto->nivel_projeto,
            "custo_previsto" => $projeto->custo_previsto,
            "data_limite" => $projeto->data_limite
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


    public function test_falhar_tentar_atualizar_projeto_meses_previsto_negativo()
    {
        $this->assertAtualizaCampoUnico(
            ["meses_previstos" => -1],
            422,
            ["meses_previstos" => ["Valor do campo meses deve ser maior ou igual a zero."]]
        );
    }
    public function test_falhar_tentar_atualizar_projeto_dias_previsto_negativo()
    {
        $this->assertAtualizaCampoUnico(
            ["dias_previstos" => -1],
            422,
            ["dias_previstos" => ["Valor do campo dias deve ser maior ou ou igual a zero."]]
        );
    }
    public function test_falhar_tentar_atualizar_projeto_horas_previstas_negativa()
    {
        $this->assertAtualizaCampoUnico(
            ["horas_previstas" => -1],
            422,
            ["horas_previstas" => ["Valor do campo horas deve ser maior ou igual zero."]]
        );
    }
    public function test_falhar_tentar_atualizar_projeto_minutos_previsto_negativo()
    {
        $this->assertAtualizaCampoUnico(
            ["minutos_previstos" => -1],
            422,
            ["minutos_previstos" => ["Valor do campo minutos deve ser maior ou igual a zero."]]
        );
    }
    public function test_falhar_tentar_atualizar_projeto_meses_previsto_maior_1200()
    {
        $this->assertAtualizaCampoUnico(
            ["meses_previstos" => 1201],
            422,
            ["meses_previstos" => ["Valor do campo meses deve ser menor ou igual a 1200."]]
        );
    }
    public function test_falhar_tentar_atualizar_projeto_dias_previsto_maior_31()
    {
        $this->assertAtualizaCampoUnico(
            ["dias_previstos" => 32],
            422,
            ["dias_previstos" => ["Valor do campo dias deve ser menor ou igual a 31."]]
        );
    }
    public function test_falhar_tentar_atualizar_projeto_horas_previstas_maior_23()
    {
        $this->assertAtualizaCampoUnico(
            ["horas_previstas" => 24],
            422,
            ["horas_previstas" => ["Valor do campo horas deve ser menor ou igual a 23."]]
        );
    }
    public function test_falhar_tentar_atualizar_projeto_minutos_previsto_maior_59()
    {
        $this->assertAtualizaCampoUnico(
            ["minutos_previstos" => 60],
            422,
            ["minutos_previstos" => ["Valor do campo minutos deve ser menor ou igual a 59."]]
        );
    }


    // public function test_falhar_tentar_atualizar_um_projeto_mesmo_nome_projeto_irmao()
    // {
    //     $nomeUnico = "ProjetoX";
    //     $projeto = Projeto::factory($this->arrayValoresPadroesCriacaoProjeto(["nome" => $nomeUnico]))->create()->fresh();
    //     $this->assertAtualizaCampoUnico(
    //         ["nome" => $nomeUnico],
    //         422,
    //         ["nome" => ["já existe um projeto com este nome neste nível"]]
    //     );
    // }
    public function arrayValoresPadroesCriacaoProjeto(array $alteracoes = [])
    {
        $padrao = [
            "nome" => "nome antigo",
            "descricao" => "descricao antiga",
            "id_projeto_pai" => 0,
            "nivel_projeto" => 0,
            "custo_previsto" => 100,
            "data_limite" => (new \DateTime())->add(new \DateInterval("P30D"))->format('Y-m-d H:i:s'),
            'meses_previstos' => 1,
            'dias_previstos' => 1,
            'horas_previstas' => 1,
            'minutos_previstos' => 1
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

    public function assertAtualizaCampoUnico(array $campo, $statusCode = 200, $assertJsonParamentro = null, $projeto = null)
    {

        if (COUNT($campo) > 1) throw new Exception("assertAtualizaCampoUnico");

        $chaveArray = key($campo);
        $valorArray = $campo[$chaveArray];

        if (!$projeto) {
            $projeto = Projeto::factory($this->arrayValoresPadroesCriacaoProjeto())->create()->fresh();
        }

        $response = $this->json('PUT', '/api/projeto/' . $projeto->id, $campo);

        $response->assertStatus($statusCode);

        $arrayControle = [
            "id" => $projeto->id,
            "nome" => $projeto->nome,
            "descricao" => $projeto->descricao,
            "id_projeto_pai" => $projeto->id_projeto_pai,
            "nivel_projeto" => $projeto->nivel_projeto,
            "custo_previsto" => $projeto->custo_previsto,
            "data_limite" => (new \DateTime())->add(new \DateInterval("P30D"))->format('Y-m-d H:i:s'),
            'meses_previstos' => 1,
            'dias_previstos' => 1,
            'horas_previstas' => 1,
            'minutos_previstos' => 1
            // "local_de_realizacao_previsto": null,
        ];
        $assertJson = array_merge(
            $arrayControle,
            ["filhos" => []]
        );

        $assertDatabaseHas = $arrayControle;

        if (!empty($assertJsonParamentro)) {
            $response->assertJsonFragment($assertJsonParamentro);
        } else {
            $assertJson[$chaveArray] = $valorArray;
            $assertDatabaseHas[$chaveArray] = $valorArray;
            $response->assertJson($assertJson);
        }

        $this->assertDatabaseHas('projetos', $assertDatabaseHas);
    }
}