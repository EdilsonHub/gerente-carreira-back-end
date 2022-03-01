<?php

namespace App\Http\Controllers;

use App\Http\Resources\AgendaResource;
use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $agenda = Agenda::create($request->all());
        $agendaBuscada = Agenda::find($agenda->id);
        $agendaResource = new AgendaResource($agendaBuscada);

        return Response()->json($agendaResource, 201);
    }
}