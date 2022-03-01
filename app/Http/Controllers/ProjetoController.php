<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjetoStoreRequest;
use App\Http\Requests\ProjetoUpdateRequest;
use App\Models\Projeto;
use App\Http\Resources\ProjetoCollection;
use App\Http\Resources\ProjetoResource;

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

        if ($queryBuscaProjetos->count() == 0) {
            return Response()->json([], 204);
        }
        return new ProjetoCollection($queryBuscaProjetos->paginate(100));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->index();
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
        if (empty($projeto)) {
            return Response()->json([], 404);
        }
        return (new ProjetoResource($projeto));
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     return Response("testes edit",201);
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjetoUpdateRequest $request, $id)
    {

        $projeto = Projeto::find($id);
        $houveAtualizacao = false;

        if (is_null($projeto)) {
            return Response()->json([], 404);
        }

        // if (isset($request->data_criacao) || isset($request->id)) {
        //     return Response()->json([], 400);
        // }

        $update = function ($projetoAttr, $requestAttr = null) use ($projeto, $request, &$houveAtualizacao) {
            if (is_null($requestAttr)) $requestAttr = $projetoAttr;
            if (isset($request->{$requestAttr})) {
                $projeto->{$projetoAttr} = $request->{$requestAttr};
                $houveAtualizacao = true;
            }
        };

        foreach ((new Projeto())->getFillable() as $atributo) {
            $update($atributo);
        }

        if ($houveAtualizacao) {
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
        $projeto = Projeto::find($id);
        if (!$projeto) {
            return Response()->json([], 404);
        }
        if ($projeto->filhos->count() > 0) {
            return Response()->json(['error' => ['Este projeto não pode ser apagado, este possui subprojetos']], 400);
        }

        Projeto::destroy($id);
        return Response()->json([], 200);
    }
}
