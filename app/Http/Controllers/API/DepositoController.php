<?php

namespace App\Http\Controllers\API;

use App\Models\Deposito;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepositoRequest;
use App\Http\Resources\DepositoResource;

class DepositoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DepositoResource::collection(Deposito::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDepositoRequest $request)
    {

        $deposito = new Deposito();
        $deposito->nombre = $request->nombre;
        $deposito->departamento_id = $request->departamento_id;
        $deposito->save();

        return response()->json(['message' => 'Depósito registrado con éxito!'], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Deposito  $deposito
     * @return \Illuminate\Http\Response
     */
    public function show(Deposito $deposito)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Departamento  $deposito
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deposito $deposito)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Departamento  $departamento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposito $deposito)
    {
        //
    }
}