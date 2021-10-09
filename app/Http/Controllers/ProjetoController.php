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
        return Response("testes update",201);
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
