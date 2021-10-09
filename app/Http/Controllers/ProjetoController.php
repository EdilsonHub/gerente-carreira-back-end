<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjetoStoreRequest;
use App\Models\Projeto;
use App\Rules\CheckProjetoMesmoNomeRule;
use http\Env\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProjetoCollection;
use App\Http\Resources\ProjetoResource;
use Facade\FlareClient\Http\Response as HttpResponse;

class ProjetoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $queryBuscaProjetos = Projeto::where('id_projeto_pai', null);

        if($queryBuscaProjetos->count() == 0) {
            return Response()->json([], 204);
        }
        return new ProjetoCollection($queryBuscaProjetos->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ProjetoStoreRequest $request)
    {
        $projeto = Projeto::create($request->all());
        $projetoBuscado = Projeto::find($projeto->id);
        $projetoResource = new ProjetoResource($projetoBuscado);

        return Response()->json($projetoResource, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $projeto = Projeto::find($id);
        if(empty($projeto)) {
            return Response()->json([], 404);
        }
        return (new ProjetoResource($projeto));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Response("testes edit",201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $projeto = Projeto::find($id);
        $houveAtualizacao = false;

        if(is_null($projeto)) {
            return Response()->json([], 404);
        }

        if(isset($request->custo_previsto)) {
            if($request->custo_previsto < 0) {
                return Response()->json(['custo_previsto' => ['Não pode ser atuazlizado com valor menor que zero.']], 422);
            }
            if(!is_numeric($request->custo_previsto)) {
                return Response()->json(['custo_previsto' => ['valor precisa ser um número.']], 422);
            }
        }

        if(isset($request->id_projeto_pai)) {
            if(!Projeto::find($request->id_projeto_pai)) {
                return Response()->json(["id_projeto_pai" => ["Não foi encontrado o projeto de id ".$request->id_projeto_pai."."]], 400);;
            }
        }

        if(isset($request->data_criacao) || isset($request->id)) {
            return Response()->json([], 400);
        }

        $update = function ($projetoAttr, $requestAttr = null) use ($projeto, $request, &$houveAtualizacao) {
            if(is_null($requestAttr)) $requestAttr = $projetoAttr;
            if(isset($request->{$requestAttr})) {
                $projeto->{$projetoAttr} = $request->{$requestAttr};
                $houveAtualizacao = true;
            }
        };

        foreach(['nome','descricao','id_projeto_pai','nivel_projeto','custo_previsto'] as $atributo) {
            $update($atributo);
        }

        if($houveAtualizacao) {
            $projeto->save();
        }

        return new ProjetoResource($projeto);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Response("testes",201);
    }
}
