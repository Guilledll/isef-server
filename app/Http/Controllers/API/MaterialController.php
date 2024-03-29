<?php

namespace App\Http\Controllers\API;

use App\Models\Material;
use App\Models\Inventario;
use App\Http\Controllers\Controller;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\InventarioResource;
use App\Http\Requests\Material\StoreMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialesRequest;
use App\Http\Requests\MoverMaterialRequest;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return MaterialResource::collection(
      Material::where('cantidad', '>', 0)
        ->with('deposito', 'categoria')
        ->orderBy('nombre', 'asc')
        ->get()
    );
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreMaterialRequest $request)
  {
    $inventarios = array();

    foreach ($request->materiales as $data) {

      $material = Material::updateOrCreate(
        [
          'nombre' => $data['nombre'],
          'deposito_id' => $data['deposito_id'],
          'categoria_id' => $data['categoria_id']
        ],
        ['cantidad' => DB::raw('cantidad + ' . $data['cantidad'])]
      );

      array_push($inventarios, [
        'material_id' => $material->id,
        'user_ci' => $request->usuario_ci,
        'deposito_id' => $data['deposito_id'],
        'cantidad' => $data['cantidad'],
        'accion' => 1,
        'nota' => $data['nota'],
        'fecha' => now(),
      ]);
    }

    DB::table('inventarios')->insert($inventarios);

    return response()->json(['message' => 'Materiales agregados con éxito']);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Material  $deposito
   * @return \Illuminate\Http\Response
   */
  public function show(Material $material)
  {
    return new MaterialResource($material);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Material  $material
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateMaterialesRequest $request, Material $material)
  {
    // Movimiento en inventario
    if ($material->cantidad != $request->cantidad) {

      // Si se están agregando o eliminando 
      $accion = ($request->cantidad > $material->cantidad) ? 1 : 0;

      // Registro el movimiento de inventario
      Inventario::create([
        'material_id' => $material->id,
        'user_ci' => $request->usuario_ci,
        'cantidad' => abs($material->cantidad - $request->cantidad),
        'accion' => $accion,
        'deposito_id' => $material->deposito_id,
        'nota' => $request->nota,
        'fecha' => now(),
      ]);
    }

    // Actualiza material
    $material->update([
      'nombre' => $request->nombre,
      'cantidad' => $request->cantidad,
    ]);

    return new MaterialResource($material);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Material  $material
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    Material::findOrFail($id)->delete();

    return response()->json(['message' => 'Material eliminado con éxito!'], 200);
  }

  /**
   * Devuelve los movimientos de ese material
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function movimientos($id)
  {
    $movimientos = Inventario::where('material_id', $id)
      ->with(['deposito', 'material'])
      ->orderBy('fecha', 'asc')
      ->get();

    return InventarioResource::collection($movimientos);
  }

  /**
   * Mueve el material de depósito
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function mover(MoverMaterialRequest $request, Material $material)
  {
    $cantidadInicial = $material->cantidad;
    $arrayDeMateriales = array();

    $nuevoMaterial = Material::updateOrCreate(
      [
        'nombre' => $material->nombre,
        'deposito_id' => $request->deposito_destino_id,
        'categoria_id' => $material->categoria_id
      ],
      ['cantidad' => DB::raw('cantidad + ' . $request->cantidad)]
    );

    // Movimiento de alta en el nuevo deposito
    array_push($arrayDeMateriales, [
      'material_id' => $nuevoMaterial->id,
      'user_ci' => $request->usuario_ci,
      'deposito_id' => $nuevoMaterial->deposito_id,
      'cantidad' => $request->cantidad,
      'accion' => 1,
      'nota' => $request->nota,
      'fecha' => now(),
    ]);

    // Actualizo la cantidad del existente
    $material->update([
      'cantidad' => abs($cantidadInicial - $request->cantidad),
    ]);

    // Movimiento de baja en el viejo deposito
    array_push($arrayDeMateriales, [
      'material_id' => $material->id,
      'user_ci' => $request->usuario_ci,
      'deposito_id' => $material->deposito_id,
      'cantidad' => $request->cantidad,
      'accion' => 0,
      'nota' => $request->nota,
      'fecha' => now(),
    ]);

    DB::table('inventarios')->insert($arrayDeMateriales);

    return MaterialResource::collection(
      Material::whereIn('id', [$material->id, $nuevoMaterial->id])
        ->with('categoria', 'deposito')
        ->get()
    );
  }
}
